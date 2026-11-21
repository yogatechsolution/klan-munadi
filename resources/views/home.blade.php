@extends('layouts.app')

@section('title', 'Kasan Munadi — Portal Silsilah Keluarga Besar')

@push('styles')
<style>
    /* ================================================================
       HERO SECTION — CINEMATIC JAVANESE HERITAGE
       ================================================================ */
    .km-hero {
        position: relative;
        min-height: 100svh;
        display: flex;
        align-items: center;
        background-image: url('{{ $heroImage }}');
        background-position: 28% center;
        background-repeat: no-repeat;
        background-size: cover;
        color: #FFFFFF;
        padding: calc(var(--nav-height) + 40px) 0 110px;
        overflow: hidden;
    }

    .km-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(
            95deg,
            rgba(22, 13, 9, 0.14) 0%,
            rgba(22, 13, 9, 0.42) 38%,
            rgba(22, 13, 9, 0.80) 72%,
            rgba(22, 13, 9, 0.88) 100%
        );
        z-index: 1;
    }

    .km-hero::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 140px;
        background: linear-gradient(180deg, rgba(22, 13, 9, 0) 0%, rgba(22, 13, 9, 0.72) 100%);
        z-index: 1;
    }

    .km-hero-inner {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: flex-end;
    }

    .km-hero-content {
        width: 100%;
        max-width: 580px;
        text-align: left;
    }

    .km-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: var(--radius-pill);
        background: rgba(200, 160, 53, 0.18);
        border: 1px solid rgba(223, 189, 92, 0.42);
        color: #F3D98B;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.8px;
        margin-bottom: 18px;
        backdrop-filter: blur(6px);
    }

    .km-hero-title {
        font-family: var(--font-display);
        font-size: clamp(2.4rem, 6vw, 4.4rem);
        color: #FCF7EC;
        letter-spacing: 2.5px;
        margin: 0 0 12px 0;
        font-weight: 600;
        line-height: 1.08;
        text-shadow: 0 4px 24px rgba(0, 0, 0, 0.45);
    }

    .km-hero-subtitle {
        font-family: var(--font-serif);
        font-style: italic;
        font-size: clamp(1.08rem, 2.3vw, 1.38rem);
        color: #E4C15E;
        margin: 0 0 26px 0;
        letter-spacing: 0.4px;
    }

    .km-hero-desc {
        font-family: var(--font-serif);
        font-size: clamp(0.95rem, 1.8vw, 1.06rem);
        line-height: 1.8;
        color: rgba(248, 241, 228, 0.90);
        border-left: 3px solid var(--gold);
        padding-left: 18px;
        margin-bottom: 32px;
    }

    .km-hero-desc p {
        margin: 0 0 10px 0;
    }

    .km-hero-desc p:last-child {
        margin-bottom: 0;
    }

    .km-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .km-btn-hero-ghost {
        background: rgba(255, 255, 255, 0.12);
        color: #FCF7EC;
        border: 1px solid rgba(255, 255, 255, 0.34);
        backdrop-filter: blur(8px);
    }

    .km-btn-hero-ghost:hover {
        background: rgba(255, 255, 255, 0.22);
        border-color: var(--gold-bright);
        color: #FFFFFF;
    }

    /* ================================================================
       FLOATING FAMILY STATS BAR
       ================================================================ */
    .km-stats-wrap {
        position: relative;
        z-index: 5;
        margin-top: -54px;
        margin-bottom: 24px;
    }

    .km-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    .km-stat-item {
        padding: 22px 18px;
        text-align: center;
        border-right: 1px solid var(--border);
        text-decoration: none;
        transition: background 0.2s ease;
    }

    .km-stat-item:last-child {
        border-right: none;
    }

    .km-stat-item:hover {
        background: var(--surface-muted);
    }

    .km-stat-value {
        font-family: var(--font-display);
        font-size: clamp(1.6rem, 3vw, 2.2rem);
        font-weight: 700;
        color: var(--espresso);
        line-height: 1.1;
    }

    .km-stat-label {
        font-size: 12.5px;
        color: var(--text-muted);
        margin-top: 5px;
        font-weight: 500;
    }

    /* ================================================================
       HERITAGE STORY SECTION (OUR ROOTS & THE LEGACY)
       ================================================================ */
    .km-heritage {
        padding: clamp(44px, 6vw, 76px) 0;
        background: var(--bg);
    }

    .km-section-head {
        text-align: center;
        max-width: 660px;
        margin: 0 auto clamp(32px, 5vw, 50px);
    }

    .km-ornament {
        color: var(--gold-dark);
        font-size: 20px;
        letter-spacing: 6px;
        margin-bottom: 10px;
    }

    .km-section-head h2 {
        font-size: clamp(1.75rem, 3.8vw, 2.5rem);
        margin: 0 0 10px 0;
    }

    .km-section-head p {
        color: var(--text-muted);
        margin: 0;
        font-family: var(--font-serif);
    }

    .km-story-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: clamp(22px, 4vw, 38px);
    }

    .km-story-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .km-story-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
        border-color: var(--gold-border);
    }

    .km-story-img-wrap {
        position: relative;
        aspect-ratio: 16 / 10;
        overflow: hidden;
        background: var(--espresso);
    }

    .km-story-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.45s ease;
    }

    .km-story-card:hover .km-story-img-wrap img {
        transform: scale(1.04);
    }

    .km-story-body {
        padding: clamp(22px, 3vw, 32px);
        display: flex;
        flex-direction: column;
        flex: 1;
        text-align: center;
        align-items: center;
    }

    .km-story-body h3 {
        font-size: clamp(1.45rem, 2.8vw, 1.9rem);
        margin: 0 0 12px 0;
        color: var(--espresso);
    }

    .km-story-body p {
        font-family: var(--font-serif);
        color: var(--text-muted);
        line-height: 1.75;
        margin: 0 0 24px 0;
        flex: 1;
    }

    /* ================================================================
       PORTAL QUICK ACCESS FEATURES
       ================================================================ */
    .km-portal-section {
        padding: clamp(44px, 6vw, 72px) 0;
        background: var(--bg-warm);
        border-top: 1px solid var(--border);
    }

    .km-feature-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }

    .km-feature-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 24px 20px;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        gap: 12px;
        transition: all 0.22s ease;
        box-shadow: var(--shadow-xs);
    }

    .km-feature-card:hover {
        transform: translateY(-4px);
        border-color: var(--gold);
        box-shadow: var(--shadow-md);
    }

    .km-feature-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: var(--gold-soft);
        color: var(--gold-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .km-feature-card h3 {
        font-size: 1.12rem;
        margin: 0;
        color: var(--espresso);
    }

    .km-feature-card p {
        font-size: 13.5px;
        color: var(--text-muted);
        margin: 0;
        line-height: 1.55;
        flex: 1;
    }

    .km-feature-cta {
        font-size: 13px;
        font-weight: 700;
        color: var(--gold-dark);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    /* ================================================================
       RESPONSIVE MOBILE TUNING
       ================================================================ */
    @media (max-width: 960px) {
        .km-hero {
            background-position: 26% top;
            align-items: flex-end;
            min-height: 88svh;
            padding: calc(var(--nav-height) + 90px) 0 88px;
        }

        .km-hero::before {
            background: linear-gradient(
                180deg,
                rgba(22, 13, 9, 0.28) 0%,
                rgba(22, 13, 9, 0.50) 45%,
                rgba(22, 13, 9, 0.88) 82%,
                rgba(22, 13, 9, 0.94) 100%
            );
        }

        .km-hero-inner {
            justify-content: flex-start;
        }

        .km-feature-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .km-stats-wrap {
            margin-top: -38px;
        }

        .km-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .km-stat-item:nth-child(1),
        .km-stat-item:nth-child(2) {
            border-bottom: 1px solid var(--border);
        }

        .km-stat-item:nth-child(2n) {
            border-right: none;
        }

        .km-story-grid {
            grid-template-columns: 1fr;
        }

        .km-hero-actions .km-btn {
            flex: 1 1 180px;
        }
    }

    @media (max-width: 480px) {
        .km-feature-grid {
            grid-template-columns: 1fr;
        }

        .km-stat-item {
            padding: 16px 12px;
        }
    }
</style>
@endpush

@section('content')

@if(session('success'))
    <div style="position:fixed;top:calc(var(--nav-height) + 12px);right:16px;left:16px;max-width:460px;margin:0 auto;z-index:950;">
        <div class="km-alert km-alert-success" style="box-shadow:var(--shadow-md);">
            <span>{{ session('success') }}</span>
            <button type="button" class="km-alert-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    </div>
@endif

<!-- 1. HERO SECTION -->
<section class="km-hero">
    <div class="km-container km-hero-inner">
        <div class="km-hero-content">
            <span class="km-hero-eyebrow">Trah &amp; Silsilah Keluarga Besar</span>
            <h1 class="km-hero-title">KASAN MUNADI</h1>
            <p class="km-hero-subtitle">A Legacy That Connects Generations</p>

            <div class="km-hero-desc">
                <p>Dari awal yang sederhana lahirlah sebuah warisan nilai yang terus hidup di setiap generasi.</p>
                <p>Kasan Munadi bukan sekadar nama, melainkan ikatan antara masa lalu, masa kini, dan masa depan keluarga besar kita.</p>
            </div>

            <div class="km-hero-actions">
                <a href="{{ route('family.index') }}" class="km-btn km-btn-gold">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="3"></circle><circle cx="5" cy="19" r="3"></circle><circle cx="19" cy="19" r="3"></circle><path d="M12 8v4m0 0-7 4m7-4 7 4"></path></svg>
                    Jelajahi Pohon Silsilah
                </a>
                <a href="{{ route('story') }}" class="km-btn km-btn-hero-ghost">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    Baca Our Story
                </a>
            </div>
        </div>
    </div>
</section>

<!-- 2. RINGKASAN STATISTIK KELUARGA -->
<div class="km-container km-stats-wrap">
    <div class="km-stats-grid">
        <a href="{{ route('family.index') }}" class="km-stat-item">
            <div class="km-stat-value">{{ $stats['members'] }}</div>
            <div class="km-stat-label">Anggota Tercatat</div>
        </a>
        <a href="{{ route('family.index') }}" class="km-stat-item">
            <div class="km-stat-value">{{ $stats['generations'] }}</div>
            <div class="km-stat-label">Generasi Trah</div>
        </a>
        <a href="{{ route('family.map') }}" class="km-stat-item">
            <div class="km-stat-value">{{ $stats['mapped'] }}</div>
            <div class="km-stat-label">Lokasi Terpetakan</div>
        </a>
        <a href="{{ route('gallery.index') }}" class="km-stat-item">
            <div class="km-stat-value">{{ $stats['galleries'] }}</div>
            <div class="km-stat-label">Album Kenangan</div>
        </a>
    </div>
</div>

<!-- 3. OUR ROOTS & THE LEGACY -->
<section class="km-heritage">
    <div class="km-container">
        <div class="km-section-head">
            <div class="km-ornament">~ ❖ ~</div>
            <h2>Akar &amp; Warisan Keluarga</h2>
            <p>Mengenal kembali asal-usul, nilai kebersamaan, dan perjalanan panjang keluarga besar Bani Kasan Munadi.</p>
        </div>

        <div class="km-story-grid">
            <article class="km-story-card">
                <div class="km-story-img-wrap">
                    <img src="{{ $rootsImage }}" alt="Our Roots — Asal-usul Klan Munadi" loading="lazy">
                </div>
                <div class="km-story-body">
                    <h3>Our Roots</h3>
                    <p>Semuanya bermula dari sebuah desa yang bersahaja di tanah Jawa, tempat Mbah Kasan Munadi membangun bukan sekadar rumah, melainkan ruang tumbuhnya cerita, nilai luhur, dan tradisi keluarga.</p>
                    <a href="{{ route('story') }}" class="km-btn km-btn-dark">Baca Kisah Lengkap</a>
                </div>
            </article>

            <article class="km-story-card">
                <div class="km-story-img-wrap">
                    <img src="{{ $legacyImage }}" alt="The Legacy — Kebersamaan Keluarga Besar Munadi" loading="lazy">
                </div>
                <div class="km-story-body">
                    <h3>The Legacy</h3>
                    <p>Melalui keteladanan dan petuah leluhur, kita mewarisi nilai keimanan, kehormatan, serta kerukunan yang terus menyatukan seluruh anak, cucu, dan cicit hingga hari ini.</p>
                    <a href="{{ route('family.index') }}" class="km-btn km-btn-gold">Lihat Bagan Silsilah</a>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- 4. AKSES CEPAT FITUR PORTAL -->
<section class="km-portal-section">
    <div class="km-container">
        <div class="km-section-head">
            <span class="km-page-eyebrow">Fitur Portal Keluarga</span>
            <h2>Semua Dalam Satu Tempat</h2>
            <p>Dirancang agar mudah dibuka dari HP maupun komputer oleh seluruh anggota keluarga lintas generasi.</p>
        </div>

        <div class="km-feature-grid">
            <a href="{{ route('family.index') }}" class="km-feature-card">
                <span class="km-feature-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="3"></circle><circle cx="5" cy="19" r="3"></circle><circle cx="19" cy="19" r="3"></circle><path d="M12 8v4m0 0-7 4m7-4 7 4"></path></svg>
                </span>
                <h3>Pohon Silsilah</h3>
                <p>Telusuri garis keturunan, cari nama keluarga, dan lihat detail biodata tiap anggota.</p>
                <span class="km-feature-cta">Buka Silsilah &rarr;</span>
            </a>

            <a href="{{ route('family.map') }}" class="km-feature-card">
                <span class="km-feature-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </span>
                <h3>Peta Persebaran</h3>
                <p>Lihat domisili anggota keluarga besar yang tersebar di berbagai kota di Indonesia.</p>
                <span class="km-feature-cta">Lihat Peta &rarr;</span>
            </a>

            <a href="{{ route('gallery.index') }}" class="km-feature-card">
                <span class="km-feature-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                </span>
                <h3>Galeri Kenangan</h3>
                <p>Simpan dan bagikan album foto reuni, lebaran, serta momen kebersamaan keluarga.</p>
                <span class="km-feature-cta">Buka Galeri &rarr;</span>
            </a>

            <a href="{{ route('jadwal') }}" class="km-feature-card">
                <span class="km-feature-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                </span>
                <h3>Jadwal &amp; Ulang Tahun</h3>
                <p>Pantau agenda pertemuan keluarga dan pengingat hari ulang tahun anggota keluarga.</p>
                <span class="km-feature-cta">Lihat Jadwal &rarr;</span>
            </a>
        </div>
    </div>
</section>

@endsection
