# Panduan Hosting Gratis Tanpa Kartu Kredit — Web Silsilah Klan Kasan Munadi

Project ini mendukung **100% hosting gratis tanpa kartu kredit** menggunakan:
- **Vercel** (Hosting web cepat tanpa sleep + HTTPS otomatis, gratis tanpa kartu kredit!)
- **Neon.tech** (Database PostgreSQL gratis selamanya, bangun otomatis)
- **Cloudinary.com** (Penyimpanan foto galeri & profil gratis 25 GB)

---

## Cara Deploy ke Vercel (Gratis, Tanpa Kartu Kredit, ~2 Menit)
1. Buka **[vercel.com](https://vercel.com)** → login gratis pakai akun **GitHub**.
2. Klik **Add New... → Project** → pilih repository **`klan-munadi`** → klik **Import**.
3. Biarkan **Framework Preset: Other** (Vercel otomatis membaca [vercel.json](vercel.json)).
4. Buka bagian **Environment Variables**, lalu tambahkan **4 variabel ini**:
   - **`APP_KEY`**: kunci aplikasi Laravel (contoh hasil `php artisan key:generate --show`, berawalan `base64:...`)
   - **`DB_URL`**: Connection String PostgreSQL dari **Neon** (`postgresql://...`)
   - **`CLOUDINARY_URL`**: URL API dari **Cloudinary** (`cloudinary://API_KEY:API_SECRET@CLOUD_NAME`)
   - **`ADMIN_PASSWORD`**: kata sandi rahasia pilihan Anda untuk login Admin (username default: `admin`)
5. Klik **Deploy** → tunggu ~1–2 menit sampai selesai dan dapatkan link `https://klan-munadi.vercel.app`!

---

## Cara Menggunakan Mode Admin Setelah Online
- **Untuk seluruh keluarga besar:** Bagikan link Vercel — semua bisa melihat Pohon Silsilah, Peta Persebaran, Galeri, Our Story, dan Jadwal dari HP tanpa login.
- **Untuk Admin:** Klik menu **Admin** (`/admin/login`) → masukkan `admin` dan `ADMIN_PASSWORD` Anda untuk menambah/mengedit anggota, lokasi peta, foto galeri, kisah, dan pengaturan website.
