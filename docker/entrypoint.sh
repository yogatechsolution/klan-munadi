#!/usr/bin/env bash
set -e

PORT="${PORT:-8080}"

# Gunakan RENDER_EXTERNAL_URL otomatis jika APP_URL belum diisi manual
if [ -z "${APP_URL}" ] && [ -n "${RENDER_EXTERNAL_URL}" ]; then
    export APP_URL="${RENDER_EXTERNAL_URL}"
fi

# Pastikan APP_KEY selalu valid untuk AES-256-CBC Laravel (termasuk hasil generateValue Render)
if [ -n "${APP_KEY}" ]; then
    export APP_KEY="$(php -r '
        $k = getenv("APP_KEY");
        if (str_starts_with($k, "base64:") && strlen(base64_decode(substr($k, 7), true) ?: "") === 32) {
            echo $k;
        } elseif (strlen($k) === 32) {
            echo $k;
        } else {
            echo "base64:" . base64_encode(hash("sha256", $k, true));
        }
    ')"
else
    export APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
fi

# Sesuaikan port Apache dengan variabel PORT dari Render/hosting
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:8080>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Pastikan folder writable oleh Apache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Pastikan storage link tersedia
php artisan storage:link --force >/dev/null 2>&1 || true

# Jalankan migrasi database otomatis (dengan retry singkat bila Neon sedang wake-up)
echo "Menjalankan migrasi database..."
MIGRATED=0
for i in 1 2 3 4 5; do
    if php artisan migrate --force; then
        MIGRATED=1
        break
    fi
    echo "Menunggu koneksi database (percobaan $i/5)..."
    sleep 3
done

if [ "$MIGRATED" -eq 1 ]; then
    # Isi data awal Klan Munadi hanya jika tabel family_members masih kosong
    php artisan family:seed-initial || true
else
    echo "PERINGATAN: Migrasi database gagal. Periksa DB_URL / konfigurasi database Anda."
fi

# Optimasi cache Laravel untuk kecepatan produksi
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Menjalankan server web pada port ${PORT}..."
exec apache2-foreground
