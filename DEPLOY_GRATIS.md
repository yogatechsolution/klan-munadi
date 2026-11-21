# Panduan Hosting Gratis — Web Silsilah Klan Kasan Munadi

Project ini sudah disiapkan lengkap untuk **hosting gratis \$0 tanpa kartu kredit** menggunakan:
- **Render.com** (Web Hosting Docker gratis + HTTPS otomatis)
- **Neon.tech** (Database PostgreSQL gratis selamanya, bangun otomatis)
- **Cloudinary.com** (Penyimpanan foto galeri & profil gratis 25 GB, permanen & cepat di HP)

---

## Langkah 1 — Upload Kode ke GitHub
1. Buat repository baru (kosong) di [github.com/new](https://github.com/new), misal bernama `klan-munadi`.
2. Jalankan perintah berikut di terminal (di dalam folder `family-tree-php`):
   ```bash
   git remote add origin https://github.com/USERNAME-ANDA/klan-munadi.git
   git branch -M main
   git push -u origin main
   ```

## Langkah 2 — Buat Database Gratis di Neon
1. Masuk ke [neon.tech](https://neon.tech) menggunakan akun GitHub/Google.
2. Klik **Create Project** → beri nama `klan-munadi` → pilih region terdekat (*Singapore / AWS ap-southeast-1*).
3. Salin **Connection String** (berawalan `postgresql://...`). Ini dipakai sebagai **`DB_URL`**.

## Langkah 3 — Buat Storage Foto Gratis di Cloudinary
1. Masuk ke [cloudinary.com](https://cloudinary.com) menggunakan akun Google.
2. Di halaman **Dashboard**, klik tombol **Copy** pada **API Environment Variable** (berawalan `cloudinary://API_KEY:API_SECRET@CLOUD_NAME`).
   Ini dipakai sebagai **`CLOUDINARY_URL`**.

## Langkah 4 — Deploy 1 Klik di Render.com
1. Masuk ke [render.com](https://render.com) menggunakan akun **GitHub**.
2. Klik **New +** → pilih **Blueprint** → pilih repository `klan-munadi`.
   *(File `render.yaml` & `Dockerfile` otomatis mengatur PHP 8.2, HTTPS, `APP_KEY`, `APP_URL`, migrasi database, dan pengisian awal 42 anggota Klan Munadi!)*
3. Cukup isi **3 variabel** yang diminta Render:
   - **`DB_URL`**: tempel Connection String dari Neon (`postgresql://...`).
   - **`CLOUDINARY_URL`**: tempel URL dari Cloudinary (`cloudinary://...`).
   - **`ADMIN_PASSWORD`**: buat kata sandi rahasia untuk login admin keluarga (misal `Munadi2026!`).
4. Klik **Apply** dan tunggu build ~3–5 menit hingga status berubah menjadi **Live**.

---

## Cara Menggunakan Mode Admin Setelah Online
- **Untuk seluruh keluarga besar:** Cukup bagikan link website — semua orang bisa langsung melihat Pohon Silsilah, Peta Persebaran, Galeri, Our Story, dan Jadwal dari HP tanpa perlu login (dan aman dari salah hapus data).
- **Untuk Admin (menambah anggota, upload foto, edit cerita, ubah pengaturan):**
  1. Buka menu **Admin** (pojok kanan atas desktop, menu drawer HP, atau footer bawah) atau kunjungi `/admin/login`.
  2. Masukkan **Username** (`admin`) dan **`ADMIN_PASSWORD`** yang Anda atur di Render.
  3. Semua tombol `+ Tambah Anggota`, `Edit`, `Hapus`, `Unggah Foto`, `Edit Cerita`, dan `Pengaturan Website` langsung muncul!
