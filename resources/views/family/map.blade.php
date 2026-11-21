@extends('layouts.app')

@section('title', 'Peta Persebaran Keluarga — Klan Munadi')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .map-page-wrap {
        padding: 24px 0 48px;
    }

    .map-stats-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 14px;
    }

    .map-layout {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 18px;
        align-items: stretch;
    }

    .map-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
    }

    #indonesiaMap {
        width: 100%;
        height: 66vh;
        min-height: 430px;
        z-index: 1;
    }

    .map-sidebar {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-direction: column;
        max-height: 66vh;
        min-height: 430px;
        overflow: hidden;
    }

    .sidebar-head {
        padding: 16px;
        border-bottom: 1px solid var(--border);
        background: var(--surface-muted);
    }

    .sidebar-head h2 {
        font-size: 1.12rem;
        margin: 0 0 10px 0;
    }

    .sidebar-list {
        overflow-y: auto;
        flex: 1;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .map-member-item {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: var(--surface);
        cursor: pointer;
        text-align: left;
        width: 100%;
        font-family: var(--font-ui);
        transition: all 0.18s ease;
    }

    .map-member-item:hover,
    .map-member-item.active {
        border-color: var(--gold);
        background: var(--gold-soft);
    }

    .map-member-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--gold);
        flex-shrink: 0;
    }

    .map-member-info {
        min-width: 0;
        flex: 1;
    }

    .map-member-name {
        font-weight: 700;
        font-size: 14px;
        color: var(--espresso);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .map-member-addr {
        font-size: 12px;
        color: var(--text-muted);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .sidebar-empty {
        padding: 32px 18px;
        text-align: center;
        color: var(--text-muted);
        font-size: 14px;
    }

    /* Popup Leaflet yang Elegan */
    .leaflet-popup-content-wrapper {
        border-radius: 14px;
        box-shadow: var(--shadow-md);
        padding: 4px;
    }

    .leaflet-popup-content {
        margin: 12px 14px;
        text-align: center;
        font-family: var(--font-ui);
        min-width: 190px;
    }

    .popup-avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--gold);
        margin-bottom: 8px;
    }

    .popup-name {
        font-family: var(--font-display);
        font-weight: 700;
        color: var(--espresso);
        font-size: 16px;
        margin-bottom: 4px;
    }

    .popup-address {
        font-size: 12.5px;
        color: var(--text-muted);
        margin-bottom: 12px;
        line-height: 1.45;
    }

    .popup-actions {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .popup-btn {
        display: block;
        background: var(--gold);
        color: #FFFFFF !important;
        padding: 7px 12px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 12.5px;
        font-weight: 600;
    }

    .popup-btn:hover {
        background: var(--gold-dark);
    }

    .popup-btn-sec {
        display: block;
        background: var(--bg-warm);
        color: var(--espresso) !important;
        padding: 6px 12px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 12px;
        font-weight: 600;
    }

    @media (max-width: 960px) {
        .map-layout {
            grid-template-columns: 1fr;
        }

        #indonesiaMap {
            height: 52vh;
            min-height: 320px;
        }

        .map-sidebar {
            max-height: 380px;
            min-height: 220px;
        }
    }
</style>
@endpush

@section('content')
<div class="km-page-header">
    <div class="km-container">
        <span class="km-page-eyebrow">Domisili Lintas Kota</span>
        <h1>Persebaran Bani Munadi</h1>
        <p>Peta interaktif lokasi tempat tinggal keluarga besar Kasan Munadi. Ketuk pin atau pilih nama di daftar untuk menyorot lokasi.</p>
        <div class="map-stats-row">
            <span class="km-badge km-badge-gold">{{ $members->count() }} dari {{ $totalMembers }} Anggota Terpetakan</span>
            <a href="{{ route('family.index') }}" class="km-btn km-btn-outline km-btn-sm">Buka Pohon Silsilah</a>
        </div>
    </div>
</div>

<div class="km-container map-page-wrap">
    <div class="map-layout">
        <div class="map-card">
            <div id="indonesiaMap"></div>
        </div>

        <aside class="map-sidebar" aria-label="Daftar Anggota Terpetakan">
            <div class="sidebar-head">
                <h2>Daftar Domisili ({{ $members->count() }})</h2>
                <input type="text" id="mapFilterInput" class="km-input" placeholder="Cari nama atau kota..." style="padding:9px 12px;font-size:14px;">
            </div>

            <div id="sidebarList" class="sidebar-list">
                @forelse($members as $m)
                    <button type="button"
                            class="map-member-item"
                            data-id="{{ $m->id }}"
                            data-search="{{ mb_strtolower($m->name . ' ' . ($m->address ?? '')) }}"
                            onclick="focusMemberPin({{ $m->id }})">
                        <img src="{{ $m->avatar_url }}" alt="{{ $m->name }}" class="map-member-avatar" loading="lazy">
                        <div class="map-member-info">
                            <div class="map-member-name">{{ $m->name }}</div>
                            <div class="map-member-addr">{{ $m->address ?: 'Koordinat tercatat (alamat belum detail)' }}</div>
                        </div>
                    </button>
                @empty
                    <div class="sidebar-empty">
                        <p style="margin:0 0 14px;">Belum ada anggota keluarga yang memiliki titik lokasi peta.</p>
                        <a href="{{ route('family.index') }}" class="km-btn km-btn-gold km-btn-sm">Pilih Anggota untuk Isi Lokasi</a>
                    </div>
                @endforelse
            </div>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const MAP_MEMBERS = @json($mapMembers);
    const MARKERS_BY_ID = {};
    let leafletMap = null;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }

    function focusMemberPin(id) {
        const item = MAP_MEMBERS.find(m => m.id === id);
        const marker = MARKERS_BY_ID[id];
        if (!item || !marker || !leafletMap) return;

        document.querySelectorAll('.map-member-item').forEach(el => {
            el.classList.toggle('active', Number(el.dataset.id) === id);
        });

        leafletMap.flyTo([item.latitude, item.longitude], 13, { duration: 0.55 });
        leafletMap.once('moveend', () => marker.openPopup());

        // Di layar HP, scroll halus kembali ke peta
        if (window.innerWidth <= 960) {
            document.getElementById('indonesiaMap').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        leafletMap = L.map('indonesiaMap').setView([-2.5, 114.0], 5);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(leafletMap);

        const bounds = [];

        MAP_MEMBERS.forEach(function (member) {
            const lat = Number(member.latitude);
            const lng = Number(member.longitude);
            if (isNaN(lat) || isNaN(lng)) return;

            const marker = L.marker([lat, lng]).addTo(leafletMap);
            const gmapsUrl = `https://www.google.com/maps?q=${encodeURIComponent(lat + ',' + lng)}`;

            const popupHtml = `
                <img src="${member.avatar}" alt="" class="popup-avatar">
                <div class="popup-name">${escapeHtml(member.name)}</div>
                <div class="popup-address">${escapeHtml(member.address || 'Alamat belum detail')}</div>
                <div class="popup-actions">
                    <a href="${member.tree_url}" class="popup-btn">Lihat di Pohon Silsilah</a>
                    <a href="${gmapsUrl}" target="_blank" rel="noopener" class="popup-btn-sec">Buka Google Maps</a>
                </div>
            `;

            marker.bindPopup(popupHtml, {
                autoPanPaddingTopLeft: L.point(24, 56),
                autoPanPaddingBottomRight: L.point(24, 24),
                maxWidth: 260
            });
            MARKERS_BY_ID[member.id] = marker;
            bounds.push([lat, lng]);
        });

        if (bounds.length === 1) {
            leafletMap.setView(bounds[0], 13);
        } else if (bounds.length > 1) {
            leafletMap.fitBounds(bounds, { padding: [44, 44], maxZoom: 13 });
        }

        // Filter daftar samping
        const filterInput = document.getElementById('mapFilterInput');
        if (filterInput) {
            filterInput.addEventListener('input', function () {
                const q = this.value.trim().toLowerCase();
                document.querySelectorAll('.map-member-item').forEach(el => {
                    const match = !q || (el.dataset.search || '').includes(q);
                    el.style.display = match ? 'flex' : 'none';
                });
            });
        }
    });
</script>
@endpush
