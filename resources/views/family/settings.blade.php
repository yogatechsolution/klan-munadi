@extends('layouts.app')

@section('title', 'Pengaturan Website — Klan Munadi')

@push('styles')
<style>
    .settings-wrap {
        padding: clamp(22px, 4vw, 40px) 0 56px;
    }

    .settings-section {
        background: var(--surface);
        border: 1px solid var(--border);
        border-top: 4px solid var(--gold);
        border-radius: var(--radius-lg);
        padding: clamp(20px, 3.5vw, 32px);
        box-shadow: var(--shadow-sm);
        margin-bottom: 26px;
    }

    .settings-section h2 {
        margin: 0 0 6px;
        font-size: 1.35rem;
    }

    .settings-section .sub {
        margin: 0 0 22px;
        font-size: 14px;
        color: var(--text-muted);
    }

    .image-settings-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .img-setting-card {
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        background: var(--surface-muted);
        display: flex;
        flex-direction: column;
    }

    .img-setting-preview {
        aspect-ratio: 16 / 9;
        background: var(--espresso);
        overflow: hidden;
    }

    .img-setting-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .img-setting-body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
    }

    .img-setting-body h3 {
        margin: 0;
        font-size: 1.05rem;
    }

    .img-setting-body p {
        margin: 0;
        font-size: 12.5px;
        color: var(--text-muted);
    }

    .img-setting-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: auto;
    }

    .contact-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .contact-form-grid .full {
        grid-column: span 2;
    }

    @media (max-width: 768px) {
        .image-settings-grid,
        .contact-form-grid {
            grid-template-columns: 1fr;
        }

        .contact-form-grid .full {
            grid-column: span 1;
        }
    }
</style>
@endpush

@section('content')
<div class="km-page-header">
    <div class="km-container">
        <span class="km-page-eyebrow">Personalisasi Portal</span>
        <h1>Pengaturan Website</h1>
        <p>Ubah gambar utama beranda, latar belakang pohon silsilah, serta informasi kontak pengurus keluarga besar.</p>
    </div>
</div>

<div class="km-container-narrow settings-wrap">
    @if(session('success'))
        <div class="km-alert km-alert-success">
            <span>{{ session('success') }}</span>
            <button type="button" class="km-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="km-alert km-alert-error">
            <div>
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="km-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @php
        $imageSlots = [
            'hero_image' => [
                'title' => 'Banner Hero Beranda',
                'desc' => 'Gambar latar utama di bagian paling atas halaman Beranda.',
            ],
            'roots_image' => [
                'title' => 'Foto "Our Roots"',
                'desc' => 'Gambar untuk kartu kisah asal-usul keluarga di Beranda.',
            ],
            'legacy_image' => [
                'title' => 'Foto "The Legacy"',
                'desc' => 'Gambar kebersamaan warisan keluarga di Beranda.',
            ],
            'tree_bg_image' => [
                'title' => 'Latar Pohon Silsilah',
                'desc' => 'Gambar latar lembut di balik bagan Pohon Silsilah.',
            ],
        ];
    @endphp

    <!-- BAGIAN 1: PENGATURAN FOTO -->
    <section class="settings-section">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-bottom:6px;">
            <h2 style="margin:0;">Gambar Beranda &amp; Silsilah</h2>
            @if(app(\App\Services\MediaStorageService::class)->isCloudinaryEnabled())
                <span class="km-badge km-badge-gold">Cloud Storage Aktif (Cloudinary)</span>
            @else
                <span class="km-badge km-badge-muted">Mode Penyimpanan Lokal</span>
            @endif
        </div>
        <p class="sub">Format didukung: JPG, PNG, atau WEBP dengan ukuran maksimal 4MB per gambar.</p>

        <div class="image-settings-grid">
            @foreach($imageSlots as $key => $slot)
                <div class="img-setting-card">
                    <div class="img-setting-preview">
                        <img src="{{ $previews[$key] }}" alt="{{ $slot['title'] }}" loading="lazy">
                    </div>
                    <div class="img-setting-body">
                        <h3>{{ $slot['title'] }}</h3>
                        <p>{{ $slot['desc'] }}</p>

                        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:10px;">
                            @csrf
                            <input type="hidden" name="key" value="{{ $key }}">
                            <input type="file" name="image" accept="image/*" required class="km-input" style="padding:8px 10px;font-size:13px;">
                            <div class="img-setting-actions">
                                <button type="submit" class="km-btn km-btn-gold km-btn-sm" style="flex:1;">Unggah Gambar</button>
                            </div>
                        </form>

                        @if($customFlags[$key])
                            <form action="{{ route('settings.reset', $key) }}" method="POST" onsubmit="return confirm('Kembalikan {{ $slot['title'] }} ke gambar bawaan?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="km-btn km-btn-outline km-btn-sm" style="width:100%;">Reset ke Bawaan</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- BAGIAN 2: PENGATURAN KONTAK PENGURUS -->
    <section class="settings-section">
        <h2>Informasi Kontak Pengurus</h2>
        <p class="sub">Data ini akan tampil pada halaman Kontak agar anggota keluarga mudah menghubungi pengurus.</p>

        <form action="{{ route('settings.contact.update') }}" method="POST">
            @csrf
            <div class="contact-form-grid">
                <div>
                    <label class="km-label" for="contact_name">Nama Pengurus / Koordinator</label>
                    <input type="text" id="contact_name" name="contact_name" class="km-input" value="{{ old('contact_name', $contact['name']) }}">
                </div>
                <div>
                    <label class="km-label" for="contact_phone">Nomor WhatsApp / Telepon</label>
                    <input type="text" id="contact_phone" name="contact_phone" class="km-input" placeholder="Contoh: 081234567890" value="{{ old('contact_phone', $contact['phone']) }}">
                </div>
                <div>
                    <label class="km-label" for="contact_email">Email Keluarga</label>
                    <input type="email" id="contact_email" name="contact_email" class="km-input" placeholder="keluarga@contoh.com" value="{{ old('contact_email', $contact['email']) }}">
                </div>
                <div>
                    <label class="km-label" for="contact_address">Alamat Rumah Besar</label>
                    <input type="text" id="contact_address" name="contact_address" class="km-input" value="{{ old('contact_address', $contact['address']) }}">
                </div>
                <div class="full">
                    <label class="km-label" for="contact_note">Catatan Tambahan</label>
                    <textarea id="contact_note" name="contact_note" rows="2" class="km-textarea">{{ old('contact_note', $contact['note']) }}</textarea>
                </div>
            </div>

            <div style="margin-top:20px;display:flex;justify-content:flex-end;">
                <button type="submit" class="km-btn km-btn-dark">Simpan Info Kontak</button>
            </div>
        </form>
    </section>
</div>
@endsection
