@extends('layouts.app')

@section('title', 'Login Admin — Klan Munadi')

@push('styles')
<style>
    .login-wrap {
        padding: clamp(32px, 6vw, 68px) 0 64px;
        display: flex;
        justify-content: center;
    }

    .login-card {
        width: 100%;
        max-width: 440px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-top: 4px solid var(--gold);
        border-radius: var(--radius-lg);
        padding: clamp(24px, 4vw, 36px);
        box-shadow: var(--shadow-md);
    }

    .login-crest {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: linear-gradient(135deg, #D9B44A 0%, #9E771C 100%);
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-display);
        font-size: 22px;
        font-weight: 700;
        margin: 0 auto 14px;
        box-shadow: 0 8px 20px rgba(158, 119, 28, 0.28);
    }

    .login-head {
        text-align: center;
        margin-bottom: 24px;
    }

    .login-head h1 {
        font-size: 1.65rem;
        margin: 0 0 6px;
    }

    .login-head p {
        margin: 0;
        font-size: 14px;
        color: var(--text-muted);
    }

    .login-field {
        margin-bottom: 16px;
    }

    .login-note {
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
        font-size: 12.5px;
        color: var(--text-muted);
        text-align: center;
        line-height: 1.55;
    }
</style>
@endpush

@section('content')
<div class="km-container login-wrap">
    <div class="login-card">
        <div class="login-crest">KM</div>
        <div class="login-head">
            <h1>Masuk Mode Admin</h1>
            <p>Khusus pengurus keluarga untuk menambah, mengubah, atau menghapus data silsilah, galeri, kisah, dan pengaturan.</p>
        </div>

        @if(session('info'))
            <div class="km-alert" style="background:var(--gold-soft);color:var(--gold-dark);border:1px solid var(--gold-border);">
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="km-alert km-alert-error">
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf

            <div class="login-field">
                <label class="km-label" for="username">Username Admin</label>
                <input type="text"
                       id="username"
                       name="username"
                       class="km-input"
                       value="{{ old('username', 'admin') }}"
                       autocomplete="username"
                       required>
            </div>

            <div class="login-field">
                <label class="km-label" for="password">Kata Sandi</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="km-input"
                       placeholder="Masukkan kata sandi admin..."
                       autocomplete="current-password"
                       required
                       autofocus>
            </div>

            <button type="submit" class="km-btn km-btn-gold" style="width:100%;padding:13px;">
                Masuk sebagai Admin
            </button>
        </form>

        <div class="login-note">
            Anggota keluarga lainnya tetap dapat melihat seluruh pohon silsilah, peta persebaran, galeri, dan kisah tanpa perlu login.<br>
            <a href="{{ route('family.index') }}" style="color:var(--gold-dark);font-weight:600;text-decoration:none;display:inline-block;margin-top:8px;">&larr; Kembali ke Pohon Silsilah</a>
        </div>
    </div>
</div>
@endsection
