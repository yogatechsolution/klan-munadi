@extends('layouts.app')

@section('title', 'Jadwal & Ulang Tahun Keluarga — Klan Munadi')

@push('styles')
<style>
    .jadwal-wrap {
        padding: clamp(22px, 4vw, 40px) 0 56px;
    }

    .jadwal-grid {
        display: grid;
        grid-template-columns: 1.25fr 1fr;
        gap: 24px;
        align-items: start;
    }

    .section-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-top: 4px solid var(--gold);
        border-radius: var(--radius-lg);
        padding: clamp(20px, 3vw, 28px);
        box-shadow: var(--shadow-sm);
    }

    .section-card h2 {
        margin: 0 0 6px;
        font-size: 1.35rem;
    }

    .section-card .sub {
        margin: 0 0 20px;
        font-size: 13.5px;
        color: var(--text-muted);
    }

    .event-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .event-item {
        display: flex;
        gap: 16px;
        padding: 16px;
        border-radius: 14px;
        background: var(--surface-muted);
        border: 1px solid var(--border);
        align-items: flex-start;
    }

    .event-date-box {
        min-width: 64px;
        padding: 10px 8px;
        border-radius: 12px;
        background: var(--espresso);
        color: #FCF4E4;
        text-align: center;
        flex-shrink: 0;
    }

    .event-date-day {
        font-family: var(--font-display);
        font-size: 1.35rem;
        font-weight: 700;
        line-height: 1.1;
        color: var(--gold-bright);
    }

    .event-date-month {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.9px;
        margin-top: 2px;
        color: rgba(252, 244, 228, 0.8);
    }

    .event-content h3 {
        margin: 0 0 4px;
        font-size: 1.08rem;
        color: var(--espresso);
    }

    .event-meta {
        font-size: 13px;
        color: var(--text-muted);
        margin: 0 0 6px;
    }

    .event-desc {
        font-size: 13.5px;
        color: var(--text);
        margin: 0;
        line-height: 1.5;
    }

    /* Ulang Tahun Keluarga */
    .birthday-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .birthday-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: var(--surface-muted);
        text-decoration: none;
        transition: border-color 0.2s, background 0.2s;
    }

    .birthday-item:hover {
        border-color: var(--gold);
        background: var(--gold-soft);
    }

    .birthday-avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--gold);
        flex-shrink: 0;
    }

    .birthday-info {
        flex: 1;
        min-width: 0;
    }

    .birthday-name {
        font-weight: 700;
        font-size: 14.5px;
        color: var(--espresso);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .birthday-date {
        font-size: 12.5px;
        color: var(--text-muted);
    }

    .empty-bday {
        text-align: center;
        padding: 26px 16px;
        background: var(--surface-muted);
        border: 1.5px dashed var(--border-strong);
        border-radius: 14px;
        color: var(--text-muted);
        font-size: 14px;
    }

    @media (max-width: 860px) {
        .jadwal-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="km-page-header">
    <div class="km-container">
        <span class="km-page-eyebrow">Agenda Kebersamaan</span>
        <h1>Jadwal Acara &amp; Ulang Tahun</h1>
        <p>Informasi kegiatan rutin keluarga besar Klan Munadi serta pengingat ulang tahun anggota keluarga yang tercatat di silsilah.</p>
    </div>
</div>

<div class="km-container jadwal-wrap">
    <div class="jadwal-grid">
        <!-- AGENDA RUTIN KELUARGA -->
        <section class="section-card">
            <h2>Agenda Pertemuan Keluarga</h2>
            <p class="sub">Jadwal silaturahmi dan kegiatan rutin Bani Kasan Munadi.</p>

            <div class="event-list">
                <article class="event-item">
                    <div class="event-date-box">
                        <div class="event-date-day">Syawal</div>
                        <div class="event-date-month">Tahunan</div>
                    </div>
                    <div class="event-content">
                        <h3>Halal Bihalal &amp; Silaturahmi Akbar Klan Munadi</h3>
                        <p class="event-meta">Lokasi: Rumah Besar / Bergilir per Cabang Keluarga</p>
                        <p class="event-desc">Pertemuan akbar seluruh generasi anak, cucu, dan cicit Mbah Kasan Munadi setiap Hari Raya Idul Fitri untuk saling bermaafan dan memperbarui data silsilah.</p>
                    </div>
                </article>

                <article class="event-item">
                    <div class="event-date-box">
                        <div class="event-date-day">Rutin</div>
                        <div class="event-date-month">Bulanan</div>
                    </div>
                    <div class="event-content">
                        <h3>Arisan &amp; Pengajian Keluarga Besar</h3>
                        <p class="event-meta">Hari Minggu Pekan Terakhir &middot; Tuan Rumah Bergilir</p>
                        <p class="event-desc">Silaturahmi rutin bulanan, doa bersama untuk leluhur, serta arisan antar keluarga besar.</p>
                    </div>
                </article>

                <article class="event-item">
                    <div class="event-date-box">
                        <div class="event-date-day">Doa</div>
                        <div class="event-date-month">Tahunan</div>
                    </div>
                    <div class="event-content">
                        <h3>Haul &amp; Doa Bersama Mbah Kasan Munadi &amp; Mbah Sutirah</h3>
                        <p class="event-meta">Lokasi: Kediaman Utama Keluarga</p>
                        <p class="event-desc">Mengenang jasa dan mendoakan leluhur keluarga besar bersama seluruh keturunan.</p>
                    </div>
                </article>
            </div>

            <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
                <span style="font-size:13px;color:var(--text-muted);">Ada informasi acara keluarga terbaru?</span>
                <a href="{{ route('contact') }}" class="km-btn km-btn-outline km-btn-sm">Hubungi Pengurus</a>
            </div>
        </section>

        <!-- PENGINGAT ULANG TAHUN OTOMATIS DARI DATA SILSILAH -->
        <section class="section-card">
            <h2>Ulang Tahun Terdekat</h2>
            <p class="sub">Dihitung otomatis dari tanggal lahir yang tercatat pada profil silsilah.</p>

            @if($upcomingBirthdays->count() > 0)
                @php
                    $months = [
                        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
                        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
                    ];
                @endphp
                <div class="birthday-list">
                    @foreach($upcomingBirthdays as $item)
                        <a href="{{ route('family.index', $item->member->id) }}" class="birthday-item">
                            <img src="{{ $item->member->avatar_url }}" alt="{{ $item->member->name }}" class="birthday-avatar" loading="lazy">
                            <div class="birthday-info">
                                <div class="birthday-name">{{ $item->member->name }}</div>
                                <div class="birthday-date">
                                    {{ $item->next_date->day }} {{ $months[$item->next_date->month] ?? '' }} {{ $item->next_date->year }}
                                    @if($item->turning_age)
                                        &middot; Genap {{ $item->turning_age }} th
                                    @endif
                                </div>
                            </div>
                            <span class="km-badge {{ $item->days_until === 0 ? 'km-badge-gold' : 'km-badge-muted' }}">
                                @if($item->days_until === 0)
                                    Hari Ini!
                                @elseif($item->days_until === 1)
                                    Besok
                                @else
                                    {{ $item->days_until }} hr lagi
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-bday">
                    <p style="margin:0 0 14px;">Belum ada tanggal lahir yang diisi pada data anggota keluarga.</p>
                    <a href="{{ route('family.index') }}" class="km-btn km-btn-gold km-btn-sm">Lengkapi Tanggal Lahir di Silsilah</a>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
