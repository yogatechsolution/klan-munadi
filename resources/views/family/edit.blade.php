@extends('layouts.app')

@section('title', 'Edit ' . $member->name . ' — Klan Munadi')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .form-wrap {
        padding: clamp(20px, 4vw, 40px) 0 56px;
    }

    .quick-child-banner {
        background: linear-gradient(135deg, #FFFDF8 0%, #FBF3E0 100%);
        border: 1.5px solid var(--gold-border);
        border-radius: var(--radius-md);
        padding: 18px 20px;
        margin-bottom: 22px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .quick-child-banner h3 {
        margin: 0 0 4px;
        font-size: 1.1rem;
        color: var(--espresso);
    }

    .quick-child-banner p {
        margin: 0;
        font-size: 13.5px;
        color: var(--text-muted);
    }

    .quick-child-btns {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
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
        margin: 5px 0 0;
        font-size: 12.5px;
        color: var(--text-muted);
    }

    .remove-photo-check {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-top: 9px;
        font-size: 13px;
        color: var(--danger);
        font-weight: 600;
        cursor: pointer;
    }

    .remove-photo-check input {
        width: auto;
        margin: 0;
    }

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

    .danger-zone {
        margin-top: 26px;
        padding: 18px 20px;
        border-radius: var(--radius-md);
        background: var(--danger-bg);
        border: 1px solid var(--danger-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .danger-zone p {
        margin: 0;
        font-size: 13.5px;
        color: var(--danger);
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

        .quick-child-btns,
        .danger-zone form {
            width: 100%;
        }

        .quick-child-btns .km-btn,
        .danger-zone button {
            flex: 1;
            width: 100%;
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
        <span class="km-page-eyebrow">Perbarui Biodata Silsilah</span>
        <h1>Edit: {{ $member->name }}</h1>
        <p>Perbarui data pribadi, foto profil, hubungan keluarga, atau titik lokasi pada peta.</p>
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

    <!-- Jalur Cepat Tambah Anak -->
    <div class="quick-child-banner">
        <div>
            <h3>Jalur Cepat: Tambah Anak</h3>
            <p>Tambahkan anak yang langsung terhubung dengan <strong>{{ $member->name }}</strong>.</p>
        </div>
        <div class="quick-child-btns">
            <a href="{{ route('family.create', ['father_id' => $member->id]) }}" class="km-btn km-btn-dark km-btn-sm">+ Sbg Ayah</a>
            <a href="{{ route('family.create', ['mother_id' => $member->id]) }}" class="km-btn km-btn-gold km-btn-sm">+ Sbg Ibu</a>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('family.update', $member->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <h2 class="form-section-title">1. Informasi Pribadi</h2>
            <div class="form-grid">
                <div class="form-group full">
                    <label class="km-label" for="photoInput">Foto Profil</label>
                    <div class="photo-upload-row">
                        <img id="photoPreview" src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="photo-preview-circle">
                        <div class="photo-upload-info">
                            <input type="file" id="photoInput" name="photo" accept="image/*" class="km-input" style="padding:9px 12px;">
                            <p>Pilih foto baru untuk mengganti (JPG/PNG/WEBP, maks. 4MB).</p>
                            @if($member->hasCustomPhoto())
                                <label class="remove-photo-check">
                                    <input type="checkbox" name="remove_photo" value="1">
                                    Hapus foto saat ini (kembali ke inisial)
                                </label>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group full">
                    <label class="km-label" for="name">Nama Lengkap *</label>
                    <input type="text" id="name" name="name" class="km-input" value="{{ old('name', $member->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="km-label" for="birth_date">Tanggal Lahir</label>
                    <input type="date" id="birth_date" name="birth_date" class="km-input" value="{{ old('birth_date', $member->birth_date) }}">
                </div>

                <div class="form-group">
                    <label class="km-label" for="spouse_name">Nama Pasangan (Suami / Istri)</label>
                    <input type="text" id="spouse_name" name="spouse_name" class="km-input" value="{{ old('spouse_name', $member->spouse_name) }}" placeholder="Kosongkan jika belum ada">
                </div>
            </div>

            <h2 class="form-section-title">2. Hubungan Orang Tua &amp; Biografi</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label class="km-label" for="father_id">Pilih Ayah</label>
                    <select id="father_id" name="father_id" class="km-select">
                        <option value="">-- Tidak Diketahui --</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" {{ (string) old('father_id', $member->father_id) === (string) $m->id ? 'selected' : '' }}>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="km-label" for="mother_id">Pilih Ibu</label>
                    <select id="mother_id" name="mother_id" class="km-select">
                        <option value="">-- Tidak Diketahui --</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" {{ (string) old('mother_id', $member->mother_id) === (string) $m->id ? 'selected' : '' }}>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group full">
                    <label class="km-label" for="bio">Catatan / Biografi Singkat</label>
                    <textarea id="bio" name="bio" rows="3" class="km-textarea" placeholder="Tambahkan keterangan seperti pekerjaan atau riwayat singkat...">{{ old('bio', $member->bio) }}</textarea>
                </div>
            </div>

            <h2 class="form-section-title">3. Lokasi Tempat Tinggal</h2>
            <div class="form-grid">
                <div class="form-group full">
                    <label class="km-label" for="address">Alamat Lengkap</label>
                    <textarea id="address" name="address" rows="2" class="km-textarea" placeholder="Contoh: Jl. Merdeka No. 10, Malang...">{{ old('address', $member->address) }}</textarea>
                </div>

                <div class="form-group full">
                    <label class="km-label">Pin Lokasi di Peta</label>
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
                    <input type="text" name="latitude" id="latInput" class="km-input" value="{{ old('latitude', $member->latitude) }}" readonly placeholder="Belum diatur">
                </div>

                <div class="form-group">
                    <label class="km-label" for="lngInput">Longitude</label>
                    <input type="text" name="longitude" id="lngInput" class="km-input" value="{{ old('longitude', $member->longitude) }}" readonly placeholder="Belum diatur">
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('family.index', $member->id) }}" class="km-btn km-btn-outline">Batal</a>
                <button type="submit" class="km-btn km-btn-gold">Simpan Perubahan</button>
            </div>
        </form>

        <div class="danger-zone">
            <p><strong>Hapus Anggota:</strong> Data yang dihapus dari silsilah tidak dapat dikembalikan.</p>
            <form action="{{ route('family.destroy', $member->id) }}" method="POST" onsubmit="return confirm('PERINGATAN! Yakin ingin menghapus {{ addslashes($member->name) }} dari silsilah secara permanen?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="km-btn km-btn-danger-outline km-btn-sm">Hapus Permanen</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
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

        const latInput = document.getElementById('latInput');
        const lngInput = document.getElementById('lngInput');
        const statusMsg = document.getElementById('mapStatusMsg');

        const existingLat = parseFloat(latInput.value);
        const existingLng = parseFloat(lngInput.value);
        const hasInitial = !isNaN(existingLat) && !isNaN(existingLng);

        const map = L.map('mapPicker').setView(hasInitial ? [existingLat, existingLng] : [-7.25, 110.4], hasInitial ? 14 : 6);
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
            setPin(existingLat, existingLng);
        }

        map.on('click', (e) => setPin(e.latlng.lat, e.latlng.lng));

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
                    statusMsg.textContent = 'Lokasi tidak ditemukan. Coba kata kunci lain.';
                }
            } catch (err) {
                statusMsg.textContent = 'Gagal mencari lokasi (periksa koneksi internet).';
            }
        }

        document.getElementById('mapSearchBtn').addEventListener('click', searchMap);
        document.getElementById('mapSearchInput').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); searchMap(); }
        });

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
