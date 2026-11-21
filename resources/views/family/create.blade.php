@extends('layouts.app')

@section('title', 'Tambah Anggota Keluarga — Klan Munadi')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .form-wrap {
        padding: clamp(20px, 4vw, 40px) 0 56px;
    }

    .form-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-top: 4px solid var(--gold);
        border-radius: var(--radius-lg);
        padding: clamp(20px, 4vw, 38px);
        box-shadow: var(--shadow-sm);
    }

    .form-section-title {
        font-family: var(--font-display);
        font-size: 1.2rem;
        color: var(--espresso);
        margin: 26px 0 14px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border);
    }

    .form-section-title:first-of-type {
        margin-top: 0;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full {
        grid-column: span 2;
    }

    /* Foto Profil Upload */
    .photo-upload-row {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
        background: var(--surface-muted);
        border: 1.5px dashed var(--border-strong);
        border-radius: 14px;
        padding: 16px;
    }

    .photo-preview-circle {
        width: 78px;
        height: 78px;
        border-radius: 50%;
        object-fit: cover;
        border: 2.5px solid var(--gold);
        background: var(--espresso);
        flex-shrink: 0;
    }

    .photo-upload-info {
        flex: 1 1 200px;
        min-width: 0;
    }

    .photo-upload-info p {
        margin: 4px 0 0;
        font-size: 12.5px;
        color: var(--text-muted);
    }

    /* Map Controls */
    .map-search-bar {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .map-search-bar .km-input {
        flex: 1 1 220px;
    }

    #mapPicker {
        height: 330px;
        width: 100%;
        border-radius: 12px;
        border: 1.5px solid var(--border-strong);
        z-index: 1;
    }

    .map-status-msg {
        font-size: 12.5px;
        color: var(--text-muted);
        margin-top: 6px;
        min-height: 18px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 28px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
    }

    .form-actions .km-btn {
        flex: 1;
        padding: 13px 20px;
    }

    @media (max-width: 640px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full {
            grid-column: span 1;
        }

        .photo-upload-row {
            flex-direction: column;
            text-align: center;
        }

        .photo-upload-info {
            width: 100%;
            flex: 1 1 auto;
        }

        #mapPicker {
            height: 260px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }
    }
</style>
@endpush

@section('content')
<div class="km-page-header">
    <div class="km-container">
        <span class="km-page-eyebrow">Data Silsilah Keluarga</span>
        <h1>Tambah Anggota Baru</h1>
        <p>Lengkapi informasi anggota keluarga agar terhubung rapi di bagan silsilah dan peta persebaran.</p>
    </div>
</div>

<div class="km-container-narrow form-wrap">
    @if($errors->any())
        <div class="km-alert km-alert-error">
            <div>
                <strong>Periksa kembali data yang dimasukkan:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="km-alert-close" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <div class="form-card">
        <form action="{{ route('family.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <h2 class="form-section-title">1. Informasi Pribadi</h2>
            <div class="form-grid">
                <div class="form-group full">
                    <label class="km-label" for="photoInput">Foto Profil (Opsional)</label>
                    <div class="photo-upload-row">
                        <img id="photoPreview"
                             src="data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 120 120'%3E%3Crect width='120' height='120' rx='60' fill='%233E271D'/%3E%3Ccircle cx='60' cy='46' r='20' fill='%23E8D5B7'/%3E%3Cpath d='M28 102c4-20 18-30 32-30s28 10 32 30' fill='%23E8D5B7'/%3E%3C/svg%3E"
                             alt="Preview Foto"
                             class="photo-preview-circle">
                        <div class="photo-upload-info">
                            <input type="file" id="photoInput" name="photo" accept="image/*" class="km-input" style="padding:9px 12px;">
                            <p>Format JPG, PNG, atau WEBP (maksimal 4MB). Jika kosong, inisial nama akan tampil otomatis.</p>
                        </div>
                    </div>
                </div>

                <div class="form-group full">
                    <label class="km-label" for="name">Nama Lengkap *</label>
                    <input type="text" id="name" name="name" class="km-input" placeholder="Masukkan nama lengkap..." value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label class="km-label" for="birth_date">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" class="km-input" value="{{ old('birth_date') }}">
                </div>

                <div class="form-group">
                    <label class="km-label" for="spouse_name">Nama Pasangan (Suami / Istri)</label>
                    <input type="text" id="spouse_name" name="spouse_name" class="km-input" placeholder="Kosongkan jika belum ada" value="{{ old('spouse_name') }}">
                </div>
            </div>

            <h2 class="form-section-title">2. Hubungan Orang Tua &amp; Biografi</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label class="km-label" for="father_id">Pilih Ayah</label>
                    <select id="father_id" name="father_id" class="km-select">
                        <option value="">-- Tidak Diketahui / Akar --</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" {{ (string) old('father_id', request('father_id')) === (string) $m->id ? 'selected' : '' }}>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="km-label" for="mother_id">Pilih Ibu</label>
                    <select id="mother_id" name="mother_id" class="km-select">
                        <option value="">-- Tidak Diketahui / Akar --</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" {{ (string) old('mother_id', request('mother_id')) === (string) $m->id ? 'selected' : '' }}>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group full">
                    <label class="km-label" for="bio">Catatan / Biografi Singkat</label>
                    <textarea id="bio" name="bio" rows="3" class="km-textarea" placeholder="Tambahkan profesi, hobi, atau catatan singkat keluarga...">{{ old('bio') }}</textarea>
                </div>
            </div>

            <h2 class="form-section-title">3. Lokasi Tempat Tinggal</h2>
            <div class="form-grid">
                <div class="form-group full">
                    <label class="km-label" for="address">Alamat Lengkap</label>
                    <textarea id="address" name="address" rows="2" class="km-textarea" placeholder="Contoh: Jl. Merdeka No. 10, Kec. Klojen, Kota Malang, Jawa Timur">{{ old('address') }}</textarea>
                </div>

                <div class="form-group full">
                    <label class="km-label">Pin Lokasi di Peta (Opsional)</label>
                    <div class="map-search-bar">
                        <input type="text" id="mapSearchInput" class="km-input" placeholder="Cari kota / kecamatan / alamat...">
                        <button type="button" id="mapSearchBtn" class="km-btn km-btn-dark km-btn-sm">Cari Peta</button>
                        <button type="button" id="gpsBtn" class="km-btn km-btn-outline km-btn-sm">Lokasi Saya</button>
                        <button type="button" id="clearPinBtn" class="km-btn km-btn-danger-outline km-btn-sm">Hapus Pin</button>
                    </div>
                    <div id="mapPicker"></div>
                    <div id="mapStatusMsg" class="map-status-msg">Ketuk peta untuk menaruh pin, atau geser pin untuk menyesuaikan titik.</div>
                </div>

                <div class="form-group">
                    <label class="km-label" for="latInput">Latitude</label>
                    <input type="text" name="latitude" id="latInput" class="km-input" value="{{ old('latitude') }}" readonly placeholder="Otomatis dari peta">
                </div>

                <div class="form-group">
                    <label class="km-label" for="lngInput">Longitude</label>
                    <input type="text" name="longitude" id="lngInput" class="km-input" value="{{ old('longitude') }}" readonly placeholder="Otomatis dari peta">
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('family.index') }}" class="km-btn km-btn-outline">Batal</a>
                <button type="submit" class="km-btn km-btn-success">Simpan Anggota Baru</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Preview Foto Profil
        const photoInput = document.getElementById('photoInput');
        const photoPreview = document.getElementById('photoPreview');
        if (photoInput && photoPreview) {
            photoInput.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => { photoPreview.src = e.target.result; };
                    reader.readAsDataURL(file);
                }
            });
        }

        // Inisialisasi Peta Leaflet
        const latInput = document.getElementById('latInput');
        const lngInput = document.getElementById('lngInput');
        const statusMsg = document.getElementById('mapStatusMsg');

        const oldLat = parseFloat(latInput.value);
        const oldLng = parseFloat(lngInput.value);
        const hasInitial = !isNaN(oldLat) && !isNaN(oldLng);

        const map = L.map('mapPicker').setView(hasInitial ? [oldLat, oldLng] : [-7.25, 110.4], hasInitial ? 14 : 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        let marker = null;

        function setPin(lat, lng, zoom) {
            if (!marker) {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', () => {
                    const pos = marker.getLatLng();
                    updateInputs(pos.lat, pos.lng);
                });
            } else {
                marker.setLatLng([lat, lng]);
            }
            updateInputs(lat, lng);
            if (zoom) map.setView([lat, lng], zoom);
        }

        function updateInputs(lat, lng) {
            latInput.value = Number(lat).toFixed(8);
            lngInput.value = Number(lng).toFixed(8);
            statusMsg.textContent = `Pin terpasang di (${latInput.value}, ${lngInput.value}).`;
        }

        if (hasInitial) {
            setPin(oldLat, oldLng);
        }

        map.on('click', (e) => setPin(e.latlng.lat, e.latlng.lng));

        // Pencarian alamat via OpenStreetMap Nominatim
        async function searchMap() {
            const q = document.getElementById('mapSearchInput').value.trim();
            if (!q) return;
            statusMsg.textContent = 'Mencari lokasi...';
            try {
                const res = await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(q));
                const data = await res.json();
                if (data && data.length > 0) {
                    setPin(parseFloat(data[0].lat), parseFloat(data[0].lon), 14);
                    statusMsg.textContent = 'Ditemukan: ' + data[0].display_name;
                } else {
                    statusMsg.textContent = 'Lokasi tidak ditemukan. Coba nama kota atau kecamatan lain.';
                }
            } catch (err) {
                statusMsg.textContent = 'Gagal mencari lokasi (periksa koneksi internet).';
            }
        }

        document.getElementById('mapSearchBtn').addEventListener('click', searchMap);
        document.getElementById('mapSearchInput').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); searchMap(); }
        });

        // Lokasi GPS Perangkat
        document.getElementById('gpsBtn').addEventListener('click', () => {
            if (!navigator.geolocation) {
                statusMsg.textContent = 'Browser tidak mendukung fitur GPS.';
                return;
            }
            statusMsg.textContent = 'Mengambil koordinat perangkat...';
            navigator.geolocation.getCurrentPosition(
                (pos) => setPin(pos.coords.latitude, pos.coords.longitude, 15),
                () => { statusMsg.textContent = 'Izin lokasi ditolak atau GPS tidak tersedia.'; }
            );
        });

        // Hapus Pin
        document.getElementById('clearPinBtn').addEventListener('click', () => {
            if (marker) {
                map.removeLayer(marker);
                marker = null;
            }
            latInput.value = '';
            lngInput.value = '';
            statusMsg.textContent = 'Pin lokasi dihapus.';
        });
    });
</script>
@endpush
