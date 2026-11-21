<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\GalleryController;

// =========================================================================
// AUTENTIKASI MODE ADMIN
// =========================================================================
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->middleware('throttle:10,1')
    ->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// =========================================================================
// HALAMAN PUBLIK (DAPAT DIAKSES SELURUH KELUARGA TANPA LOGIN)
// =========================================================================
Route::get('/', [FamilyMemberController::class, 'home'])->name('home');
Route::get('/jadwal', [FamilyMemberController::class, 'jadwal'])->name('jadwal');
Route::get('/kontak', [FamilyMemberController::class, 'contact'])->name('contact');
Route::get('/persebaran-keluarga', [FamilyMemberController::class, 'map'])->name('family.map');
Route::get('/galeri', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/galeri/{id}', [GalleryController::class, 'show'])->where('id', '[0-9]+')->name('gallery.show');
Route::get('/our-story', [FamilyMemberController::class, 'story'])->name('story');

// =========================================================================
// RUTE KELUARGA (AKSI TAMBAH/EDIT/HAPUS/PENGATURAN KHUSUS ADMIN)
// =========================================================================
Route::prefix('keluarga')->group(function () {
    Route::middleware('admin')->group(function () {
        Route::get('/tambah', [FamilyMemberController::class, 'create'])->name('family.create');
        Route::post('/simpan', [FamilyMemberController::class, 'store'])->name('family.store');
        Route::get('/edit/{id}', [FamilyMemberController::class, 'edit'])->where('id', '[0-9]+')->name('family.edit');
        Route::put('/update/{id}', [FamilyMemberController::class, 'update'])->where('id', '[0-9]+')->name('family.update');
        Route::delete('/hapus/{id}', [FamilyMemberController::class, 'destroy'])->where('id', '[0-9]+')->name('family.destroy');

        // Pengaturan Website & Kontak
        Route::get('/settings', [FamilyMemberController::class, 'settings'])->name('settings.index');
        Route::post('/settings/update', [FamilyMemberController::class, 'updateSetting'])->name('settings.update');
        Route::post('/settings/contact', [FamilyMemberController::class, 'updateContactSettings'])->name('settings.contact.update');
        Route::delete('/settings/{key}', [FamilyMemberController::class, 'resetSetting'])->name('settings.reset');
    });

    // Rute utama (Pohon Silsilah publik) diletakkan paling bawah dalam grup ini
    Route::get('/{id?}', [FamilyMemberController::class, 'index'])
        ->where('id', '[0-9]+')
        ->name('family.index');
});

// Aksi Galeri & Cerita (Khusus Admin)
Route::middleware('admin')->group(function () {
    Route::post('/galeri/folder', [GalleryController::class, 'storeFolder'])->name('gallery.storeFolder');
    Route::post('/galeri/{id}/foto', [GalleryController::class, 'storePhoto'])->where('id', '[0-9]+')->name('gallery.storePhoto');
    Route::delete('/galeri/foto/{id}', [GalleryController::class, 'destroyPhoto'])->where('id', '[0-9]+')->name('gallery.destroyPhoto');
    Route::delete('/galeri/folder/{id}', [GalleryController::class, 'destroyFolder'])->where('id', '[0-9]+')->name('gallery.destroyFolder');

    Route::post('/update-story/{id}', [FamilyMemberController::class, 'updateStory'])->where('id', '[0-9]+')->name('story.update');
});

// Fallback aman untuk file storage publik lokal
Route::get('/db-status', function () {
    $url = config('database.connections.pgsql.url');
    $parsedHost = is_string($url) && $url !== '' ? parse_url($url, PHP_URL_HOST) : null;
    try {
        $count = \App\Models\FamilyMember::count();
        return response()->json([
            'ok' => true,
            'pdo_pgsql' => extension_loaded('pdo_pgsql'),
            'db_default' => config('database.default'),
            'db_url_set' => !empty($url),
            'db_host' => $parsedHost,
            'members_count' => $count,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'pdo_pgsql' => extension_loaded('pdo_pgsql'),
            'db_default' => config('database.default'),
            'db_url_set' => !empty($url),
            'db_host' => $parsedHost,
            'pgoptions' => getenv('PGOPTIONS') ?: null,
            'error_class' => get_class($e),
            'error_message' => preg_replace('#://[^@]+@#', '://***:***@', $e->getMessage()),
        ], 500);
    }
});

Route::get('/storage/{path}', function (string $path) {
    $baseDir = realpath(storage_path('app/public'));
    if ($baseDir === false) {
        abort(404);
    }

    $normalized = ltrim(str_replace(['\\', "\0"], ['/', ''], $path), '/');
    $fullPath = realpath($baseDir . DIRECTORY_SEPARATOR . $normalized);

    if ($fullPath === false || !str_starts_with($fullPath, $baseDir . DIRECTORY_SEPARATOR) || !is_file($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath, [
        'Cache-Control' => 'public, max-age=604800',
    ]);
})->where('path', '.*');
