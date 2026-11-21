@extends('layouts.app')

@section('title', ($selectedMember ? 'Kisah ' . $selectedMember->name . ' — ' : '') . 'Our Story Klan Munadi')

@push('styles')
<style>
    .story-wrap {
        padding: clamp(22px, 4vw, 40px) 0 56px;
    }

    /* Bar Pilihan Anggota */
    .story-picker-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 18px 20px;
        box-shadow: var(--shadow-xs);
        margin-bottom: 24px;
    }

    .story-picker-form {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
    }

    .story-picker-form .km-select {
        flex: 1 1 240px;
    }

    .story-chips-section {
        margin-top: 14px;
        padding-top: 14px;
        border-top: 1px solid var(--border);
    }

    .story-chips-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: var(--text-subtle);
        margin-bottom: 8px;
        display: block;
    }

    .story-chips-list {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    .story-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: var(--radius-pill);
        background: var(--surface-muted);
        border: 1px solid var(--border);
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        color: var(--espresso);
        transition: all 0.18s ease;
    }

    .story-chip:hover {
        border-color: var(--gold);
        background: var(--gold-soft);
        color: var(--gold-dark);
    }

    .story-chip.active {
        background: var(--espresso);
        border-color: var(--espresso);
        color: #FAF0DC;
    }

    /* Kartu Cerita Editorial */
    .story-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-top: 4px solid var(--gold);
        border-radius: var(--radius-lg);
        padding: clamp(22px, 4.5vw, 48px);
        box-shadow: var(--shadow-sm);
    }

    .story-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding-bottom: 26px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 28px;
    }

    .story-photo {
        width: 132px;
        height: 132px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--gold);
        box-shadow: 0 8px 24px rgba(154, 117, 25, 0.22);
        margin-bottom: 16px;
        background: var(--espresso);
    }

    .story-name {
        font-family: var(--font-display);
        font-size: clamp(1.7rem, 4vw, 2.45rem);
        color: var(--espresso);
        margin: 8px 0 8px;
    }

    .story-meta-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 8px;
        margin-bottom: 20px;
    }

    .story-header-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
    }

    .story-prose {
        font-family: var(--font-serif);
        font-size: clamp(1rem, 2vw, 1.1rem);
        line-height: 1.9;
        color: #3B3029;
        max-width: 68ch;
        margin: 0 auto;
        white-space: pre-line;
    }

    .story-empty {
        text-align: center;
        padding: 28px 16px;
        background: var(--surface-muted);
        border: 1.5px dashed var(--border-strong);
        border-radius: 16px;
        color: var(--text-muted);
    }

    .story-empty p {
        margin: 0 0 16px;
        font-family: var(--font-serif);
    }

    /* Modal Edit Story */
    .story-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(24, 15, 10, 0.58);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1100;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .story-modal-backdrop.open {
        display: flex;
    }

    .story-modal-box {
        background: var(--surface);
        border-radius: 20px;
        width: 100%;
        max-width: 680px;
        max-height: 90dvh;
        overflow-y: auto;
        padding: clamp(20px, 4vw, 32px);
        box-shadow: var(--shadow-lg);
    }

    .story-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .story-modal-head h2 {
        margin: 0;
        font-size: clamp(1.25rem, 3vw, 1.55rem);
    }

    .story-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 18px;
    }

    @media (max-width: 600px) {
        .story-photo {
            width: 108px;
            height: 108px;
        }

        .story-picker-form .km-btn {
            width: 100%;
        }

        .story-header-actions {
            width: 100%;
        }

        .story-header-actions .km-btn {
            flex: 1 1 140px;
        }

        .story-modal-actions {
            flex-direction: column-reverse;
        }

        .story-modal-actions .km-btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="km-page-header">
    <div class="km-container">
        <span class="km-page-eyebrow">Jejak Sejarah &amp; Kenangan</span>
        <h1>Our Story</h1>
        <p>Mengabadikan kisah perjalanan hidup, perjuangan, dan kenangan berharga para tokoh serta anggota keluarga Klan Munadi.</p>
    </div>
</div>

<div class="km-container-narrow story-wrap">
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

    <!-- Pemilih Anggota -->
    <div class="story-picker-card">
        <form action="{{ route('story') }}" method="GET" class="story-picker-form">
            <select name="search_id" class="km-select" aria-label="Pilih anggota keluarga" onchange="this.form.submit()">
                <option value="">-- Pilih Anggota Keluarga --</option>
                @foreach($members as $m)
                    <option value="{{ $m->id }}" {{ $selectedMember && $selectedMember->id === $m->id ? 'selected' : '' }}>
                        {{ $m->name }}{{ !empty(trim((string) $m->story)) ? ' ★' : '' }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="km-btn km-btn-dark">Tampilkan Kisah</button>
        </form>

        @if($membersWithStory->count() > 0)
            <div class="story-chips-section">
                <span class="story-chips-label">Kisah yang Sudah Ditulis ({{ $membersWithStory->count() }})</span>
                <div class="story-chips-list">
                    @foreach($membersWithStory as $wm)
                        <a href="{{ route('story', ['search_id' => $wm->id]) }}"
                           class="story-chip {{ $selectedMember && $selectedMember->id === $wm->id ? 'active' : '' }}">
                            {{ $wm->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Kartu Kisah Utama -->
    @if($selectedMember)
        <article class="story-card">
            <header class="story-header">
                <img src="{{ $selectedMember->avatar_url }}" alt="{{ $selectedMember->name }}" class="story-photo">
                <span class="km-badge km-badge-gold">Generasi {{ $selectedGeneration }}</span>
                <h2 class="story-name">{{ $selectedMember->name }}</h2>

                <div class="story-meta-row">
                    @if($selectedSpouse)
                        <span class="km-badge km-badge-muted">Pasangan: {{ $selectedSpouse }}</span>
                    @endif
                    @if($selectedMember->formatted_birth_date)
                        <span class="km-badge km-badge-muted">Lahir: {{ $selectedMember->formatted_birth_date }}</span>
                    @endif
                    <span class="km-badge km-badge-muted">{{ $selectedChildrenCount }} Anak</span>
                </div>

                <div class="story-header-actions">
                    @if($isAdmin)
                        <button type="button" class="km-btn km-btn-gold km-btn-sm" onclick="openStoryModal()">
                            {{ $selectedMember->story ? 'Edit Cerita' : 'Tulis Cerita Sekarang' }}
                        </button>
                    @endif
                    <a href="{{ route('family.index', $selectedMember->id) }}" class="km-btn km-btn-outline km-btn-sm">
                        Lihat di Silsilah
                    </a>
                </div>
            </header>

            @if($selectedMember->story)
                <div class="story-prose">{{ $selectedMember->story }}</div>
            @else
                <div class="story-empty">
                    <p>Kisah untuk <strong>{{ $selectedMember->name }}</strong> belum dituliskan. Mari abadikan riwayat hidup, pesan, atau kenangan indah beliau untuk generasi penerus.</p>
                    @if($isAdmin)
                        <button type="button" class="km-btn km-btn-dark km-btn-sm" onclick="openStoryModal()">Mulai Menulis Kisah</button>
                    @endif
                </div>
            @endif
        </article>

        <!-- MODAL EDIT STORY (KHUSUS ADMIN) -->
        @if($isAdmin)
        <div id="storyModal" class="story-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="storyModalTitle">
            <div class="story-modal-box">
                <div class="story-modal-head">
                    <h2 id="storyModalTitle">Tulis Kisah: {{ $selectedMember->name }}</h2>
                    <button type="button" class="km-drawer-close" onclick="closeStoryModal()" aria-label="Tutup">&times;</button>
                </div>

                <form action="{{ route('story.update', $selectedMember->id) }}" method="POST">
                    @csrf
                    <label class="km-label" for="storyTextarea">Isi Kisah / Sejarah Hidup</label>
                    <textarea id="storyTextarea"
                              name="story"
                              rows="11"
                              class="km-textarea"
                              style="font-family:var(--font-serif);line-height:1.75;"
                              placeholder="Tuliskan perjalanan hidup, nasihat, atau kenangan keluarga di sini..."
                              required>{{ old('story', $selectedMember->story) }}</textarea>

                    <div class="story-modal-actions">
                        <button type="button" class="km-btn km-btn-outline" onclick="closeStoryModal()">Batal</button>
                        <button type="submit" class="km-btn km-btn-gold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    @else
        <div class="km-card km-card-accent" style="padding:36px 24px;text-align:center;">
            <h2 style="margin-top:0;">Belum Ada Anggota Keluarga</h2>
            <p style="color:var(--text-muted);margin-bottom:20px;">Tambahkan data anggota terlebih dahulu agar bisa menuliskan kisah keluarga.</p>
            <a href="{{ route('family.create') }}" class="km-btn km-btn-gold">+ Tambah Anggota</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function openStoryModal() {
        const modal = document.getElementById('storyModal');
        if (!modal) return;
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
        const ta = document.getElementById('storyTextarea');
        if (ta) setTimeout(() => ta.focus(), 100);
    }

    function closeStoryModal() {
        const modal = document.getElementById('storyModal');
        if (!modal) return;
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }

    document.addEventListener('click', (e) => {
        const modal = document.getElementById('storyModal');
        if (modal && e.target === modal) closeStoryModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeStoryModal();
    });
</script>
@endpush
