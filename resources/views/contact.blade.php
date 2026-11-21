@extends('layouts.app')

@section('title', 'Kontak Pengurus — Klan Munadi')

@push('styles')
<style>
    .contact-wrap {
        padding: clamp(22px, 4vw, 44px) 0 56px;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
        margin-bottom: 26px;
    }

    .contact-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 24px;
        box-shadow: var(--shadow-xs);
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .contact-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--gold-soft);
        color: var(--gold-dark);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .contact-card h3 {
        margin: 0;
        font-size: 1.15rem;
    }

    .contact-value {
        font-size: 15px;
        color: var(--text);
        font-weight: 600;
        margin: 0;
        word-break: break-word;
    }

    .contact-note-card {
        background: linear-gradient(135deg, #FFFDF8 0%, #FBF3E0 100%);
        border: 1px solid var(--gold-border);
        border-radius: var(--radius-lg);
        padding: clamp(22px, 4vw, 32px);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
    }

    .contact-note-card p {
        margin: 6px 0 0;
        color: var(--text-muted);
        max-width: 620px;
    }

    @media (max-width: 700px) {
        .contact-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="km-page-header">
    <div class="km-container">
        <span class="km-page-eyebrow">Koordinasi Keluarga Besar</span>
        <h1>Kontak Pengurus</h1>
        <p>Silakan hubungi pengurus atau admin silsilah Klan Munadi untuk bantuan pembaruan data, dokumentasi foto, dan koordinasi acara.</p>
    </div>
</div>

@php
    $waNumber = preg_replace('/[^0-9]/', '', $contact['phone'] ?? '');
    if ($waNumber && str_starts_with($waNumber, '0')) {
        $waNumber = '62' . substr($waNumber, 1);
    }
@endphp

<div class="km-container-narrow contact-wrap">
    <div class="contact-grid">
        <div class="contact-card">
            <span class="contact-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </span>
            <h3>Koordinator / Pengurus</h3>
            <p class="contact-value">{{ $contact['name'] }}</p>
            <span style="font-size:13px;color:var(--text-muted);">Penanggung jawab data silsilah &amp; dokumentasi keluarga.</span>
        </div>

        <div class="contact-card">
            <span class="contact-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            </span>
            <h3>WhatsApp / Telepon</h3>
            <p class="contact-value">{{ $contact['phone'] ?: 'Belum diatur di Pengaturan' }}</p>
            @if($waNumber)
                <div style="margin-top:4px;">
                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener" class="km-btn km-btn-success km-btn-sm">Chat via WhatsApp</a>
                </div>
            @endif
        </div>

        <div class="contact-card">
            <span class="contact-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            </span>
            <h3>Email Keluarga</h3>
            <p class="contact-value">{{ $contact['email'] ?: 'Belum diatur di Pengaturan' }}</p>
            @if(!empty($contact['email']))
                <div style="margin-top:4px;">
                    <a href="mailto:{{ $contact['email'] }}" class="km-btn km-btn-outline km-btn-sm">Kirim Email</a>
                </div>
            @endif
        </div>

        <div class="contact-card">
            <span class="contact-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            </span>
            <h3>Alamat Rumah Besar</h3>
            <p class="contact-value">{{ $contact['address'] }}</p>
        </div>
    </div>

    <div class="contact-note-card">
        <div>
            <h3 style="margin:0;font-size:1.2rem;">Butuh Pembaruan Data Silsilah?</h3>
            <p>{{ $contact['note'] }}</p>
        </div>
        @if($isAdmin)
            <a href="{{ route('settings.index') }}" class="km-btn km-btn-dark">Kelola di Pengaturan</a>
        @else
            <a href="{{ route('admin.login') }}" class="km-btn km-btn-dark">Masuk Mode Admin</a>
        @endif
    </div>
</div>
@endsection
