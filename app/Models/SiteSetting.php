<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Throwable;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Ambil satu nilai setting dengan aman (tidak crash bila tabel belum tersedia).
     */
    public static function getValue(string $key, ?string $default = null): ?string
    {
        try {
            $setting = static::where('key', $key)->first();
            return ($setting && $setting->value !== null && $setting->value !== '')
                ? $setting->value
                : $default;
        } catch (Throwable $e) {
            return $default;
        }
    }

    /**
     * Ambil seluruh key => value setting dalam satu query secara aman.
     */
    public static function getAllMap(): array
    {
        try {
            return static::pluck('value', 'key')->toArray();
        } catch (Throwable $e) {
            return [];
        }
    }
}
