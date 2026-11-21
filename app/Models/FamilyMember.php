<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FamilyMember extends Model
{
    protected $fillable = [
        'name',
        'birth_date',
        'spouse_name',
        'father_id',
        'mother_id',
        'bio',
        'story',
        'photo',
        'address',
        'latitude',
        'longitude',
    ];

    protected $appends = ['avatar_url', 'formatted_birth_date'];

    public function father()
    {
        return $this->belongsTo(FamilyMember::class, 'father_id');
    }

    public function mother()
    {
        return $this->belongsTo(FamilyMember::class, 'mother_id');
    }

    public function fatherChildren()
    {
        return $this->hasMany(FamilyMember::class, 'father_id');
    }

    public function motherChildren()
    {
        return $this->hasMany(FamilyMember::class, 'mother_id');
    }

    /**
     * Query anak-anak baik dari garis ayah maupun ibu (dikelompokkan dengan aman).
     */
    public function scopeChildrenOf($query, int $memberId)
    {
        return $query->where(function ($q) use ($memberId) {
            $q->where('father_id', $memberId)
              ->orWhere('mother_id', $memberId);
        });
    }

    /**
     * Mengambil inisial maksimal 2 huruf dari nama.
     */
    public function getInitialsAttribute(): string
    {
        $clean = trim(preg_replace('/\([^)]*\)/', '', (string) $this->name));
        $parts = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($parts)) {
            return '?';
        }

        if (count($parts) === 1) {
            return mb_strtoupper(mb_substr($parts[0], 0, 2));
        }

        return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1));
    }

    /**
     * Mengembalikan URL foto profil jika ada, atau monogram SVG elegan (aman saat offline).
     */
    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->photo)) {
            if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                return $this->photo;
            }
            if (Storage::disk('public')->exists($this->photo)) {
                return asset('storage/' . $this->photo);
            }
            if (file_exists(public_path($this->photo))) {
                return asset($this->photo);
            }
        }

        $initials = htmlspecialchars($this->initials, ENT_QUOTES, 'UTF-8');
        $palettes = [
            ['#5C3D2E', '#E8D5B7'],
            ['#7C5A2C', '#F8F0DF'],
            ['#3E4E3B', '#E5EFE3'],
            ['#4A3525', '#EADCC6'],
            ['#6E473B', '#F5E6D3'],
            ['#3B4252', '#ECEFF4'],
        ];
        $idx = abs((int) ($this->id ?? crc32((string) $this->name))) % count($palettes);
        [$bg, $fg] = $palettes[$idx];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120">'
            . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
            . '<stop offset="0%" stop-color="' . $bg . '"/>'
            . '<stop offset="100%" stop-color="#2A1A12"/>'
            . '</linearGradient></defs>'
            . '<rect width="120" height="120" rx="60" fill="url(#g)"/>'
            . '<circle cx="60" cy="60" r="55" fill="none" stroke="#D4AF37" stroke-opacity="0.45" stroke-width="2"/>'
            . '<text x="50%" y="53%" dominant-baseline="middle" text-anchor="middle" '
            . 'fill="' . $fg . '" font-family="Georgia,serif" font-size="42" font-weight="700" letter-spacing="1">'
            . $initials . '</text></svg>';

        return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
    }

    /**
     * Format tanggal lahir dalam Bahasa Indonesia (contoh: 15 Agustus 1950).
     */
    public function getFormattedBirthDateAttribute(): ?string
    {
        if (empty($this->birth_date)) {
            return null;
        }

        try {
            $date = Carbon::parse($this->birth_date);
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];
            return $date->day . ' ' . ($months[$date->month] ?? $date->format('F')) . ' ' . $date->year;
        } catch (\Throwable $e) {
            return (string) $this->birth_date;
        }
    }

    /**
     * Cek apakah anggota memiliki foto asli (bukan avatar inisial).
     */
    public function hasCustomPhoto(): bool
    {
        if (empty($this->photo)) {
            return false;
        }
        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
            return true;
        }
        return Storage::disk('public')->exists($this->photo) || file_exists(public_path($this->photo));
    }
}
