<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use App\Models\Gallery;
use App\Models\Photo;
use App\Models\SiteSetting;
use App\Services\MediaStorageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Throwable;

class FamilyMemberController extends Controller
{
    public function __construct(private readonly MediaStorageService $mediaStorage)
    {
    }
    private const IMAGE_SETTING_KEYS = [
        'hero_image',
        'roots_image',
        'legacy_image',
        'tree_bg_image',
    ];

    /**
     * Halaman Beranda utama.
     */
    public function home()
    {
        $settings = SiteSetting::getAllMap();

        $heroImage = $this->resolveSettingImage($settings['hero_image'] ?? null, 'assets/images/Pemandangan-alam.jpg');
        $rootsImage = $this->resolveSettingImage($settings['roots_image'] ?? null, 'assets/images/Gambar1.jpeg');
        $legacyImage = $this->resolveSettingImage($settings['legacy_image'] ?? null, 'assets/images/Bg.jpeg');

        $stats = [
            'members' => 0,
            'generations' => 0,
            'mapped' => 0,
            'galleries' => 0,
            'photos' => 0,
            'stories' => 0,
        ];

        try {
            $allMembers = FamilyMember::all();
            $stats['members'] = $allMembers->count();
            $stats['mapped'] = $allMembers->filter(fn ($m) => $m->latitude !== null && $m->longitude !== null)->count();
            $stats['stories'] = $allMembers->filter(fn ($m) => !empty(trim((string) $m->story)))->count();

            if ($allMembers->isNotEmpty()) {
                $generations = $this->computeGenerations($allMembers);
                $stats['generations'] = max($generations) ?: 1;
            }

            $stats['galleries'] = Gallery::count();
            $stats['photos'] = Photo::count();
        } catch (Throwable $e) {
            // Tetap tampilkan beranda meskipun tabel belum dimigrasi
        }

        return view('home', compact('heroImage', 'rootsImage', 'legacyImage', 'stats'));
    }

    /**
     * Halaman Pohon Silsilah interaktif.
     */
    public function index($id = null)
    {
        $allMembers = FamilyMember::orderBy('id')->get();
        $membersById = $allMembers->keyBy('id');

        $childrenMap = $this->buildChildrenMap($allMembers);
        $generations = $this->computeGenerations($allMembers);
        $inferredSpouses = $this->inferSpouses($allMembers, $membersById);

        $rootMember = $this->findRootMember($allMembers, $childrenMap);

        if ($id !== null) {
            $currentCore = $membersById->get((int) $id) ?? $rootMember;
        } else {
            $currentCore = $rootMember;
        }

        // Bangun breadcrumb silsilah dari leluhur ke anggota saat ini
        $breadcrumbs = [];
        if ($currentCore) {
            $cursor = $currentCore;
            $visited = [];
            while ($cursor && !isset($visited[$cursor->id])) {
                $visited[$cursor->id] = true;
                array_unshift($breadcrumbs, $cursor);
                $parentId = $cursor->father_id ?? $cursor->mother_id;
                $cursor = $parentId ? $membersById->get($parentId) : null;
            }
        }

        // Hitung jumlah anak langsung & total keturunan per anggota
        $descendantCounts = [];
        foreach ($allMembers as $member) {
            $descendantCounts[$member->id] = $this->countTotalDescendants($member->id, $childrenMap);
        }

        $directChildren = $currentCore
            ? ($childrenMap[$currentCore->id] ?? collect())
            : collect();

        // Data JSON yang aman & lengkap untuk pencarian instan & modal detail
        $membersPayload = [];
        foreach ($allMembers as $m) {
            $father = $m->father_id ? $membersById->get($m->father_id) : null;
            $mother = $m->mother_id ? $membersById->get($m->mother_id) : null;
            $kids = $childrenMap[$m->id] ?? collect();
            $effectiveSpouse = trim((string) $m->spouse_name) !== ''
                ? $m->spouse_name
                : ($inferredSpouses[$m->id]['name'] ?? null);
            $inferredSpouseId = $inferredSpouses[$m->id]['id'] ?? null;

            $membersPayload[$m->id] = [
                'id' => $m->id,
                'name' => $m->name,
                'avatar' => $m->avatar_url,
                'generation' => $generations[$m->id] ?? 1,
                'birth_date' => $m->birth_date,
                'formatted_birth_date' => $m->formatted_birth_date,
                'spouse_name' => $effectiveSpouse,
                'spouse_id' => $inferredSpouseId,
                'father' => $father ? ['id' => $father->id, 'name' => $father->name] : null,
                'mother' => $mother ? ['id' => $mother->id, 'name' => $mother->name] : null,
                'children' => $kids->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'tree_url' => route('family.index', $c->id),
                ])->values()->all(),
                'children_count' => $kids->count(),
                'descendants_count' => $descendantCounts[$m->id] ?? 0,
                'address' => $m->address,
                'latitude' => $m->latitude,
                'longitude' => $m->longitude,
                'bio' => $m->bio,
                'has_story' => !empty(trim((string) $m->story)),
                'edit_url' => route('family.edit', $m->id),
                'tree_url' => route('family.index', $m->id),
                'story_url' => route('story', ['search_id' => $m->id]),
                'add_child_father_url' => route('family.create', ['father_id' => $m->id]),
                'add_child_mother_url' => route('family.create', ['mother_id' => $m->id]),
            ];
        }

        $treeBgPath = SiteSetting::getValue('tree_bg_image');
        $treeBgImage = $this->resolveSettingImage($treeBgPath, 'assets/images/Gambar1.jpeg');

        return view('family.index', compact(
            'allMembers',
            'currentCore',
            'rootMember',
            'directChildren',
            'breadcrumbs',
            'generations',
            'descendantCounts',
            'childrenMap',
            'inferredSpouses',
            'membersPayload',
            'treeBgImage'
        ));
    }

    /**
     * Form tambah anggota keluarga baru.
     */
    public function create()
    {
        $members = FamilyMember::orderBy('name', 'asc')->get();
        return view('family.create', compact('members'));
    }

    /**
     * Simpan anggota keluarga baru.
     */
    public function store(Request $request)
    {
        $validated = $this->validateMember($request);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $this->mediaStorage->storeImage($request->file('photo'), 'avatars');
        }

        $member = FamilyMember::create($validated);

        $redirectId = $member->father_id ?? $member->mother_id ?? $member->id;

        return redirect()
            ->route('family.index', $redirectId)
            ->with('success', 'Anggota keluarga "' . $member->name . '" berhasil ditambahkan!');
    }

    /**
     * Form edit anggota keluarga.
     */
    public function edit($id)
    {
        $member = FamilyMember::findOrFail($id);
        $allMembers = FamilyMember::orderBy('name', 'asc')->get();
        $childrenMap = $this->buildChildrenMap($allMembers);

        // Cegah memilih diri sendiri atau keturunannya sebagai orang tua (anti-siklus)
        $excludedIds = $this->collectDescendantIds((int) $member->id, $childrenMap);
        $excludedIds[] = (int) $member->id;

        $members = $allMembers->reject(fn ($m) => in_array($m->id, $excludedIds, true))->values();

        return view('family.edit', compact('member', 'members'));
    }

    /**
     * Update data anggota keluarga.
     */
    public function update(Request $request, $id)
    {
        $member = FamilyMember::findOrFail($id);
        $validated = $this->validateMember($request, (int) $member->id);

        // Validasi anti-siklus: ayah/ibu tidak boleh diri sendiri atau keturunannya
        $allMembers = FamilyMember::all();
        $childrenMap = $this->buildChildrenMap($allMembers);
        $descendantIds = $this->collectDescendantIds((int) $member->id, $childrenMap);
        $invalidParentIds = array_merge([(int) $member->id], $descendantIds);

        if (!empty($validated['father_id']) && in_array((int) $validated['father_id'], $invalidParentIds, true)) {
            return back()->withInput()->withErrors(['father_id' => 'Ayah tidak boleh diri sendiri atau keturunannya sendiri.']);
        }
        if (!empty($validated['mother_id']) && in_array((int) $validated['mother_id'], $invalidParentIds, true)) {
            return back()->withInput()->withErrors(['mother_id' => 'Ibu tidak boleh diri sendiri atau keturunannya sendiri.']);
        }

        if ($request->boolean('remove_photo') && $member->photo) {
            $this->deleteStoredPhoto($member->photo);
            $validated['photo'] = null;
        }

        if ($request->hasFile('photo')) {
            if ($member->photo) {
                $this->deleteStoredPhoto($member->photo);
            }
            $validated['photo'] = $this->mediaStorage->storeImage($request->file('photo'), 'avatars');
        }

        $member->update($validated);

        return redirect()
            ->route('family.index', $member->id)
            ->with('success', 'Data "' . $member->name . '" berhasil diperbarui!');
    }

    /**
     * Hapus anggota keluarga.
     */
    public function destroy($id)
    {
        $member = FamilyMember::findOrFail($id);
        $name = $member->name;
        $parentId = $member->father_id ?? $member->mother_id;

        if ($member->photo) {
            $this->deleteStoredPhoto($member->photo);
        }

        $member->delete();

        return redirect()
            ->route('family.index', $parentId)
            ->with('success', 'Data "' . $name . '" telah dihapus dari silsilah.');
    }

    /**
     * Halaman Pengaturan Website (Foto & Kontak).
     */
    public function settings()
    {
        $settings = SiteSetting::getAllMap();

        $previews = [
            'hero_image' => $this->resolveSettingImage($settings['hero_image'] ?? null, 'assets/images/Pemandangan-alam.jpg'),
            'roots_image' => $this->resolveSettingImage($settings['roots_image'] ?? null, 'assets/images/Gambar1.jpeg'),
            'legacy_image' => $this->resolveSettingImage($settings['legacy_image'] ?? null, 'assets/images/Bg.jpeg'),
            'tree_bg_image' => $this->resolveSettingImage($settings['tree_bg_image'] ?? null, 'assets/images/Gambar1.jpeg'),
        ];

        $customFlags = [
            'hero_image' => !empty($settings['hero_image']),
            'roots_image' => !empty($settings['roots_image']),
            'legacy_image' => !empty($settings['legacy_image']),
            'tree_bg_image' => !empty($settings['tree_bg_image']),
        ];

        $contact = $this->getContactData($settings);

        return view('family.settings', compact('previews', 'customFlags', 'contact'));
    }

    /**
     * Simpan perubahan gambar pengaturan website.
     */
    public function updateSetting(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
            'key' => 'required|string|in:' . implode(',', self::IMAGE_SETTING_KEYS),
        ], [
            'image.required' => 'Pilih file gambar terlebih dahulu.',
            'image.image' => 'File harus berupa gambar yang valid.',
            'image.max' => 'Ukuran gambar maksimal 4MB.',
        ]);

        if ($request->hasFile('image')) {
            $oldSetting = SiteSetting::where('key', $request->key)->first();
            $storedPath = $this->mediaStorage->storeImage(
                $request->file('image'),
                'settings',
                true,
                $request->key
            );

            SiteSetting::updateOrCreate(
                ['key' => $request->key],
                ['value' => $storedPath]
            );

            if ($oldSetting && $oldSetting->value && str_starts_with($oldSetting->value, 'http')) {
                $this->mediaStorage->deleteImage($oldSetting->value);
            }
        }

        $labels = [
            'hero_image' => 'Hero Beranda',
            'roots_image' => 'Our Roots',
            'legacy_image' => 'The Legacy',
            'tree_bg_image' => 'Latar Pohon Silsilah',
        ];

        return back()->with('success', 'Gambar ' . ($labels[$request->key] ?? $request->key) . ' berhasil diperbarui!');
    }

    /**
     * Reset salah satu gambar pengaturan ke gambar bawaan.
     */
    public function resetSetting(string $key)
    {
        if (!in_array($key, self::IMAGE_SETTING_KEYS, true)) {
            abort(404);
        }

        SiteSetting::where('key', $key)->delete();

        return back()->with('success', 'Gambar berhasil dikembalikan ke tampilan bawaan.');
    }

    /**
     * Simpan perubahan informasi kontak pengurus keluarga.
     */
    public function updateContactSettings(Request $request)
    {
        $validated = $request->validate([
            'contact_name' => 'nullable|string|max:120',
            'contact_phone' => 'nullable|string|max:40',
            'contact_email' => 'nullable|email|max:120',
            'contact_address' => 'nullable|string|max:250',
            'contact_note' => 'nullable|string|max:250',
        ]);

        foreach ($validated as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => trim((string) $value)]
            );
        }

        return back()->with('success', 'Informasi kontak pengurus keluarga berhasil diperbarui!');
    }

    /**
     * Halaman Peta Persebaran Keluarga.
     */
    public function map()
    {
        $allMembers = FamilyMember::orderBy('name', 'asc')->get();
        $totalMembers = $allMembers->count();

        $members = $allMembers->filter(fn ($m) => $m->latitude !== null && $m->longitude !== null)->values();

        $mapMembers = $members->map(fn ($m) => [
            'id' => $m->id,
            'name' => $m->name,
            'spouse_name' => $m->spouse_name,
            'address' => $m->address,
            'latitude' => (float) $m->latitude,
            'longitude' => (float) $m->longitude,
            'avatar' => $m->avatar_url,
            'tree_url' => route('family.index', $m->id),
            'edit_url' => route('family.edit', $m->id),
        ])->values();

        return view('family.map', compact('members', 'mapMembers', 'totalMembers'));
    }

    /**
     * Halaman Our Story (Kisah & Biografi Anggota Keluarga).
     */
    public function story(Request $request)
    {
        $members = FamilyMember::orderBy('name', 'asc')->get();
        $allById = $members->keyBy('id');
        $childrenMap = $this->buildChildrenMap($members);
        $generations = $this->computeGenerations($members);
        $inferredSpouses = $this->inferSpouses($members, $allById);

        $selectedMember = null;
        if ($request->filled('search_id')) {
            $selectedMember = $allById->get((int) $request->search_id);
        }

        if (!$selectedMember) {
            $selectedMember = $this->findRootMember($members, $childrenMap);
        }

        $membersWithStory = $members
            ->filter(fn ($m) => !empty(trim((string) $m->story)))
            ->values();

        $selectedGeneration = $selectedMember ? ($generations[$selectedMember->id] ?? 1) : 1;
        $selectedChildrenCount = $selectedMember ? (($childrenMap[$selectedMember->id] ?? collect())->count()) : 0;
        $selectedSpouse = $selectedMember
            ? (trim((string) $selectedMember->spouse_name) !== ''
                ? $selectedMember->spouse_name
                : ($inferredSpouses[$selectedMember->id]['name'] ?? null))
            : null;

        return view('story.index', compact(
            'members',
            'selectedMember',
            'membersWithStory',
            'selectedGeneration',
            'selectedChildrenCount',
            'selectedSpouse'
        ));
    }

    /**
     * Update kisah/biografi anggota keluarga.
     */
    public function updateStory(Request $request, $id)
    {
        $request->validate([
            'story' => 'required|string|max:20000',
        ], [
            'story.required' => 'Isi cerita tidak boleh kosong.',
        ]);

        $member = FamilyMember::findOrFail($id);
        $member->update([
            'story' => trim($request->story),
        ]);

        return redirect()
            ->route('story', ['search_id' => $member->id])
            ->with('success', 'Kisah untuk ' . $member->name . ' berhasil diperbarui!');
    }

    /**
     * Halaman Jadwal Acara & Ulang Tahun Keluarga.
     */
    public function jadwal()
    {
        $upcomingBirthdays = collect();

        try {
            $today = Carbon::today();
            $upcomingBirthdays = FamilyMember::whereNotNull('birth_date')
                ->get()
                ->map(function ($member) use ($today) {
                    try {
                        $birth = Carbon::parse($member->birth_date);
                        $next = Carbon::create($today->year, $birth->month, min($birth->day, Carbon::create($today->year, $birth->month, 1)->daysInMonth));
                        if ($next->lt($today)) {
                            $nextYear = $today->year + 1;
                            $next = Carbon::create($nextYear, $birth->month, min($birth->day, Carbon::create($nextYear, $birth->month, 1)->daysInMonth));
                        }
                        $daysUntil = (int) $today->diffInDays($next, false);
                        $turningAge = $next->year - $birth->year;

                        return (object) [
                            'member' => $member,
                            'next_date' => $next,
                            'days_until' => $daysUntil,
                            'turning_age' => $turningAge > 0 ? $turningAge : null,
                        ];
                    } catch (Throwable $e) {
                        return null;
                    }
                })
                ->filter()
                ->sortBy('days_until')
                ->take(8)
                ->values();
        } catch (Throwable $e) {
            // Aman jika tabel belum tersedia
        }

        return view('jadwal', compact('upcomingBirthdays'));
    }

    /**
     * Halaman Kontak Pengurus Keluarga.
     */
    public function contact()
    {
        $settings = SiteSetting::getAllMap();
        $contact = $this->getContactData($settings);

        return view('contact', compact('contact'));
    }

    // =========================================================================
    // HELPER INTERNAL
    // =========================================================================

    private function validateMember(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'spouse_name' => 'nullable|string|max:255',
            'father_id' => 'nullable|integer|exists:family_members,id',
            'mother_id' => 'nullable|integer|exists:family_members,id',
            'bio' => 'nullable|string|max:5000',
            'address' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
            'photo.image' => 'Foto profil harus berupa file gambar.',
            'photo.max' => 'Ukuran foto profil maksimal 4MB.',
        ]);

        // Normalisasi nilai kosong menjadi null agar database tetap rapi
        foreach (['birth_date', 'spouse_name', 'father_id', 'mother_id', 'bio', 'address', 'latitude', 'longitude'] as $field) {
            if (!isset($validated[$field]) || $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        unset($validated['photo']);

        return $validated;
    }

    private function buildChildrenMap(Collection $members): array
    {
        $map = [];
        foreach ($members as $member) {
            if ($member->father_id) {
                $map[$member->father_id][$member->id] = $member;
            }
            if ($member->mother_id) {
                $map[$member->mother_id][$member->id] = $member;
            }
        }

        foreach ($map as $parentId => $kids) {
            $map[$parentId] = collect($kids)->values();
        }

        return $map;
    }

    private function findRootMember(Collection $members, array $childrenMap): ?FamilyMember
    {
        if ($members->isEmpty()) {
            return null;
        }

        // Prioritaskan Kasan Munadi sebagai leluhur utama bila ada
        $kasan = $members->first(fn ($m) => stripos((string) $m->name, 'Kasan Munadi') !== false);
        if ($kasan) {
            return $kasan;
        }

        // Berikutnya, cari akar tanpa orang tua yang memiliki keturunan terbanyak
        $roots = $members->filter(fn ($m) => $m->father_id === null && $m->mother_id === null);
        if ($roots->isNotEmpty()) {
            return $roots->sortByDesc(fn ($m) => ($childrenMap[$m->id] ?? collect())->count())->first();
        }

        return $members->first();
    }

    private function computeGenerations(Collection $members): array
    {
        $byId = $members->keyBy('id');
        $generations = [];

        $resolve = function (int $id, array $stack = []) use (&$resolve, $byId, &$generations): int {
            if (isset($generations[$id])) {
                return $generations[$id];
            }
            if (isset($stack[$id])) {
                return 1;
            }
            $member = $byId->get($id);
            if (!$member) {
                return 1;
            }

            $stack[$id] = true;
            $parentGens = [];
            if ($member->father_id && $byId->has($member->father_id)) {
                $parentGens[] = $resolve($member->father_id, $stack);
            }
            if ($member->mother_id && $byId->has($member->mother_id)) {
                $parentGens[] = $resolve($member->mother_id, $stack);
            }

            $gen = empty($parentGens) ? 1 : (max($parentGens) + 1);
            $generations[$id] = $gen;
            return $gen;
        };

        foreach ($members as $m) {
            $resolve($m->id);
        }

        // Selaraskan generasi pasangan dari luar garis darah berdasarkan generasi anak
        foreach ($members as $m) {
            if ($m->father_id && $m->mother_id) {
                $fGen = $generations[$m->father_id] ?? 1;
                $mGen = $generations[$m->mother_id] ?? 1;
                $childGen = max($fGen, $mGen) + 1;
                $generations[$m->id] = max($generations[$m->id] ?? 1, $childGen);

                $father = $byId->get($m->father_id);
                $mother = $byId->get($m->mother_id);
                if ($father && !$father->father_id && !$father->mother_id && $mGen > $fGen) {
                    $generations[$father->id] = $mGen;
                }
                if ($mother && !$mother->father_id && !$mother->mother_id && $fGen > $mGen) {
                    $generations[$mother->id] = $fGen;
                }
            }
        }

        return $generations;
    }

    private function inferSpouses(Collection $members, Collection $byId): array
    {
        $spouses = [];
        foreach ($members as $child) {
            if ($child->father_id && $child->mother_id) {
                $father = $byId->get($child->father_id);
                $mother = $byId->get($child->mother_id);
                if ($father && $mother) {
                    $spouses[$father->id] ??= ['id' => $mother->id, 'name' => $mother->name];
                    $spouses[$mother->id] ??= ['id' => $father->id, 'name' => $father->name];
                }
            }
        }
        return $spouses;
    }

    private function countTotalDescendants(int $memberId, array $childrenMap, array $visited = []): int
    {
        if (isset($visited[$memberId])) {
            return 0;
        }
        $visited[$memberId] = true;
        $kids = $childrenMap[$memberId] ?? collect();
        $total = 0;

        foreach ($kids as $kid) {
            if (!isset($visited[$kid->id])) {
                $total += 1 + $this->countTotalDescendants($kid->id, $childrenMap, $visited);
            }
        }

        return $total;
    }

    private function collectDescendantIds(int $memberId, array $childrenMap, array $visited = []): array
    {
        if (isset($visited[$memberId])) {
            return [];
        }
        $visited[$memberId] = true;
        $ids = [];
        foreach ($childrenMap[$memberId] ?? [] as $kid) {
            if (!isset($visited[$kid->id])) {
                $ids[] = (int) $kid->id;
                $ids = array_merge($ids, $this->collectDescendantIds((int) $kid->id, $childrenMap, $visited));
            }
        }
        return array_values(array_unique($ids));
    }

    private function resolveSettingImage(?string $savedPath, string $defaultRelativePath): string
    {
        if (!empty($savedPath)) {
            if (str_starts_with($savedPath, 'http://') || str_starts_with($savedPath, 'https://')) {
                return $savedPath;
            }
            if (file_exists(public_path($savedPath))) {
                return asset($savedPath);
            }
        }

        return asset($defaultRelativePath);
    }

    private function deleteStoredPhoto(string $photoPath): void
    {
        $this->mediaStorage->deleteImage($photoPath);
    }

    private function getContactData(array $settings): array
    {
        return [
            'name' => !empty($settings['contact_name']) ? $settings['contact_name'] : 'Pengurus Trah Bani Kasan Munadi',
            'phone' => !empty($settings['contact_phone']) ? $settings['contact_phone'] : '',
            'email' => !empty($settings['contact_email']) ? $settings['contact_email'] : '',
            'address' => !empty($settings['contact_address']) ? $settings['contact_address'] : 'Rumah Besar Klan Munadi, Jawa',
            'note' => !empty($settings['contact_note']) ? $settings['contact_note'] : 'Hubungi pengurus keluarga untuk pembaruan data silsilah, pengiriman foto dokumentasi, atau informasi agenda silaturahmi.',
        ];
    }
}
