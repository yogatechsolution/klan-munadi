<?php

namespace Tests\Feature;

use App\Models\FamilyMember;
use App\Models\Gallery;
use App\Models\Photo;
use App\Models\SiteSetting;
use App\Services\MediaStorageService;
use Database\Seeders\MunadiFamilySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FamilyTreeTest extends TestCase
{
    use RefreshDatabase;

    private function asAdmin(): self
    {
        return $this->withSession(['is_admin' => true, 'admin_username' => 'admin']);
    }

    public function test_public_pages_load_without_login_and_protect_admin_routes(): void
    {
        $this->seed(MunadiFamilySeeder::class);

        // Halaman publik bebas diakses keluarga tanpa login
        $this->get('/')->assertStatus(200)->assertSee('KASAN MUNADI');
        $this->get('/keluarga')->assertStatus(200)->assertSee('Kasan Munadi')->assertSee('Sutirah');
        $this->get('/persebaran-keluarga')->assertStatus(200)->assertSee('Persebaran Bani Munadi');
        $this->get('/galeri')->assertStatus(200)->assertSee('Galeri Keluarga');
        $this->get('/our-story')->assertStatus(200)->assertSee('Kasan Munadi');
        $this->get('/jadwal')->assertStatus(200)->assertSee('Jadwal Acara');
        $this->get('/kontak')->assertStatus(200)->assertSee('Kontak Pengurus');
        $this->get('/admin/login')->assertStatus(200)->assertSee('Masuk Mode Admin');

        // Rute admin harus mengarahkan tamu ke halaman login admin
        $this->get('/keluarga/tambah')->assertRedirect(route('admin.login'));
        $this->get('/keluarga/settings')->assertRedirect(route('admin.login'));
        $this->post('/keluarga/simpan', ['name' => 'Tamu'])->assertRedirect(route('admin.login'));
        $this->post('/galeri/folder', ['name' => 'Album Tamu'])->assertRedirect(route('admin.login'));
    }

    public function test_admin_login_and_logout_flow(): void
    {
        config(['admin.username' => 'admin', 'admin.password' => 'rahasia123']);

        // Password salah ditolak
        $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'salah',
        ])->assertSessionHasErrors('username');

        // Password benar masuk Mode Admin
        $this->post('/admin/login', [
            'username' => 'admin',
            'password' => 'rahasia123',
        ])->assertRedirect(route('family.index'));

        $this->get('/keluarga/tambah')->assertStatus(200)->assertSee('Tambah Anggota Baru');
        $this->get('/keluarga/settings')->assertStatus(200)->assertSee('Pengaturan Website');

        // Logout mengunci kembali rute admin
        $this->post('/admin/logout')->assertRedirect(route('home'));
        $this->get('/keluarga/tambah')->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_create_update_and_delete_family_member_with_newlines_and_location(): void
    {
        Storage::fake('public');

        $father = FamilyMember::create(['name' => 'Ayah Uji']);

        $storeRes = $this->asAdmin()->post('/keluarga/simpan', [
            'name' => 'Anak Uji',
            'birth_date' => '1995-08-17',
            'spouse_name' => 'Pasangan Uji',
            'father_id' => $father->id,
            'bio' => "Baris pertama\nBaris kedua dengan 'kutip' dan \"ganda\"",
            'address' => "Jl. Mawar No. 5\nMalang",
            'latitude' => '-7.96662040',
            'longitude' => '112.63263210',
        ]);

        $storeRes->assertRedirect(route('family.index', $father->id));

        $child = FamilyMember::where('name', 'Anak Uji')->firstOrFail();
        $this->assertEquals($father->id, $child->father_id);

        // Halaman publik tetap merender aman tanpa broken link /family/
        $this->get(route('family.index', $father->id))
            ->assertStatus(200)
            ->assertSee('Anak Uji');

        $this->get('/persebaran-keluarga')
            ->assertStatus(200)
            ->assertSee('Anak Uji')
            ->assertDontSee('/family/' . $child->id);

        $this->get('/jadwal')
            ->assertStatus(200)
            ->assertSee('Anak Uji');

        // Pencegahan siklus: ayah tidak boleh memilih anaknya sendiri sebagai ayahnya
        $this->asAdmin()->put(route('family.update', $father->id), [
            'name' => 'Ayah Uji',
            'father_id' => $child->id,
        ])->assertSessionHasErrors('father_id');

        // Update normal
        $this->asAdmin()->put(route('family.update', $child->id), [
            'name' => 'Anak Uji Updated',
            'father_id' => $father->id,
        ])->assertRedirect(route('family.index', $child->id));

        $this->assertDatabaseHas('family_members', ['id' => $child->id, 'name' => 'Anak Uji Updated']);

        // Delete
        $this->asAdmin()->delete(route('family.destroy', $child->id))
            ->assertRedirect(route('family.index', $father->id));

        $this->assertDatabaseMissing('family_members', ['id' => $child->id]);
    }

    public function test_admin_can_update_story_settings_and_gallery(): void
    {
        Storage::fake('public');

        $member = FamilyMember::create(['name' => 'Tokoh Keluarga']);

        $this->asAdmin()->post(route('story.update', $member->id), [
            'story' => 'Kisah perjalanan panjang penuh keteladanan.',
        ])->assertRedirect(route('story', ['search_id' => $member->id]));

        $this->get(route('story', ['search_id' => $member->id]))
            ->assertStatus(200)
            ->assertSee('Kisah perjalanan panjang penuh keteladanan.');

        $this->asAdmin()->post(route('settings.contact.update'), [
            'contact_name' => 'Pak Yoga',
            'contact_phone' => '081234567890',
            'contact_email' => 'keluarga@munadi.test',
            'contact_address' => 'Malang, Jawa Timur',
            'contact_note' => 'Hubungi via WA.',
        ])->assertRedirect();

        $this->assertEquals('Pak Yoga', SiteSetting::getValue('contact_name'));

        $this->get('/kontak')
            ->assertStatus(200)
            ->assertSee('Pak Yoga')
            ->assertSee('https://wa.me/6281234567890');

        $this->asAdmin()->post(route('gallery.storeFolder'), [
            'name' => 'Reuni Lebaran 2026',
            'description' => 'Foto kumpul keluarga besar.',
        ])->assertRedirect();

        $gallery = Gallery::where('name', 'Reuni Lebaran 2026')->firstOrFail();

        $this->asAdmin()->post(route('gallery.storePhoto', $gallery->id), [
            'images' => [
                UploadedFile::fake()->create('foto1.jpg', 100, 'image/jpeg'),
                UploadedFile::fake()->create('foto2.png', 100, 'image/png'),
            ],
        ])->assertRedirect();

        $this->assertEquals(2, Photo::where('gallery_id', $gallery->id)->count());

        $this->get(route('gallery.show', $gallery->id))
            ->assertStatus(200)
            ->assertSee('Reuni Lebaran 2026');

        $this->asAdmin()->delete(route('gallery.destroyFolder', $gallery->id))
            ->assertRedirect(route('gallery.index'));

        $this->assertDatabaseMissing('galleries', ['id' => $gallery->id]);
    }

    public function test_initial_seed_command_is_idempotent(): void
    {
        Artisan::call('family:seed-initial');
        $firstCount = FamilyMember::count();
        $this->assertGreaterThanOrEqual(40, $firstCount);

        // Jalankan kedua kalinya — jumlah tidak boleh bertambah (anti-duplikat)
        Artisan::call('family:seed-initial');
        $this->assertEquals($firstCount, FamilyMember::count());
    }

    public function test_cloudinary_storage_service_uploads_when_configured(): void
    {
        config([
            'cloudinary.url' => 'cloudinary://testkey:testsecret@demo-cloud',
            'cloudinary.folder' => 'klan-munadi',
        ]);

        Http::fake([
            'https://api.cloudinary.com/v1_1/demo-cloud/image/upload' => Http::response([
                'secure_url' => 'https://res.cloudinary.com/demo-cloud/image/upload/v123/klan-munadi/gallery/reuni.jpg',
            ], 200),
        ]);

        $service = app(MediaStorageService::class);
        $this->assertTrue($service->isCloudinaryEnabled());

        $file = UploadedFile::fake()->createWithContent('reuni.jpg', 'fake-jpeg-binary-content');
        $url = $service->storeImage($file, 'gallery');

        $this->assertEquals('https://res.cloudinary.com/demo-cloud/image/upload/v123/klan-munadi/gallery/reuni.jpg', $url);

        $photo = new Photo(['image' => $url]);
        $this->assertEquals($url, $photo->url);
    }
}
