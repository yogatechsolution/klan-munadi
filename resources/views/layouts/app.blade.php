<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#261710">
    <title>@yield('title', 'Kasan Munadi — Silsilah & Portal Keluarga')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;1,400&family=Playfair+Display:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/klan-munadi.css') }}">
    @stack('styles')
</head>
<body>
    @php
        $isHome = request()->routeIs('home');
        $isTree = request()->routeIs('family.index') || request()->routeIs('family.edit');
    @endphp

    <!-- NAVBAR UTAMA -->
    <nav id="mainNavbar" class="km-navbar {{ $isHome ? 'transparent' : 'solid' }}" aria-label="Navigasi Utama">
        <a href="{{ route('home') }}" class="km-brand">
            <span class="km-brand-crest" aria-hidden="true">KM</span>
            <span class="km-brand-text">
                <span class="km-brand-subtitle">Bagan Utama Klan Munadi</span>
                <span class="km-brand-title">KASAN MUNADI</span>
            </span>
        </a>

        <!-- Menu Desktop -->
        <div class="km-nav-desktop">
            <a href="{{ route('home') }}" class="km-nav-link {{ $isHome ? 'active' : '' }}">Beranda</a>
            <a href="{{ route('family.index') }}" class="km-nav-link {{ $isTree ? 'active' : '' }}">Silsilah</a>
            <a href="{{ route('family.map') }}" class="km-nav-link {{ request()->routeIs('family.map') ? 'active' : '' }}">Persebaran</a>
            <a href="{{ route('gallery.index') }}" class="km-nav-link {{ request()->routeIs('gallery.*') ? 'active' : '' }}">Galeri</a>
            <a href="{{ route('story') }}" class="km-nav-link {{ request()->routeIs('story') ? 'active' : '' }}">Our Story</a>
            <a href="{{ route('jadwal') }}" class="km-nav-link {{ request()->routeIs('jadwal') ? 'active' : '' }}">Jadwal</a>
            <a href="{{ route('contact') }}" class="km-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Kontak</a>

            <div class="km-nav-actions">
                @if($isAdmin)
                    <a href="{{ route('settings.index') }}"
                       class="km-icon-btn {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                       title="Pengaturan Website"
                       aria-label="Pengaturan Website">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c.26.6.85 1 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                    </a>
                    <a href="{{ route('family.create') }}" class="km-nav-cta">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Tambah Anggota
                    </a>
                    <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit" class="km-icon-btn" title="Keluar Mode Admin" aria-label="Keluar Mode Admin">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('admin.login') }}" class="km-nav-link {{ request()->routeIs('admin.login') ? 'active' : '' }}" style="border:1px solid currentColor;opacity:0.9;">
                        Admin
                    </a>
                @endif
            </div>
        </div>

        <!-- Kontrol Mobile (Hamburger + Quick Action) -->
        <div class="km-mobile-controls">
            @if($isAdmin)
                <a href="{{ route('family.create') }}" class="km-icon-btn" title="Tambah Anggota" aria-label="Tambah Anggota Baru" style="width:42px;height:42px;border-radius:12px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                </a>
            @endif
            <button type="button" id="mobileMenuBtn" class="km-hamburger" aria-label="Buka Menu Navigasi" aria-expanded="false" aria-controls="mobileDrawer">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
    </nav>

    <!-- MOBILE SLIDE-OVER DRAWER -->
    <div id="drawerBackdrop" class="km-drawer-backdrop" aria-hidden="true"></div>
    <aside id="mobileDrawer" class="km-drawer" aria-label="Menu Mobile" aria-hidden="true">
        <div class="km-drawer-header">
            <div class="km-brand">
                <span class="km-brand-crest">KM</span>
                <span class="km-brand-text">
                    <span class="km-brand-subtitle">Klan Munadi</span>
                    <span class="km-brand-title" style="font-size:17px;">KASAN MUNADI</span>
                </span>
            </div>
            <button type="button" id="closeDrawerBtn" class="km-drawer-close" aria-label="Tutup Menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <nav class="km-drawer-nav">
            <a href="{{ route('home') }}" class="km-drawer-link {{ $isHome ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                Beranda
            </a>
            <a href="{{ route('family.index') }}" class="km-drawer-link {{ $isTree ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="3"></circle><circle cx="5" cy="19" r="3"></circle><circle cx="19" cy="19" r="3"></circle><path d="M12 8v4m0 0-7 4m7-4 7 4"></path></svg>
                Pohon Silsilah
            </a>
            <a href="{{ route('family.map') }}" class="km-drawer-link {{ request()->routeIs('family.map') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                Peta Persebaran
            </a>
            <a href="{{ route('gallery.index') }}" class="km-drawer-link {{ request()->routeIs('gallery.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                Galeri Foto
            </a>
            <a href="{{ route('story') }}" class="km-drawer-link {{ request()->routeIs('story') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                Our Story
            </a>
            <a href="{{ route('jadwal') }}" class="km-drawer-link {{ request()->routeIs('jadwal') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Jadwal & Ulang Tahun
            </a>
            <a href="{{ route('contact') }}" class="km-drawer-link {{ request()->routeIs('contact') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                Kontak Pengurus
            </a>
            @if($isAdmin)
                <a href="{{ route('settings.index') }}" class="km-drawer-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c.26.6.85 1 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    Pengaturan Website
                </a>
            @endif
        </nav>

        <div class="km-drawer-footer">
            @if($isAdmin)
                <a href="{{ route('family.create') }}" class="km-btn km-btn-gold" style="width:100%;">
                    + Tambah Anggota Keluarga
                </a>
                <form action="{{ route('admin.logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="km-btn km-btn-outline" style="width:100%;">Keluar Mode Admin</button>
                </form>
            @else
                <a href="{{ route('admin.login') }}" class="km-btn km-btn-dark" style="width:100%;">
                    Masuk Mode Admin
                </a>
            @endif
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <main>
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="km-footer">
        <div class="km-container">
            <div class="km-footer-inner">
                <div>
                    <h3 class="km-footer-brand">TRAH BANI KASAN MUNADI</h3>
                    <p class="km-footer-desc">
                        Portal silsilah digital keluarga besar Kasan Munadi — menyambung tali silaturahmi, merawat sejarah, dan meneruskan nilai luhur lintas generasi.
                    </p>
                </div>
                <div>
                    <h4 class="km-footer-heading">Jelajahi</h4>
                    <ul class="km-footer-links">
                        <li><a href="{{ route('family.index') }}">Pohon Silsilah</a></li>
                        <li><a href="{{ route('family.map') }}">Peta Persebaran</a></li>
                        <li><a href="{{ route('gallery.index') }}">Galeri Kenangan</a></li>
                        <li><a href="{{ route('story') }}">Kisah & Biografi</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="km-footer-heading">Keluarga & Pengelolaan</h4>
                    <ul class="km-footer-links">
                        <li><a href="{{ route('jadwal') }}">Jadwal & Ulang Tahun</a></li>
                        <li><a href="{{ route('contact') }}">Kontak Pengurus</a></li>
                        @if($isAdmin)
                            <li><a href="{{ route('family.create') }}">Tambah Anggota Baru</a></li>
                            <li><a href="{{ route('settings.index') }}">Pengaturan Website</a></li>
                        @else
                            <li><a href="{{ route('admin.login') }}">Login Admin Pengurus</a></li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="km-footer-bottom">
                <span>&copy; {{ date('Y') }} Keluarga Besar Kasan Munadi. Dirawat bersama lintas generasi.</span>
                <span>Bagan Silsilah Digital</span>
            </div>
        </div>
    </footer>

    <!-- NAVIGASI BAWAH KHUSUS HP (<= 768px) -->
    <nav class="km-bottom-nav" aria-label="Navigasi Cepat Mobile">
        <a href="{{ route('home') }}" class="km-bottom-item {{ $isHome ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            <span>Beranda</span>
        </a>
        <a href="{{ route('family.index') }}" class="km-bottom-item {{ $isTree ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="3"></circle><circle cx="5" cy="19" r="3"></circle><circle cx="19" cy="19" r="3"></circle><path d="M12 8v4m0 0-7 4m7-4 7 4"></path></svg>
            <span>Silsilah</span>
        </a>
        <a href="{{ route('family.map') }}" class="km-bottom-item {{ request()->routeIs('family.map') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            <span>Peta</span>
        </a>
        <a href="{{ route('gallery.index') }}" class="km-bottom-item {{ request()->routeIs('gallery.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
            <span>Galeri</span>
        </a>
        <a href="{{ route('story') }}" class="km-bottom-item {{ request()->routeIs('story') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
            <span>Kisah</span>
        </a>
    </nav>

    <script>
        (function () {
            const navbar = document.getElementById('mainNavbar');
            if (navbar && navbar.classList.contains('transparent')) {
                const onScroll = () => {
                    if (window.scrollY > 12) {
                        navbar.classList.add('scrolled');
                    } else {
                        navbar.classList.remove('scrolled');
                    }
                };
                window.addEventListener('scroll', onScroll, { passive: true });
                onScroll();
            }

            const menuBtn = document.getElementById('mobileMenuBtn');
            const closeBtn = document.getElementById('closeDrawerBtn');
            const drawer = document.getElementById('mobileDrawer');
            const backdrop = document.getElementById('drawerBackdrop');

            function openDrawer() {
                drawer.classList.add('open');
                backdrop.classList.add('open');
                drawer.setAttribute('aria-hidden', 'false');
                menuBtn && menuBtn.setAttribute('aria-expanded', 'true');
                document.body.style.overflow = 'hidden';
            }

            function closeDrawer() {
                drawer.classList.remove('open');
                backdrop.classList.remove('open');
                drawer.setAttribute('aria-hidden', 'true');
                menuBtn && menuBtn.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }

            if (menuBtn) menuBtn.addEventListener('click', openDrawer);
            if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
            if (backdrop) backdrop.addEventListener('click', closeDrawer);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && drawer.classList.contains('open')) {
                    closeDrawer();
                }
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
