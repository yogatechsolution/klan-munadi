@extends('layouts.app')

@section('title', 'Galeri Keluarga — Klan Munadi')

@push('styles')
<style>
    .gallery-wrap {
        padding: clamp(22px, 4vw, 40px) 0 56px;
    }

    .create-album-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-top: 4px solid var(--gold);
        border-radius: var(--radius-lg);
        padding: clamp(18px, 3vw, 26px);
        box-shadow: var(--shadow-sm);
        margin-bottom: 30px;
    }

    .create-album-card h2 {
        margin: 0 0 14px;
        font-size: 1.2rem;
    }

    .create-album-form {
        display: grid;
        grid-template-columns: 1.2fr 1.5fr auto;
        gap: 10px;
        align-items: center;
    }

    .album-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
    }

    .album-card {
        position: relative;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-xs);
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        display: flex;
        flex-direction: column;
    }

    .album-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
        border-color: var(--gold);
    }

    .album-link {
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .album-cover {
        position: relative;
        aspect-ratio: 16 / 10;
        background: linear-gradient(135deg, #3E271D 0%, #24150E 100%);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .album-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .album-card:hover .album-cover img {
        transform: scale(1.05);
    }

    .album-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        color: rgba(248, 236, 212, 0.65);
        font-size: 12.5px;
    }

    .album-count-badge {
        position: absolute;
        bottom: 10px;
        left: 10px;
        background: rgba(24, 15, 10, 0.78);
        backdrop-filter: blur(6px);
        color: #FBF4E2;
        padding: 4px 11px;
        border-radius: var(--radius-pill);
        font-size: 12px;
        font-weight: 600;
    }

    .album-body {
        padding: 16px 18px 18px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .album-title {
        font-family: var(--font-display);
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--espresso);
        margin: 0 0 6px;
    }

    .album-desc {
        font-size: 13.5px;
        color: var(--text-muted);
        margin: 0;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .album-delete-form {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
    }

    .btn-delete-album {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.3);
        background: rgba(24, 15, 10, 0.68);
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s, transform 0.15s;
    }

    .btn-delete-album:hover {
        background: var(--danger);
        transform: scale(1.06);
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 48px 20px;
        background: var(--surface);
        border: 1.5px dashed var(--border-strong);
        border-radius: var(--radius-lg);
        color: var(--text-muted);
    }

    @media (max-width: 820px) {
        .create-album-form {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 520px) {
        .album-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="km-page-header">
    <div class="km-container">
        <span class="km-page-eyebrow">Dokumentasi Kebersamaan</span>
        <h1>Galeri Keluarga</h1>
        <p>Kumpulan album foto momen lebaran, reuni, dan silaturahmi keluarga besar Bani Kasan Munadi.</p>
    </div>
</div>

<div class="km-container gallery-wrap">
    @if(session('success'))
        <div class="km-alert km-alert-success">
            <span>{{ session('success') }}</span>
            <button type="button" class="km-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="km-alert km-alert-error">
            <span>{{ $errors->first() }}</span>
            <button type="button" class="km-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- Form Buat Album Baru (Khusus Admin) -->
    @if($isAdmin)
        <div class="create-album-card">
            <h2>+ Buat Album Baru</h2>
            <form action="{{ route('gallery.storeFolder') }}" method="POST" class="create-album-form">
                @csrf
                <input type="text" name="name" class="km-input" placeholder="Nama album (misal: Lebaran 2026)..." value="{{ old('name') }}" required>
                <input type="text" name="description" class="km-input" placeholder="Deskripsi singkat acara (opsional)..." value="{{ old('description') }}">
                <button type="submit" class="km-btn km-btn-gold">Buat Album</button>
            </form>
        </div>
    @endif

    <!-- Daftar Album -->
    <div class="album-grid">
        @forelse($galleries as $gallery)
            <article class="album-card">
                @if($isAdmin)
                    <form action="{{ route('gallery.destroyFolder', $gallery->id) }}"
                          method="POST"
                          class="album-delete-form"
                          onsubmit="return confirm('Hapus album \'{{ addslashes($gallery->name) }}\' beserta seluruh foto di dalamnya?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete-album" title="Hapus Album" aria-label="Hapus Album">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><path d="M10 11v6"></path><path d="M14 11v6"></path><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path></svg>
                        </button>
                    </form>
                @endif

                <a href="{{ route('gallery.show', $gallery->id) }}" class="album-link">
                    <div class="album-cover">
                        @if($gallery->coverPhoto)
                            <img src="{{ $gallery->coverPhoto->url }}" alt="{{ $gallery->name }}" loading="lazy">
                        @else
                            <div class="album-placeholder">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                <span>Belum ada foto</span>
                            </div>
                        @endif
                        <span class="album-count-badge">{{ $gallery->photos_count }} Foto</span>
                    </div>

                    <div class="album-body">
                        <h3 class="album-title">{{ $gallery->name }}</h3>
                        <p class="album-desc">{{ $gallery->description ?: 'Ketuk untuk membuka dan menambahkan foto kenangan keluarga.' }}</p>
                    </div>
                </a>
            </article>
        @empty
            <div class="empty-state">
                <h3 style="margin-top:0;">Belum Ada Album Galeri</h3>
                <p style="margin:0;">Buat album pertama di atas untuk mulai menyimpan foto kebersamaan keluarga.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
