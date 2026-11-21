<?php

use App\Models\FamilyMember;
use Database\Seeders\MunadiFamilySeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('family:seed-initial', function () {
    if (FamilyMember::count() > 0) {
        $this->info('Data keluarga sudah terisi (' . FamilyMember::count() . ' anggota). Seeder dilewati agar tidak duplikat.');
        return;
    }

    $this->info('Database masih kosong. Mengisi data awal Klan Munadi...');
    $this->call('db:seed', ['--class' => MunadiFamilySeeder::class, '--force' => true]);

    $kasan = FamilyMember::where('name', 'Kasan Munadi')->first();
    if ($kasan && empty($kasan->story)) {
        $kasan->update([
            'bio' => 'Leluhur utama Keluarga Besar Bani Kasan Munadi.',
            'story' => "Mbah Kasan Munadi bersama Mbah Sutirah adalah leluhur utama Keluarga Besar Klan Munadi.\n\nMelalui keteladanan hidup yang sederhana, rukun, dan penuh kasih sayang, beliau membesarkan enam orang putra-putri yang kini terus menyambung silaturahmi lintas generasi.",
        ]);
    }

    $this->info('Seeder awal selesai (' . FamilyMember::count() . ' anggota berhasil ditambahkan).');
})->purpose('Isi data awal Klan Munadi sekali saja apabila tabel masih kosong');
