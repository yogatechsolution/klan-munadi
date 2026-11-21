@extends('layouts.app')

@section('title', ($currentCore ? $currentCore->name . ' — ' : '') . 'Pohon Silsilah Klan Munadi')

@push('styles')
<style>
    /* Latar belakang khusus halaman silsilah (ramah HP, tanpa jitter background-attachment fixed) */
    .tree-page-bg {
        position: relative;
        min-height: calc(100vh - var(--nav-height));
        background-color: var(--bg);
        padding-bottom: 48px;
    }

    .tree-page-bg::before {
        content: '';
        position: fixed;
        inset: 0;
        background-image:
            linear-gradient(180deg, rgba(250, 247, 242, 0.88) 0%, rgba(250, 247, 242, 0.78) 45%, rgba(243, 236, 225, 0.90) 100%),
            url("{{ $treeBgImage }}");
        background-size: cover;
        background-position: center;
        z-index: 0;
        pointer-events: none;
    }

    .tree-page-inner {
        position: relative;
        z-index: 1;
        padding-top: 22px;
    }

    /* ================================================================
       TOOLBAR KONTROL (PENCARIAN, MODE TAMPILAN, NAVIGASI)
       ================================================================ */
    .tree-toolbar {
        position: relative;
        z-index: 60;
        background: rgba(255, 255, 255, 0.94);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        padding: 16px 18px;
        box-shadow: var(--shadow-sm);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .tree-search-wrap {
        position: relative;
        flex: 1 1 260px;
        min-width: 220px;
    }

    .tree-search-icon {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-subtle);
        pointer-events: none;
    }

    .tree-search-input {
        width: 100%;
        padding: 11px 38px 11px 40px;
        border: 1.5px solid var(--border-strong);
        border-radius: 12px;
        font-family: var(--font-ui);
        font-size: 14.5px;
        background: var(--surface);
        color: var(--text);
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .tree-search-input:focus {
        outline: none;
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(200, 160, 53, 0.16);
    }

    .tree-search-clear {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: var(--bg-warm);
        border: none;
        color: var(--text-muted);
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }

    .tree-search-clear.visible {
        display: flex;
    }

    .tree-search-results {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        background: var(--surface);
        border: 1px solid var(--border-strong);
        border-radius: 14px;
        box-shadow: var(--shadow-lg);
        max-height: 320px;
        overflow-y: auto;
        z-index: 500;
        display: none;
    }

    .tree-search-results.open {
        display: block;
    }

    .search-result-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 14px;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        transition: background 0.15s;
    }

    .search-result-item:last-child {
        border-bottom: none;
    }

    .search-result-item:hover {
        background: var(--gold-soft);
    }

    .search-result-main {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        flex: 1;
    }

    .search-result-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 1.5px solid var(--gold);
        flex-shrink: 0;
    }

    .search-result-info {
        min-width: 0;
    }

    .search-result-name {
        font-weight: 700;
        font-size: 14px;
        color: var(--espresso);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .search-result-meta {
        font-size: 12px;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .search-result-actions {
        display: flex;
        gap: 6px;
        flex-shrink: 0;
    }

    .search-empty {
        padding: 18px;
        text-align: center;
        color: var(--text-muted);
        font-size: 13.5px;
    }

    /* Segmented View Toggle */
    .view-toggle {
        display: inline-flex;
        background: var(--bg-warm);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 3px;
        gap: 3px;
    }

    .view-toggle-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 13px;
        border: none;
        border-radius: 9px;
        background: transparent;
        color: var(--text-muted);
        font-family: var(--font-ui);
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .view-toggle-btn.active {
        background: var(--surface);
        color: var(--espresso);
        box-shadow: var(--shadow-xs);
    }

    .toolbar-nav-btns {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    /* ================================================================
       BREADCRUMB GARIS KETURUNAN
       ================================================================ */
    .tree-breadcrumb {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding: 6px 2px 14px;
        scrollbar-width: none;
    }

    .tree-breadcrumb::-webkit-scrollbar {
        display: none;
    }

    .crumb-link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        border-radius: var(--radius-pill);
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid var(--border);
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-muted);
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .crumb-link:hover {
        border-color: var(--gold);
        color: var(--gold-dark);
        background: var(--gold-soft);
    }

    .crumb-link.current {
        background: var(--espresso);
        border-color: var(--espresso);
        color: #F8EEDB;
    }

    .crumb-sep {
        color: var(--text-subtle);
        font-size: 14px;
        flex-shrink: 0;
    }

    /* ================================================================
       MODE 1: BAGAN POHON INTERAKTIF (FIX OVERFLOW & ZOOM)
       ================================================================ */
    .tree-stage-card {
        position: relative;
        z-index: 1;
        background: rgba(255, 255, 255, 0.52);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(230, 221, 208, 0.9);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .tree-stage-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        padding: 12px 18px;
        background: rgba(255, 255, 255, 0.86);
        border-bottom: 1px solid var(--border);
    }

    .tree-stage-hint {
        font-size: 12.5px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .zoom-controls {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--bg-warm);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 3px;
    }

    .zoom-btn {
        min-width: 32px;
        height: 30px;
        border: none;
        border-radius: 7px;
        background: transparent;
        color: var(--espresso);
        font-family: var(--font-ui);
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        padding: 0 8px;
        transition: background 0.15s;
    }

    .zoom-btn:hover {
        background: var(--surface);
    }

    .zoom-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        min-width: 44px;
        text-align: center;
    }

    /* Scroll container menggunakan text-align:center + inline-block agar sisi kiri TIDAK PERNAH terpotong */
    .tree-scroll-viewport {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        padding: 28px 24px 44px;
        text-align: center;
        cursor: grab;
    }

    .tree-scroll-viewport.grabbing {
        cursor: grabbing;
        user-select: none;
    }

    .tree-zoom-wrapper {
        display: inline-block;
        transform-origin: top center;
        transition: transform 0.2s ease;
        min-width: min-content;
    }

    /* CSS Connectors Pohon Silsilah */
    .tree {
        display: inline-block;
    }

    .tree ul {
        padding-top: 28px;
        position: relative;
        display: flex;
        justify-content: center;
        padding-left: 0;
        margin: 0;
    }

    .tree > ul {
        padding-top: 0;
    }

    .tree li {
        text-align: center;
        list-style-type: none;
        position: relative;
        padding: 28px 10px 0 10px;
    }

    .tree > ul > li {
        padding-top: 0;
    }

    .tree li::before,
    .tree li::after {
        content: '';
        position: absolute;
        top: 0;
        right: 50%;
        border-top: 2px solid #C2AE8E;
        width: 50%;
        height: 28px;
    }

    .tree li::after {
        right: auto;
        left: 50%;
        border-left: 2px solid #C2AE8E;
    }

    .tree li:only-child::after,
    .tree li:only-child::before {
        display: none;
    }

    .tree li:only-child {
        padding-top: 0;
    }

    .tree li:first-child::before,
    .tree li:last-child::after {
        border: 0 none;
    }

    .tree li:last-child::before {
        border-right: 2px solid #C2AE8E;
        border-radius: 0 10px 0 0;
    }

    .tree li:first-child::after {
        border-radius: 10px 0 0 0;
    }

    .tree ul ul::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        border-left: 2px solid #C2AE8E;
        width: 0;
        height: 28px;
    }

    /* Kartu Anggota di Bagan Pohon */
    .node-card {
        background: rgba(255, 255, 255, 0.96);
        border: 1.5px solid var(--border);
        border-radius: 16px;
        padding: 16px 14px 14px;
        width: 196px;
        box-shadow: 0 8px 24px rgba(38, 23, 16, 0.08);
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        position: relative;
        text-align: center;
    }

    .node-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(38, 23, 16, 0.14);
        border-color: var(--gold);
    }

    .node-card.root-node {
        width: 260px;
        padding: 20px 18px 18px;
        background: linear-gradient(180deg, #FFFDF8 0%, #FBF4E4 100%);
        border: 2px solid var(--gold);
        box-shadow: 0 12px 32px rgba(154, 117, 25, 0.18);
    }

    .node-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        object-fit: cover;
        border: 2.5px solid var(--gold);
        margin-bottom: 10px;
        box-shadow: 0 4px 10px rgba(38, 23, 16, 0.12);
        background: var(--espresso);
    }

    .node-card.root-node .node-avatar {
        width: 72px;
        height: 72px;
        border-width: 3px;
    }

    .node-gen-pill {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.9px;
        color: var(--gold-dark);
        background: var(--gold-soft);
        border: 1px solid var(--gold-border);
        padding: 2px 9px;
        border-radius: var(--radius-pill);
        margin-bottom: 6px;
    }

    .node-name {
        font-family: var(--font-display);
        font-size: 15.5px;
        font-weight: 700;
        color: var(--espresso);
        line-height: 1.25;
        margin-bottom: 4px;
        word-break: break-word;
    }

    .node-card.root-node .node-name {
        font-size: 19px;
    }

    .node-spouse {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 6px;
        line-height: 1.3;
    }

    .node-meta {
        font-size: 11.5px;
        color: var(--text-subtle);
        margin-bottom: 8px;
    }

    .node-actions {
        display: flex;
        justify-content: center;
        gap: 6px;
        width: 100%;
        border-top: 1px solid var(--border);
        padding-top: 10px;
        margin-top: auto;
    }

    .node-pill-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 6px 11px;
        border-radius: var(--radius-pill);
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--border);
        background: var(--surface-muted);
        color: var(--espresso);
        cursor: pointer;
        transition: all 0.18s ease;
    }

    .node-pill-btn:hover {
        background: var(--gold-soft);
        border-color: var(--gold);
        color: var(--gold-dark);
    }

    .node-pill-btn.has-kids {
        background: var(--espresso);
        border-color: var(--espresso);
        color: #FAF0DC;
    }

    .node-pill-btn.has-kids:hover {
        background: var(--gold-dark);
        border-color: var(--gold-dark);
        color: #FFFFFF;
    }

    .no-children-box {
        margin: 26px auto 4px;
        max-width: 440px;
        background: rgba(255, 255, 255, 0.92);
        border: 1.5px dashed var(--border-strong);
        border-radius: 16px;
        padding: 20px;
        text-align: center;
    }

    .no-children-box p {
        margin: 0 0 14px 0;
        font-size: 14px;
        color: var(--text-muted);
    }

    /* ================================================================
       MODE 2: TAMPILAN DAFTAR / HIERARKI (SUPER NYAMAN DI HP)
       ================================================================ */
    .list-view-wrap {
        display: none;
    }

    .list-view-wrap.active {
        display: block;
    }

    .tree-view-wrap {
        display: block;
    }

    .tree-view-wrap.hidden {
        display: none;
    }

    .list-core-card {
        background: linear-gradient(135deg, #FFFDF8 0%, #FBF3E1 100%);
        border: 2px solid var(--gold);
        border-radius: var(--radius-lg);
        padding: 22px;
        box-shadow: var(--shadow-md);
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
        margin-bottom: 22px;
        cursor: pointer;
    }

    .list-core-avatar {
        width: 78px;
        height: 78px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--gold);
        flex-shrink: 0;
    }

    .list-core-info {
        flex: 1 1 220px;
        min-width: 0;
    }

    .list-core-name {
        font-family: var(--font-display);
        font-size: clamp(1.35rem, 3vw, 1.8rem);
        margin: 4px 0;
        color: var(--espresso);
    }

    .list-core-sub {
        font-size: 13.5px;
        color: var(--text-muted);
    }

    .list-core-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .list-section-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin: 0 0 14px 0;
        flex-wrap: wrap;
    }

    .list-section-title h3 {
        margin: 0;
        font-size: 1.2rem;
    }

    .children-list-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 14px;
    }

    .child-list-card {
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 16px;
        box-shadow: var(--shadow-xs);
        display: flex;
        flex-direction: column;
        gap: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .child-list-card:hover {
        border-color: var(--gold);
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    .child-list-top {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .child-list-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--gold);
        flex-shrink: 0;
    }

    .child-list-body {
        min-width: 0;
        flex: 1;
    }

    .child-list-name {
        font-family: var(--font-display);
        font-size: 1.08rem;
        font-weight: 700;
        color: var(--espresso);
        margin: 0 0 2px 0;
    }

    .child-list-meta {
        font-size: 12.5px;
        color: var(--text-muted);
    }

    .child-list-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        border-top: 1px solid var(--border);
        padding-top: 10px;
    }

    /* ================================================================
       MODAL DETAIL ANGGOTA (CENTERED DESKTOP · BOTTOM SHEET MOBILE)
       ================================================================ */
    .km-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(24, 15, 10, 0.58);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1200;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }

    .km-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .km-detail-modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -48%) scale(0.95);
        width: min(92vw, 500px);
        max-height: 88dvh;
        overflow-y: auto;
        background: var(--surface);
        border-radius: 22px;
        box-shadow: var(--shadow-lg);
        z-index: 1210;
        padding: 28px 26px 24px;
        opacity: 0;
        visibility: hidden;
        transition: all 0.26s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .km-detail-modal.active {
        opacity: 1;
        visibility: visible;
        transform: translate(-50%, -50%) scale(1);
    }

    .modal-close-btn {
        position: absolute;
        top: 14px;
        right: 14px;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 1px solid var(--border);
        background: var(--surface-muted);
        color: var(--text-muted);
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.18s;
    }

    .modal-close-btn:hover {
        background: var(--danger-bg);
        border-color: var(--danger-border);
        color: var(--danger);
    }

    .modal-profile-head {
        text-align: center;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 18px;
    }

    .modal-avatar {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--gold);
        box-shadow: 0 6px 18px rgba(38, 23, 16, 0.14);
        margin-bottom: 12px;
    }

    .modal-name {
        font-family: var(--font-display);
        font-size: 1.5rem;
        color: var(--espresso);
        margin: 0 0 6px 0;
    }

    .modal-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 14px;
    }

    .modal-field {
        background: var(--surface-muted);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 11px 13px;
    }

    .modal-field.full {
        grid-column: span 2;
    }

    .modal-field-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: var(--text-subtle);
        margin-bottom: 4px;
    }

    .modal-field-value {
        font-size: 14px;
        font-weight: 500;
        color: var(--text);
        word-break: break-word;
    }

    .modal-person-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: var(--radius-pill);
        background: var(--surface);
        border: 1px solid var(--border-strong);
        color: var(--espresso);
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        margin: 2px 4px 2px 0;
        transition: all 0.15s;
    }

    .modal-person-chip:hover {
        border-color: var(--gold);
        background: var(--gold-soft);
        color: var(--gold-dark);
    }

    .modal-actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
    }

    .modal-actions-grid .full {
        grid-column: span 2;
    }

    /* ================================================================
       RESPONSIVE MOBILE TUNING
       ================================================================ */
    @media (max-width: 768px) {
        .tree-toolbar {
            padding: 12px;
            gap: 10px;
        }

        .view-toggle {
            width: 100%;
        }

        .view-toggle-btn {
            flex: 1;
            justify-content: center;
        }

        .toolbar-nav-btns {
            width: 100%;
        }

        .toolbar-nav-btns .km-btn {
            flex: 1;
        }

        .tree-stage-bar {
            padding: 10px 12px;
        }

        .tree-stage-hint {
            font-size: 12px;
        }

        .node-card {
            width: 170px;
            padding: 14px 10px 12px;
        }

        .node-card.root-node {
            width: 220px;
        }

        .list-core-card {
            padding: 18px 16px;
        }

        .list-core-actions {
            width: 100%;
        }

        .list-core-actions .km-btn {
            flex: 1;
        }

        .children-list-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        /* Bottom Sheet style modal di HP */
        .km-detail-modal {
            top: auto;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            max-width: 100%;
            max-height: 86dvh;
            border-radius: 24px 24px 0 0;
            padding: 24px 18px calc(24px + env(safe-area-inset-bottom, 0px));
            transform: translateY(100%);
        }

        .km-detail-modal.active {
            transform: translateY(0);
        }

        .modal-info-grid {
            grid-template-columns: 1fr;
        }

        .modal-field.full {
            grid-column: span 1;
        }

        .modal-actions-grid {
            grid-template-columns: 1fr;
        }

        .modal-actions-grid .full {
            grid-column: span 1;
        }
    }
</style>
@endpush

@section('content')
<div class="tree-page-bg">
    <div class="km-container tree-page-inner">

        @if(session('success'))
            <div class="km-alert km-alert-success" role="status">
                <span>{{ session('success') }}</span>
                <button type="button" class="km-alert-close" onclick="this.parentElement.remove()" aria-label="Tutup">&times;</button>
            </div>
        @endif

        @if($currentCore)
            @php
                $coreGen = $generations[$currentCore->id] ?? 1;
                $coreSpouse = trim((string) $currentCore->spouse_name) !== ''
                    ? $currentCore->spouse_name
                    : ($inferredSpouses[$currentCore->id]['name'] ?? null);
                $coreTotalDesc = $descendantCounts[$currentCore->id] ?? 0;
                $parentId = $currentCore->father_id ?? $currentCore->mother_id;
            @endphp

            <!-- 1. TOOLBAR PENCARIAN, TOGGLE MODE, NAVIGASI -->
            <div class="tree-toolbar">
                <div class="tree-search-wrap">
                    <svg class="tree-search-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text"
                           id="memberSearchInput"
                           class="tree-search-input"
                           placeholder="Cari nama, pasangan, atau alamat ({{ $allMembers->count() }} anggota)..."
                           autocomplete="off"
                           aria-label="Cari anggota keluarga">
                    <button type="button" id="searchClearBtn" class="tree-search-clear" aria-label="Hapus pencarian">&times;</button>
                    <div id="searchResultsBox" class="tree-search-results" role="listbox"></div>
                </div>

                <div class="view-toggle" role="tablist" aria-label="Mode tampilan silsilah">
                    <button type="button" id="btnModeTree" class="view-toggle-btn active" onclick="setViewMode('tree')" role="tab" aria-selected="true">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="3"></circle><circle cx="5" cy="19" r="3"></circle><circle cx="19" cy="19" r="3"></circle><path d="M12 8v4m0 0-7 4m7-4 7 4"></path></svg>
                        Bagan Pohon
                    </button>
                    <button type="button" id="btnModeList" class="view-toggle-btn" onclick="setViewMode('list')" role="tab" aria-selected="false">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        Daftar HP
                    </button>
                </div>

                <div class="toolbar-nav-btns">
                    @if($rootMember && $currentCore->id !== $rootMember->id)
                        <a href="{{ route('family.index', $rootMember->id) }}" class="km-btn km-btn-outline km-btn-sm">
                            Ke Akar Utama
                        </a>
                    @endif
                    @if($parentId)
                        <a href="{{ route('family.index', $parentId) }}" class="km-btn km-btn-dark km-btn-sm">
                            &uarr; Generasi Sebelumnya
                        </a>
                    @endif
                </div>
            </div>

            <!-- 2. BREADCRUMB GARIS KETURUNAN -->
            @if(count($breadcrumbs) > 0)
                <nav class="tree-breadcrumb" aria-label="Jejak Generasi">
                    @foreach($breadcrumbs as $idx => $crumb)
                        @if($idx > 0)
                            <span class="crumb-sep" aria-hidden="true">&rsaquo;</span>
                        @endif
                        <a href="{{ route('family.index', $crumb->id) }}"
                           class="crumb-link {{ $crumb->id === $currentCore->id ? 'current' : '' }}">
                            <span>Gen {{ $generations[$crumb->id] ?? ($idx + 1) }}</span>
                            <span>&middot; {{ $crumb->name }}</span>
                        </a>
                    @endforeach
                </nav>
            @endif

            <!-- 3. MODE 1: BAGAN POHON VISUAL -->
            <div id="treeViewWrap" class="tree-view-wrap">
                <div class="tree-stage-card">
                    <div class="tree-stage-bar">
                        <div class="tree-stage-hint">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                            <span>Ketuk kartu untuk detail. Geser kanan/kiri untuk melihat semua cabang.</span>
                        </div>
                        <div class="zoom-controls" aria-label="Kontrol Zoom Bagan">
                            <button type="button" class="zoom-btn" onclick="changeZoom(-0.12)" title="Perkecil">&minus;</button>
                            <span id="zoomLabel" class="zoom-label">100%</span>
                            <button type="button" class="zoom-btn" onclick="changeZoom(0.12)" title="Perbesar">+</button>
                            <button type="button" class="zoom-btn" onclick="resetZoom()" title="Reset Zoom">Reset</button>
                        </div>
                    </div>

                    <div id="treeScrollViewport" class="tree-scroll-viewport">
                        <div id="treeZoomWrapper" class="tree-zoom-wrapper">
                            <div class="tree">
                                <ul>
                                    <li>
                                        <!-- CORE NODE -->
                                        <div class="node-card root-node" onclick="openMemberModal({{ $currentCore->id }})">
                                            <img src="{{ $currentCore->avatar_url }}" alt="{{ $currentCore->name }}" class="node-avatar">
                                            <span class="node-gen-pill">Generasi {{ $coreGen }}</span>
                                            <div class="node-name">{{ $currentCore->name }}</div>
                                            @if($coreSpouse)
                                                <div class="node-spouse">&amp; {{ $coreSpouse }}</div>
                                            @endif
                                            <div class="node-meta">
                                                {{ $directChildren->count() }} Anak
                                                @if($coreTotalDesc > $directChildren->count())
                                                    &middot; {{ $coreTotalDesc }} Total Keturunan
                                                @endif
                                            </div>
                                            <div class="node-actions">
                                                <button type="button" class="node-pill-btn" onclick="event.stopPropagation(); openMemberModal({{ $currentCore->id }});">
                                                    Detail
                                                </button>
                                                @if($isAdmin)
                                                    <a href="{{ route('family.edit', $currentCore->id) }}" class="node-pill-btn" onclick="event.stopPropagation();">
                                                        Edit
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- ANAK-ANAK LANGSUNG -->
                                        @if($directChildren->count() > 0)
                                            <ul>
                                                @foreach($directChildren as $child)
                                                    @php
                                                        $childKidsCount = ($childrenMap[$child->id] ?? collect())->count();
                                                        $childTotalDesc = $descendantCounts[$child->id] ?? 0;
                                                        $childSpouse = trim((string) $child->spouse_name) !== ''
                                                            ? $child->spouse_name
                                                            : ($inferredSpouses[$child->id]['name'] ?? null);
                                                    @endphp
                                                    <li>
                                                        <div class="node-card" onclick="openMemberModal({{ $child->id }})">
                                                            <img src="{{ $child->avatar_url }}" alt="{{ $child->name }}" class="node-avatar" loading="lazy">
                                                            <span class="node-gen-pill">Generasi {{ $generations[$child->id] ?? ($coreGen + 1) }}</span>
                                                            <div class="node-name">{{ $child->name }}</div>
                                                            @if($childSpouse)
                                                                <div class="node-spouse">&amp; {{ $childSpouse }}</div>
                                                            @endif
                                                            <div class="node-actions">
                                                                <a href="{{ route('family.index', $child->id) }}"
                                                                   class="node-pill-btn {{ $childKidsCount > 0 ? 'has-kids' : '' }}"
                                                                   onclick="event.stopPropagation();">
                                                                    @if($childKidsCount > 0)
                                                                        Keturunan ({{ $childKidsCount }})
                                                                    @else
                                                                        Buka Cabang
                                                                    @endif
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                </ul>
                            </div>

                            @if($directChildren->count() === 0)
                                <div class="no-children-box">
                                    <p>Belum ada data keturunan untuk <strong>{{ $currentCore->name }}</strong>.</p>
                                    @if($isAdmin)
                                        <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
                                            <a href="{{ route('family.create', ['father_id' => $currentCore->id]) }}" class="km-btn km-btn-gold km-btn-sm">+ Tambah Anak (Sbg Ayah)</a>
                                            <a href="{{ route('family.create', ['mother_id' => $currentCore->id]) }}" class="km-btn km-btn-outline km-btn-sm">+ Tambah Anak (Sbg Ibu)</a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. MODE 2: TAMPILAN DAFTAR RAMAH HP -->
            <div id="listViewWrap" class="list-view-wrap">
                <div class="list-core-card" onclick="openMemberModal({{ $currentCore->id }})">
                    <img src="{{ $currentCore->avatar_url }}" alt="{{ $currentCore->name }}" class="list-core-avatar">
                    <div class="list-core-info">
                        <span class="km-badge km-badge-gold">Fokus Saat Ini &middot; Generasi {{ $coreGen }}</span>
                        <h2 class="list-core-name">{{ $currentCore->name }}</h2>
                        <div class="list-core-sub">
                            @if($coreSpouse)
                                Pasangan: <strong>{{ $coreSpouse }}</strong> &middot;
                            @endif
                            {{ $directChildren->count() }} Anak
                            @if($coreTotalDesc > $directChildren->count())
                                ({{ $coreTotalDesc }} Total Keturunan)
                            @endif
                        </div>
                    </div>
                    <div class="list-core-actions" onclick="event.stopPropagation();">
                        <button type="button" class="km-btn km-btn-outline km-btn-sm" onclick="openMemberModal({{ $currentCore->id }})">Lihat Detail</button>
                        @if($isAdmin)
                            <a href="{{ route('family.edit', $currentCore->id) }}" class="km-btn km-btn-dark km-btn-sm">Edit</a>
                            <a href="{{ route('family.create', ['father_id' => $currentCore->id]) }}" class="km-btn km-btn-gold km-btn-sm">+ Anak</a>
                        @endif
                    </div>
                </div>

                <div class="list-section-title">
                    <h3>Keturunan Langsung ({{ $directChildren->count() }})</h3>
                    <span class="km-badge km-badge-muted">Ketuk kartu untuk profil lengkap</span>
                </div>

                @if($directChildren->count() > 0)
                    <div class="children-list-grid">
                        @foreach($directChildren as $child)
                            @php
                                $childKidsCount = ($childrenMap[$child->id] ?? collect())->count();
                                $childTotalDesc = $descendantCounts[$child->id] ?? 0;
                                $childSpouse = trim((string) $child->spouse_name) !== ''
                                    ? $child->spouse_name
                                    : ($inferredSpouses[$child->id]['name'] ?? null);
                            @endphp
                            <div class="child-list-card" onclick="openMemberModal({{ $child->id }})">
                                <div class="child-list-top">
                                    <img src="{{ $child->avatar_url }}" alt="{{ $child->name }}" class="child-list-avatar" loading="lazy">
                                    <div class="child-list-body">
                                        <div class="child-list-name">{{ $child->name }}</div>
                                        <div class="child-list-meta">
                                            Gen {{ $generations[$child->id] ?? ($coreGen + 1) }}
                                            @if($childSpouse)
                                                &middot; Pasangan: {{ $childSpouse }}
                                            @endif
                                        </div>
                                        @if($child->address)
                                            <div class="child-list-meta" style="margin-top:2px;">{{ \Illuminate\Support\Str::limit($child->address, 42) }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="child-list-footer" onclick="event.stopPropagation();">
                                    <button type="button" class="node-pill-btn" onclick="openMemberModal({{ $child->id }})">Profil Detail</button>
                                    <a href="{{ route('family.index', $child->id) }}" class="node-pill-btn {{ $childKidsCount > 0 ? 'has-kids' : '' }}">
                                        @if($childKidsCount > 0)
                                            Lihat {{ $childKidsCount }} Anak &rarr;
                                        @else
                                            Buka Cabang &rarr;
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-children-box" style="margin-top:0;max-width:100%;">
                        <p>Belum ada data keturunan untuk <strong>{{ $currentCore->name }}</strong>.</p>
                        @if($isAdmin)
                            <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                                <a href="{{ route('family.create', ['father_id' => $currentCore->id]) }}" class="km-btn km-btn-gold km-btn-sm">+ Tambah Anak (Sbg Ayah)</a>
                                <a href="{{ route('family.create', ['mother_id' => $currentCore->id]) }}" class="km-btn km-btn-outline km-btn-sm">+ Tambah Anak (Sbg Ibu)</a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- 5. END LIST VIEW -->
        @else
            <div class="km-card km-card-accent" style="max-width:520px;margin:48px auto;padding:38px 26px;text-align:center;">
                <h2 style="margin-top:0;">Belum Ada Data Keluarga</h2>
                <p style="color:var(--text-muted);margin-bottom:24px;">Mulai bangun pohon silsilah dengan menambahkan leluhur atau anggota keluarga pertama.</p>
                <a href="{{ route('family.create') }}" class="km-btn km-btn-gold">+ Tambah Anggota Pertama</a>
            </div>
        @endif
    </div>
</div>

@if($currentCore)
<!-- MODAL DETAIL ANGGOTA (DI LUAR STACKING CONTEXT TREE AGAR SELALU DI ATAS NAVBAR) -->
<div id="modalOverlay" class="km-modal-overlay" onclick="closeMemberModal()"></div>
<div id="detailModal" class="km-detail-modal" role="dialog" aria-modal="true" aria-labelledby="md-name">
    <button type="button" class="modal-close-btn" onclick="closeMemberModal()" aria-label="Tutup">&times;</button>

    <div class="modal-profile-head">
        <img id="md-photo" src="" alt="Foto Profil" class="modal-avatar">
        <h2 id="md-name" class="modal-name">Nama Anggota</h2>
        <div style="display:flex;justify-content:center;gap:8px;flex-wrap:wrap;">
            <span id="md-gen" class="km-badge km-badge-gold">Generasi 1</span>
            <span id="md-desc-badge" class="km-badge km-badge-muted">0 Anak</span>
        </div>
    </div>

    <div class="modal-info-grid">
        <div class="modal-field">
            <span class="modal-field-label">Tanggal Lahir</span>
            <div class="modal-field-value" id="md-birth">-</div>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Pasangan</span>
            <div class="modal-field-value" id="md-spouse">-</div>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Ayah</span>
            <div class="modal-field-value" id="md-father">-</div>
        </div>
        <div class="modal-field">
            <span class="modal-field-label">Ibu</span>
            <div class="modal-field-value" id="md-mother">-</div>
        </div>
        <div class="modal-field full" id="md-children-wrap" style="display:none;">
            <span class="modal-field-label">Anak / Keturunan Langsung</span>
            <div class="modal-field-value" id="md-children"></div>
        </div>
        <div class="modal-field full">
            <span class="modal-field-label">Alamat Tempat Tinggal</span>
            <div class="modal-field-value" id="md-address">-</div>
            <div id="md-map-link-wrap" style="margin-top:8px;display:none;">
                <a id="md-map-link" href="#" target="_blank" rel="noopener" class="node-pill-btn">Buka di Google Maps &rarr;</a>
            </div>
        </div>
        <div class="modal-field full">
            <span class="modal-field-label">Catatan / Biografi Singkat</span>
            <div class="modal-field-value" id="md-bio" style="white-space:pre-line;">-</div>
        </div>
    </div>

    <div class="modal-actions-grid">
        <a id="md-tree-btn" href="#" class="km-btn km-btn-dark km-btn-sm">Fokus di Pohon</a>
        <a id="md-story-btn" href="#" class="km-btn km-btn-outline km-btn-sm">Kisah &amp; Biografi</a>
        @if($isAdmin)
            <a id="md-add-child-btn" href="#" class="km-btn km-btn-outline km-btn-sm">+ Tambah Anak</a>
            <a id="md-edit-btn" href="#" class="km-btn km-btn-gold km-btn-sm">Edit Data</a>
        @endif
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    const FAMILY_DATA = @json($membersPayload ?? []);
    const MEMBERS_LIST = Object.values(FAMILY_DATA);
    let currentZoom = 1;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }

    // =========================================================================
    // VIEW MODE (BAGAN POHON vs DAFTAR HP)
    // =========================================================================
    function setViewMode(mode, persist = true) {
        const treeWrap = document.getElementById('treeViewWrap');
        const listWrap = document.getElementById('listViewWrap');
        const btnTree = document.getElementById('btnModeTree');
        const btnList = document.getElementById('btnModeList');
        if (!treeWrap || !listWrap) return;

        if (mode === 'list') {
            treeWrap.classList.add('hidden');
            listWrap.classList.add('active');
            btnTree.classList.remove('active');
            btnTree.setAttribute('aria-selected', 'false');
            btnList.classList.add('active');
            btnList.setAttribute('aria-selected', 'true');
        } else {
            treeWrap.classList.remove('hidden');
            listWrap.classList.remove('active');
            btnTree.classList.add('active');
            btnTree.setAttribute('aria-selected', 'true');
            btnList.classList.remove('active');
            btnList.setAttribute('aria-selected', 'false');
            centerTreeViewport();
        }

        if (persist) {
            try { localStorage.setItem('km_tree_view_mode', mode); } catch (e) {}
        }
    }

    function centerTreeViewport() {
        const vp = document.getElementById('treeScrollViewport');
        if (!vp) return;
        requestAnimationFrame(() => {
            if (vp.scrollWidth > vp.clientWidth) {
                vp.scrollLeft = (vp.scrollWidth - vp.clientWidth) / 2;
            }
        });
    }

    // =========================================================================
    // ZOOM & DRAG TO PAN PADA BAGAN POHON
    // =========================================================================
    function applyZoom() {
        const wrapper = document.getElementById('treeZoomWrapper');
        const label = document.getElementById('zoomLabel');
        if (!wrapper) return;
        wrapper.style.transform = `scale(${currentZoom})`;
        if (label) label.textContent = Math.round(currentZoom * 100) + '%';
    }

    function changeZoom(delta) {
        currentZoom = Math.min(1.4, Math.max(0.55, +(currentZoom + delta).toFixed(2)));
        applyZoom();
    }

    function resetZoom() {
        currentZoom = 1;
        applyZoom();
        centerTreeViewport();
    }

    // =========================================================================
    // PENCARIAN INSTAN ANGGOTA KELUARGA
    // =========================================================================
    (function initSearch() {
        const input = document.getElementById('memberSearchInput');
        const resultsBox = document.getElementById('searchResultsBox');
        const clearBtn = document.getElementById('searchClearBtn');
        if (!input || !resultsBox) return;

        function renderResults(query) {
            const q = query.trim().toLowerCase();
            clearBtn.classList.toggle('visible', q.length > 0);

            if (!q) {
                resultsBox.classList.remove('open');
                resultsBox.innerHTML = '';
                return;
            }

            const matches = MEMBERS_LIST.filter(m => {
                const hay = [
                    m.name,
                    m.spouse_name || '',
                    m.address || '',
                    m.father ? m.father.name : '',
                    m.mother ? m.mother.name : ''
                ].join(' ').toLowerCase();
                return hay.includes(q);
            }).slice(0, 12);

            if (matches.length === 0) {
                resultsBox.innerHTML = '<div class="search-empty">Tidak ditemukan anggota dengan kata kunci tersebut.</div>';
                resultsBox.classList.add('open');
                return;
            }

            resultsBox.innerHTML = matches.map(m => {
                const parentText = [m.father?.name, m.mother?.name].filter(Boolean).join(' & ');
                const meta = `Gen ${m.generation}` + (parentText ? ` · Anak dari ${parentText}` : '');
                return `
                    <div class="search-result-item" onclick="window.location.href='${m.tree_url}'">
                        <div class="search-result-main">
                            <img src="${m.avatar}" alt="" class="search-result-avatar">
                            <div class="search-result-info">
                                <div class="search-result-name">${escapeHtml(m.name)}</div>
                                <div class="search-result-meta">${escapeHtml(meta)}</div>
                            </div>
                        </div>
                        <div class="search-result-actions">
                            <button type="button" class="node-pill-btn" onclick="event.stopPropagation(); openMemberModal(${m.id});">Detail</button>
                        </div>
                    </div>
                `;
            }).join('');
            resultsBox.classList.add('open');
        }

        input.addEventListener('input', (e) => renderResults(e.target.value));
        input.addEventListener('focus', () => {
            if (input.value.trim()) renderResults(input.value);
        });
        clearBtn.addEventListener('click', () => {
            input.value = '';
            renderResults('');
            input.focus();
        });
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.tree-search-wrap')) {
                resultsBox.classList.remove('open');
            }
        });
    })();

    // =========================================================================
    // MODAL DETAIL ANGGOTA
    // =========================================================================
    function openMemberModal(id) {
        const m = FAMILY_DATA[id];
        if (!m) return;

        document.getElementById('md-photo').src = m.avatar;
        document.getElementById('md-name').textContent = m.name;
        document.getElementById('md-gen').textContent = `Generasi ${m.generation}`;
        document.getElementById('md-desc-badge').textContent =
            m.descendants_count > m.children_count
                ? `${m.children_count} Anak · ${m.descendants_count} Keturunan`
                : `${m.children_count} Anak`;

        document.getElementById('md-birth').textContent = m.formatted_birth_date || 'Belum diisi';

        const spouseEl = document.getElementById('md-spouse');
        if (m.spouse_name && m.spouse_id && FAMILY_DATA[m.spouse_id]) {
            spouseEl.innerHTML = `<button type="button" class="modal-person-chip" onclick="openMemberModal(${m.spouse_id})">${escapeHtml(m.spouse_name)}</button>`;
        } else {
            spouseEl.textContent = m.spouse_name || '-';
        }

        const fatherEl = document.getElementById('md-father');
        fatherEl.innerHTML = m.father
            ? `<button type="button" class="modal-person-chip" onclick="openMemberModal(${m.father.id})">${escapeHtml(m.father.name)}</button>`
            : '-';

        const motherEl = document.getElementById('md-mother');
        motherEl.innerHTML = m.mother
            ? `<button type="button" class="modal-person-chip" onclick="openMemberModal(${m.mother.id})">${escapeHtml(m.mother.name)}</button>`
            : '-';

        const kidsWrap = document.getElementById('md-children-wrap');
        const kidsEl = document.getElementById('md-children');
        if (m.children && m.children.length > 0) {
            kidsWrap.style.display = 'block';
            kidsEl.innerHTML = m.children.map(c =>
                `<a href="${c.tree_url}" class="modal-person-chip">${escapeHtml(c.name)}</a>`
            ).join('');
        } else {
            kidsWrap.style.display = 'none';
            kidsEl.innerHTML = '';
        }

        document.getElementById('md-address').textContent = m.address || 'Belum ada data alamat';
        const mapWrap = document.getElementById('md-map-link-wrap');
        const mapLink = document.getElementById('md-map-link');
        if (m.latitude && m.longitude) {
            mapWrap.style.display = 'block';
            mapLink.href = `https://www.google.com/maps?q=${encodeURIComponent(m.latitude + ',' + m.longitude)}`;
        } else {
            mapWrap.style.display = 'none';
        }

        document.getElementById('md-bio').textContent = m.bio || 'Belum ada catatan biografi.';

        document.getElementById('md-tree-btn').href = m.tree_url;
        document.getElementById('md-story-btn').href = m.story_url;
        const addChildBtn = document.getElementById('md-add-child-btn');
        if (addChildBtn) addChildBtn.href = m.add_child_father_url;
        const editBtn = document.getElementById('md-edit-btn');
        if (editBtn) editBtn.href = m.edit_url;

        document.getElementById('detailModal').classList.add('active');
        document.getElementById('modalOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMemberModal() {
        document.getElementById('detailModal').classList.remove('active');
        document.getElementById('modalOverlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeMemberModal();
    });

    // Drag-to-pan dengan mouse di desktop + inisialisasi awal yang responsif
    document.addEventListener('DOMContentLoaded', () => {
        const vp = document.getElementById('treeScrollViewport');
        if (vp) {
            let isDown = false, startX = 0, scrollLeft = 0;
            vp.addEventListener('mousedown', (e) => {
                if (e.target.closest('.node-card') || e.target.closest('a') || e.target.closest('button')) return;
                isDown = true;
                vp.classList.add('grabbing');
                startX = e.pageX - vp.offsetLeft;
                scrollLeft = vp.scrollLeft;
            });
            vp.addEventListener('mouseleave', () => { isDown = false; vp.classList.remove('grabbing'); });
            vp.addEventListener('mouseup', () => { isDown = false; vp.classList.remove('grabbing'); });
            vp.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - vp.offsetLeft;
                vp.scrollLeft = scrollLeft - (x - startX) * 1.2;
            });
        }

        // Pulihkan preferensi tampilan user (atau otomatis mode daftar pada HP sempit bila belum memilih)
        let savedMode = null;
        try { savedMode = localStorage.getItem('km_tree_view_mode'); } catch (e) {}
        if (savedMode === 'list' || savedMode === 'tree') {
            setViewMode(savedMode, false);
        } else if (window.innerWidth <= 640) {
            setViewMode('list', false);
        } else {
            centerTreeViewport();
        }
    });
</script>
@endpush
