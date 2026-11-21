<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Cloudinary (Penyimpanan Foto Cloud Gratis)
    |--------------------------------------------------------------------------
    | Bisa diisi lewat satu variabel CLOUDINARY_URL (format:
    | cloudinary://API_KEY:API_SECRET@CLOUD_NAME) atau terpisah per variabel.
    | Jika kosong, aplikasi otomatis menyimpan foto secara lokal di server.
    */
    'url' => env('CLOUDINARY_URL'),
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'folder' => env('CLOUDINARY_FOLDER', 'klan-munadi'),
];
