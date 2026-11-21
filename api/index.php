<?php

/**
 * Entry point Laravel 12 untuk Vercel Serverless (vercel-php).
 * Menyiapkan direktori writable di /tmp sebelum Laravel dijalankan
 * karena filesystem utama Vercel bersifat read-only.
 */
$tmpDirs = [
    '/tmp/views',
    '/tmp/storage/app/public',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
];

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Layani file statis public/assets bila masuk melalui fungsi PHP (fallback aman)
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if (str_starts_with($uri, '/assets/') || $uri === '/favicon.ico' || $uri === '/robots.txt') {
    $publicBase = realpath(__DIR__ . '/../public');
    $candidate = $publicBase ? realpath($publicBase . '/' . ltrim($uri, '/')) : false;
    if ($candidate && $publicBase && str_starts_with($candidate, $publicBase . DIRECTORY_SEPARATOR) && is_file($candidate)) {
        $ext = strtolower(pathinfo($candidate, PATHINFO_EXTENSION));
        $mimeTypes = [
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'txt' => 'text/plain; charset=UTF-8',
        ];
        header('Content-Type: ' . ($mimeTypes[$ext] ?? 'application/octet-stream'));
        header('Cache-Control: public, max-age=604800');
        readfile($candidate);
        exit;
    }
}

require __DIR__ . '/../public/index.php';
