<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Photo;
use App\Services\MediaStorageService;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function __construct(private readonly MediaStorageService $mediaStorage)
    {
    }

    /**
     * Menampilkan halaman daftar Album / Folder Galeri.
     */
    public function index()
    {
        $galleries = Gallery::withCount('photos')
            ->with('coverPhoto')
            ->latest()
            ->get();

        return view('gallery.index', compact('galleries'));
    }

    /**
     * Menyimpan Album / Folder Baru.
     */
    public function storeFolder(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama album wajib diisi.',
        ]);

        Gallery::create([
            'name' => trim($validated['name']),
            'description' => isset($validated['description']) && trim($validated['description']) !== ''
                ? trim($validated['description'])
                : null,
        ]);

        return back()->with('success', 'Album "' . trim($validated['name']) . '" berhasil dibuat!');
    }

    /**
     * Menampilkan isi dalam Album (Foto-foto).
     */
    public function show($id)
    {
        $gallery = Gallery::with('photos')->findOrFail($id);
        return view('gallery.show', compact('gallery'));
    }

    /**
     * Menyimpan / Upload Foto ke dalam Album (mendukung 1 foto atau banyak foto sekaligus).
     */
    public function storePhoto(Request $request, $id)
    {
        $gallery = Gallery::findOrFail($id);

        if ($request->hasFile('images')) {
            $request->validate([
                'images' => 'required|array|min:1|max:20',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ], [
                'images.*.image' => 'Semua file harus berupa gambar.',
                'images.*.max' => 'Ukuran setiap foto maksimal 5MB.',
            ]);

            $count = 0;
            foreach ($request->file('images') as $file) {
                $imagePath = $this->mediaStorage->storeImage($file, 'gallery');
                Photo::create([
                    'gallery_id' => $gallery->id,
                    'image' => $imagePath,
                ]);
                $count++;
            }

            return back()->with('success', $count . ' foto berhasil diunggah ke album "' . $gallery->name . '"!');
        }

        // Backward compatibility untuk input tunggal bernama 'image'
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'image.required' => 'Pilih foto terlebih dahulu.',
            'image.image' => 'File harus berupa gambar yang valid.',
            'image.max' => 'Ukuran foto maksimal 5MB.',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $this->mediaStorage->storeImage($request->file('image'), 'gallery');
            Photo::create([
                'gallery_id' => $gallery->id,
                'image' => $imagePath,
            ]);
        }

        return back()->with('success', 'Foto berhasil diunggah!');
    }

    /**
     * Menghapus satu foto.
     */
    public function destroyPhoto($id)
    {
        $photo = Photo::findOrFail($id);
        $this->mediaStorage->deleteImage($photo->image);
        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus!');
    }

    /**
     * Menghapus seluruh folder album beserta fotonya.
     */
    public function destroyFolder($id)
    {
        $gallery = Gallery::with('photos')->findOrFail($id);
        $name = $gallery->name;

        foreach ($gallery->photos as $photo) {
            $this->mediaStorage->deleteImage($photo->image);
        }

        $gallery->delete();

        return redirect()
            ->route('gallery.index')
            ->with('success', 'Album "' . $name . '" dan seluruh fotonya berhasil dihapus!');
    }
}
