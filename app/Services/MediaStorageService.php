<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MediaStorageService
{
    /**
     * Cek apakah Cloudinary sudah dikonfigurasi di environment.
     */
    public function isCloudinaryEnabled(): bool
    {
        $creds = $this->getCloudinaryCredentials();
        return !empty($creds['cloud_name']) && !empty($creds['api_key']) && !empty($creds['api_secret']);
    }

    /**
     * Simpan file gambar ke Cloudinary (jika aktif) atau ke disk lokal.
     *
     * @param  UploadedFile  $file
     * @param  string  $subfolder  Contoh: 'avatars', 'gallery', 'settings'
     * @param  bool  $usePublicAssetsFallback  Jika true dan lokal, simpan ke public/assets/images
     * @param  string|null  $customPrefix  Prefix nama file opsional
     */
    public function storeImage(
        UploadedFile $file,
        string $subfolder,
        bool $usePublicAssetsFallback = false,
        ?string $customPrefix = null
    ): string {
        if ($this->isCloudinaryEnabled()) {
            return $this->uploadToCloudinary($file, $subfolder);
        }

        if ($usePublicAssetsFallback) {
            $targetDir = public_path('assets/images');
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $prefix = $customPrefix ? preg_replace('/[^a-zA-Z0-9_-]/', '', $customPrefix) : 'img';
            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $fileName = $prefix . '_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
            $file->move($targetDir, $fileName);

            return 'assets/images/' . $fileName;
        }

        return $file->store($subfolder, 'public');
    }

    /**
     * Hapus gambar dari Cloudinary atau penyimpanan lokal secara aman.
     */
    public function deleteImage(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            if ($this->isCloudinaryEnabled() && str_contains($path, 'res.cloudinary.com')) {
                $this->deleteFromCloudinaryByUrl($path);
            }
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Upload file ke Cloudinary menggunakan signed REST Upload API resmi.
     */
    private function uploadToCloudinary(UploadedFile $file, string $subfolder): string
    {
        $creds = $this->getCloudinaryCredentials();
        $baseFolder = trim((string) config('cloudinary.folder', 'klan-munadi'), '/');
        $folder = $baseFolder . '/' . trim($subfolder, '/');
        $timestamp = time();

        // Parameter wajib diurutkan alfabetis untuk signature Cloudinary
        $paramsToSign = "folder={$folder}&timestamp={$timestamp}";
        $signature = sha1($paramsToSign . $creds['api_secret']);

        $endpoint = "https://api.cloudinary.com/v1_1/{$creds['cloud_name']}/image/upload";

        $stream = fopen($file->getRealPath(), 'r');
        if ($stream === false) {
            throw new RuntimeException('Gagal membaca file gambar yang diunggah.');
        }

        try {
            $response = Http::timeout(30)
                ->attach(
                    'file',
                    $stream,
                    $file->getClientOriginalName() ?: ('upload.' . ($file->extension() ?: 'jpg'))
                )
                ->post($endpoint, [
                    'api_key' => (string) $creds['api_key'],
                    'timestamp' => (string) $timestamp,
                    'folder' => (string) $folder,
                    'signature' => (string) $signature,
                ]);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        if (!$response->successful()) {
            Log::error('Cloudinary upload gagal', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('Gagal mengunggah gambar ke Cloudinary. Periksa konfigurasi CLOUDINARY_URL.');
        }

        $secureUrl = $response->json('secure_url');
        if (empty($secureUrl)) {
            throw new RuntimeException('Cloudinary tidak mengembalikan URL gambar.');
        }

        return $secureUrl;
    }

    /**
     * Hapus gambar Cloudinary berdasarkan secure_url.
     */
    private function deleteFromCloudinaryByUrl(string $url): void
    {
        try {
            $creds = $this->getCloudinaryCredentials();
            $path = parse_url($url, PHP_URL_PATH);
            if (!$path || !preg_match('#/upload/(?:v\d+/)?(.+)\.[a-zA-Z0-9]+$#', $path, $matches)) {
                return;
            }

            $publicId = $matches[1];
            $timestamp = time();
            $signature = sha1("public_id={$publicId}&timestamp={$timestamp}" . $creds['api_secret']);

            Http::timeout(15)->post("https://api.cloudinary.com/v1_1/{$creds['cloud_name']}/image/destroy", [
                'public_id' => $publicId,
                'api_key' => $creds['api_key'],
                'timestamp' => $timestamp,
                'signature' => $signature,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal menghapus gambar dari Cloudinary: ' . $e->getMessage());
        }
    }

    private function getCloudinaryCredentials(): array
    {
        $cloudName = config('cloudinary.cloud_name');
        $apiKey = config('cloudinary.api_key');
        $apiSecret = config('cloudinary.api_secret');

        $url = config('cloudinary.url');
        if (!empty($url)) {
            $parsed = parse_url($url);
            if (is_array($parsed)) {
                $cloudName = $cloudName ?: ($parsed['host'] ?? null);
                $apiKey = $apiKey ?: ($parsed['user'] ?? null);
                $apiSecret = $apiSecret ?: ($parsed['pass'] ?? null);
            }
        }

        return [
            'cloud_name' => $cloudName ? trim((string) $cloudName) : null,
            'api_key' => $apiKey ? trim((string) $apiKey) : null,
            'api_secret' => $apiSecret ? trim((string) $apiSecret) : null,
        ];
    }
}
