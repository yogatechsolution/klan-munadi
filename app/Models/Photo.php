<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = ['gallery_id', 'image'];

    protected $appends = ['url'];

    // Relasi: Foto ini MILIK sebuah folder
    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }

    // URL gambar (mendukung URL Cloudinary maupun penyimpanan lokal storage)
    public function getUrlAttribute(): string
    {
        if (str_starts_with((string) $this->image, 'http://') || str_starts_with((string) $this->image, 'https://')) {
            return (string) $this->image;
        }

        return asset('storage/' . $this->image);
    }
}
