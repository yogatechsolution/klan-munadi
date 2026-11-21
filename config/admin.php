<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kredensial Mode Admin Portal Keluarga
    |--------------------------------------------------------------------------
    | Atur ADMIN_USERNAME dan ADMIN_PASSWORD di file .env atau Environment
    | Variables hosting (Render) agar hanya pengurus/admin yang dapat menambah,
    | mengubah, atau menghapus data silsilah, galeri, cerita, dan pengaturan.
    */
    'username' => env('ADMIN_USERNAME', 'admin'),
    'password' => env('ADMIN_PASSWORD', 'munadi2026'),
];
