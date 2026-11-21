<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['name', 'description'];

    // Relasi: Satu folder punya BANYAK foto
    public function photos()
    {
        return $this->hasMany(Photo::class)->latest();
    }

    // Relasi: Foto sampul (foto terbaru di dalam album)
    public function coverPhoto()
    {
        return $this->hasOne(Photo::class)->latestOfMany();
    }
}
