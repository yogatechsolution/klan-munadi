<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FamilyMember;

class MunadiFamilySeeder extends Seeder
{
    public function run()
    {
        // ==========================================
        // GENERASI 1: LELUHUR (ROOT)
        // ==========================================
        $kasan = FamilyMember::create(['name' => 'Kasan Munadi']);
        $sutirah = FamilyMember::create(['name' => 'Sutirah']);

        // ==========================================
        // GENERASI 2: ANAK-ANAK MBAH MUNADI
        // ==========================================
        
        // 1. Sukiyem & Suami
        $sukiyem = FamilyMember::create(['name' => 'Sukiyem', 'father_id' => $kasan->id, 'mother_id' => $sutirah->id]);
        $iman = FamilyMember::create(['name' => 'Iman Supangat']);

        // 2. Abu Thalib & Istri
        $abu = FamilyMember::create(['name' => 'Abu Thalib', 'father_id' => $kasan->id, 'mother_id' => $sutirah->id]);
        $parti = FamilyMember::create(['name' => 'Parti']);

        // 3. Iman Asy'ari & Istri
        $asyari = FamilyMember::create(['name' => 'Iman Asy\'ari', 'father_id' => $kasan->id, 'mother_id' => $sutirah->id]);
        $sukinem = FamilyMember::create(['name' => 'Sukinem']);

        // 4. Jamin & Istri
        $jamin = FamilyMember::create(['name' => 'Jamin (Adi)', 'father_id' => $kasan->id, 'mother_id' => $sutirah->id]);
        $mbokdhe_adi = FamilyMember::create(['name' => 'Mbokdhe Adi']);

        // 5. Darmin (Sarji) & Istri
        $darmin = FamilyMember::create(['name' => 'Darmin (Sarji)', 'father_id' => $kasan->id, 'mother_id' => $sutirah->id]);
        $tasmi = FamilyMember::create(['name' => 'Tasmi']);

        // 6. Musmin & Istri
        $musmin = FamilyMember::create(['name' => 'Musmin', 'father_id' => $kasan->id, 'mother_id' => $sutirah->id]);
        $padmi = FamilyMember::create(['name' => 'Padmi']);


        // ==========================================
        // GENERASI 3: CUCU-CUCU MBAH MUNADI
        // ==========================================

        // --- Anak-anak Keluarga Sukiyem ---
        FamilyMember::create(['name' => 'Muhtarom', 'father_id' => $iman->id, 'mother_id' => $sukiyem->id]);
        FamilyMember::create(['name' => 'Suyanto', 'father_id' => $iman->id, 'mother_id' => $sukiyem->id]);
        FamilyMember::create(['name' => 'Sutiknyo', 'father_id' => $iman->id, 'mother_id' => $sukiyem->id]);

        // --- Anak-anak Keluarga Abu Thalib ---
        FamilyMember::create(['name' => 'Slamet', 'father_id' => $abu->id, 'mother_id' => $parti->id]);
        FamilyMember::create(['name' => 'Insriati', 'father_id' => $abu->id, 'mother_id' => $parti->id]);
        FamilyMember::create(['name' => 'Asriatul Murni', 'father_id' => $abu->id, 'mother_id' => $parti->id]);
        FamilyMember::create(['name' => 'Fitriah', 'father_id' => $abu->id, 'mother_id' => $parti->id]);
        FamilyMember::create(['name' => 'Sulistyowati', 'father_id' => $abu->id, 'mother_id' => $parti->id]);
        FamilyMember::create(['name' => 'Sri Nuryati', 'father_id' => $abu->id, 'mother_id' => $parti->id]);
        FamilyMember::create(['name' => 'Umi Sitatin', 'father_id' => $abu->id, 'mother_id' => $parti->id]);
        FamilyMember::create(['name' => 'Sri Wayhuni', 'father_id' => $abu->id, 'mother_id' => $parti->id]);

        // --- Anak-anak Keluarga Iman Asy'ari ---
        FamilyMember::create(['name' => 'Puji Astuti', 'father_id' => $asyari->id, 'mother_id' => $sukinem->id]);
        FamilyMember::create(['name' => 'Wiwik T', 'father_id' => $asyari->id, 'mother_id' => $sukinem->id]);
        FamilyMember::create(['name' => 'Sale Wasesa', 'father_id' => $asyari->id, 'mother_id' => $sukinem->id]);
        FamilyMember::create(['name' => 'Sugeng Wahyudi', 'father_id' => $asyari->id, 'mother_id' => $sukinem->id]);
        FamilyMember::create(['name' => 'Sugeng Widodo', 'father_id' => $asyari->id, 'mother_id' => $sukinem->id]);
        FamilyMember::create(['name' => 'Hari Subagyo', 'father_id' => $asyari->id, 'mother_id' => $sukinem->id]);
        FamilyMember::create(['name' => 'Muhamad Riadi', 'father_id' => $asyari->id, 'mother_id' => $sukinem->id]);

        // --- Anak Keluarga Jamin ---
        FamilyMember::create(['name' => 'Sudarmini', 'father_id' => $jamin->id, 'mother_id' => $mbokdhe_adi->id]);
        FamilyMember::create(['name' => 'Widodo', 'father_id' => $jamin->id, 'mother_id' => $mbokdhe_adi->id]);

        // --- Anak-anak Keluarga Darmin (Sarji) ---
        FamilyMember::create(['name' => 'Hendro Martono', 'father_id' => $darmin->id, 'mother_id' => $tasmi->id]);
        FamilyMember::create(['name' => 'Gunawan Wibisono', 'father_id' => $darmin->id, 'mother_id' => $tasmi->id]);
        FamilyMember::create(['name' => 'Mardiyah Hayati', 'father_id' => $darmin->id, 'mother_id' => $tasmi->id]);
        FamilyMember::create(['name' => 'Rahmat Isdiyanto', 'father_id' => $darmin->id, 'mother_id' => $tasmi->id]);
        FamilyMember::create(['name' => 'Nurrohmah Purnaningsih', 'father_id' => $darmin->id, 'mother_id' => $tasmi->id]);
        FamilyMember::create(['name' => 'Sigit Rudianto', 'father_id' => $darmin->id, 'mother_id' => $tasmi->id]);

        // --- Anak Keluarga Musmin ---
        FamilyMember::create(['name' => 'Eko', 'father_id' => $musmin->id, 'mother_id' => $padmi->id]);
        FamilyMember::create(['name' => 'Suryati', 'father_id' => $musmin->id, 'mother_id' => $padmi->id]);
    }
}