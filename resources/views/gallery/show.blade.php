@extends('layouts.app')

@section('title', 'Album ' . $gallery->name . ' — Galeri Klan Munadi')

@push('styles')
<style>
    .album-show-wrap {
        padding: clamp(20px, 4vw, 36px) 0 56px;
    }

    .album-top-nav {
        margin-bottom: 16px;
    }

    .album-head-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-top: 4px solid var(--gold);
        border-radius: var(--radius-lg);
        padding: clamp(18px, 3vw, 28px);
        box-shadow: var(--shadow-sm);
        margin-bottom: 24px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .album-head-info h1 {
        margin: 0 0 6px;
        font-size: clamp(1.5rem, 3.6vw, 2.1rem);
    }

    .album-head-info p {
        margin: 0;
        color: var(--text-muted);
        font-size: 14.5px;
    }

    /* Upload Dropzone */
    .upload-card {
        background: var(--surface-muted);
        border: 2px dashed var(--border-strong);
        border-radius: var(--radius-lg);
        padding: clamp(18px, 3vw, 26px);
        margin-bottom: 28px;
        text-align: center;
        transition: border-color 0.2s, background 0.2s;
    }

    .upload-card:hover {
        border-color: var(--gold);
        background: var(--gold-soft);
    }

    .upload-form {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .upload-file-input {
        flex: 1 1 240px;
        max-width: 420px;
        background: var(--surface);
    }

    .upload-hint {
        margin: 10px 0 0;
        font-size: 12.5px;
        color: var(--text-muted);
    }

    /* Grid Foto Responsif */
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }

    .photo-card {
        position: relative;
        aspect-ratio: 1 / 1;
        border-radius: 14px;
        overflow: hidden;
        background: var(--espresso);
        box-shadow: var(--shadow-xs);
        border: 1px solid var(--border);
    }

    .photo-trigger {
        display: block;
        width: 100%;
        height: 100%;
        padding: 0;
        border: none;
        background: none;
        cursor: pointer;
    }

    .photo-trigger img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }

    .photo-card:hover .photo-trigger img {
        transform: scale(1.06);
    }

    .photo-delete-form {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 10;
    }

    .btn-delete-photo {
        background: rgba(185, 28, 28, 0.88);
        color: #FFFFFF;
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: 999px;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        opacity: 0.92;
        transition: transform 0.15s, background 0.2s, opacity 0.2s;
    }

    .btn-delete-photo:hover {
        background: #991B1B;
        transform: scale(1.08);
        opacity: 1;
    }

    @media (hover: hover) {
        .btn-delete-photo {
            opacity: 0;
        }
        .photo-card:hover .btn-delete-photo,
        .btn-delete-photo:focus {
            opacity: 1;
        }
    }

    .empty-photos {
        grid-column: 1 / -1;
        text-align: center;
        padding: 48px 20px;
        background: var(--surface);
        border: 1.5px dashed var(--border-strong);
        border-radius: var(--radius-lg);
        color: var(--text-muted);
    }

    /* Full-Screen Interactive Lightbox */
    .km-lightbox {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(16, 10, 7, 0.94);
        backdrop-filter: blur(8px);
        z-index: 1200;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .km-lightbox.open {
        display: flex;
    }

    .lightbox-top {
        position: absolute;
        top: 14px;
        left: 16px;
        right: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: #F8F1E4;
        z-index: 5;
    }

    .lightbox-counter {
        font-size: 13.5px;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.14);
        padding: 6px 14px;
        border-radius: var(--radius-pill);
    }

    .lightbox-controls {
        display: flex;
        gap: 8px;
    }

    .lightbox-icon-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.22);
        background: rgba(255, 255, 255, 0.12);
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        font-size: 20px;
    }

    .lightbox-icon-btn:hover {
        background: rgba(200, 160, 53, 0.45);
    }

    .lightbox-stage {
        position: relative;
        width: 100%;
        max-width: 1040px;
        max-height: 78dvh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .lightbox-img {
        max-width: 100%;
        max-height: 78dvh;
        border-radius: 12px;
        object-fit: contain;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }

    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 46px;
        height: 46px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.25);
        background: rgba(24, 15, 10, 0.72);
        color: #FFFFFF;
        font-size: 22px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .lightbox-nav.prev { left: 10px; }
    .lightbox-nav.next { right: 10px; }

    @media (max-width: 900px) {
        .photo-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 600px) {
        .photo-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .upload-form .km-btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="km-container album-show-wrap">
    <div class="album-top-nav">
        <a href="{{ route('gallery.index') }}" class="km-btn km-btn-outline km-btn-sm">&larr; Kembali ke Daftar Album</a>
    </div>

    <div class="album-head-card">
        <div class="album-head-info">
            <span class="km-badge km-badge-gold" style="margin-bottom:8px;">Album Kenangan</span>
            <h1>{{ $gallery->name }}</h1>
            @if($gallery->description)
                <p>{{ $gallery->description }}</p>
            @endif
        </div>
        <span class="km-badge km-badge-dark" style="padding:7px 15px;font-size:13px;">
            {{ $gallery->photos->count() }} Foto
        </span>
    </div>

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

    <!-- Box Upload Multi-Foto (Khusus Admin) -->
    @if($isAdmin)
        <div class="upload-card">
            <form action="{{ route('gallery.storePhoto', $gallery->id) }}" method="POST" enctype="multipart/form-data" class="upload-form">
                @csrf
                <input type="file" id="multiPhotoInput" name="images[]" accept="image/*" multiple required class="km-input upload-file-input" style="padding:9px 12px;">
                <button type="submit" class="km-btn km-btn-gold">Unggah Foto</button>
            </form>
            <p id="selectedFilesHint" class="upload-hint">Bisa memilih lebih dari satu foto sekaligus (JPG, PNG, WEBP — maks. 5MB per foto).</p>
        </div>
    @endif

    <!-- Grid Foto -->
    <div class="photo-grid">
        @forelse($gallery->photos as $index => $photo)
            @php $photoUrl = $photo->url; @endphp
            <div class="photo-card">
                @if($isAdmin)
                    <form action="{{ route('gallery.destroyPhoto', $photo->id) }}"
                          method="POST"
                          class="photo-delete-form"
                          onsubmit="return confirm('Hapus foto ini dari album?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete-photo" title="Hapus Foto" aria-label="Hapus Foto">&times;</button>
                    </form>
                @endif

                <button type="button" class="photo-trigger" onclick="openLightbox({{ $index }})" aria-label="Lihat foto {{ $index + 1 }}">
                    <img src="{{ $photoUrl }}" alt="Foto {{ $gallery->name }} #{{ $index + 1 }}" loading="lazy">
                </button>
            </div>
        @empty
            <div class="empty-photos">
                <h3 style="margin-top:0;">Album Ini Masih Kosong</h3>
                <p style="margin:0;">Pilih foto dari HP atau komputer di kotak unggah di atas untuk mulai mengisi album ini.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- FULLSCREEN LIGHTBOX -->
<div id="photoLightbox" class="km-lightbox" role="dialog" aria-modal="true" aria-label="Pratinjau Foto">
    <div class="lightbox-top">
        <span id="lightboxCounter" class="lightbox-counter">1 / 1</span>
        <div class="lightbox-controls">
            <a id="lightboxDownload" href="#" download class="lightbox-icon-btn" title="Unduh Foto" aria-label="Unduh Foto">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            </a>
            <button type="button" class="lightbox-icon-btn" onclick="closeLightbox()" aria-label="Tutup">&times;</button>
        </div>
    </div>

    <div class="lightbox-stage">
        <button type="button" id="lbPrev" class="lightbox-nav prev" onclick="stepLightbox(-1)" aria-label="Foto sebelumnya">&lsaquo;</button>
        <img id="lightboxImg" src="" alt="Foto diperbesar" class="lightbox-img">
        <button type="button" id="lbNext" class="lightbox-nav next" onclick="stepLightbox(1)" aria-label="Foto berikutnya">&rsaquo;</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const GALLERY_PHOTOS = @json($gallery->photos->map(fn ($p) => $p->url)->values());
    let lbIndex = 0;

    const multiInput = document.getElementById('multiPhotoInput');
    const filesHint = document.getElementById('selectedFilesHint');
    if (multiInput && filesHint) {
        multiInput.addEventListener('change', () => {
            const count = multiInput.files ? multiInput.files.length : 0;
            if (count > 0) {
                filesHint.textContent = `${count} foto siap diunggah. Klik tombol "Unggah Foto".`;
            }
        });
    }

    function openLightbox(index) {
        if (!GALLERY_PHOTOS.length) return;
        lbIndex = index;
        renderLightbox();
        document.getElementById('photoLightbox').classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('photoLightbox').classList.remove('open');
        document.body.style.overflow = '';
    }

    function stepLightbox(delta) {
        if (!GALLERY_PHOTOS.length) return;
        lbIndex = (lbIndex + delta + GALLERY_PHOTOS.length) % GALLERY_PHOTOS.length;
        renderLightbox();
    }

    function renderLightbox() {
        const url = GALLERY_PHOTOS[lbIndex];
        document.getElementById('lightboxImg').src = url;
        document.getElementById('lightboxDownload').href = url;
        document.getElementById('lightboxCounter').textContent = `${lbIndex + 1} / ${GALLERY_PHOTOS.length}`;
        const showNav = GALLERY_PHOTOS.length > 1;
        document.getElementById('lbPrev').style.display = showNav ? 'flex' : 'none';
        document.getElementById('lbNext').style.display = showNav ? 'flex' : 'none';
    }

    // Keyboard & Swipe Support di HP
    document.addEventListener('keydown', (e) => {
        const lb = document.getElementById('photoLightbox');
        if (!lb || !lb.classList.contains('open')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') stepLightbox(-1);
        if (e.key === 'ArrowRight') stepLightbox(1);
    });

    (function initSwipe() {
        const lb = document.getElementById('photoLightbox');
        if (!lb) return;
        let touchStartX = 0;
        lb.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        lb.addEventListener('touchend', (e) => {
            const diff = e.changedTouches[0].screenX - touchStartX;
            if (Math.abs(diff) > 45) {
                stepLightbox(diff > 0 ? -1 : 1);
            }
        }, { passive: true });
        lb.addEventListener('click', (e) => {
            if (e.target === lb) closeLightbox();
        });
    })();
</script>
@endpush
