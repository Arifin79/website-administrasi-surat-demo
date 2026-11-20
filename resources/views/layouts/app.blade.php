<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | PT Duri Rejang Berseri</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Critical inline styles: set background before external CSS loads to prevent white flash --}}
    <style>html,body{margin:0;background:#f4f7fb}</style>
    {{-- Preconnect to Google Fonts CDN to avoid cold DNS+TLS on each navigation --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/turbolinks/5.2.0/turbolinks.js" defer></script>
</head>
<body>
@php
    $authUser = session('auth_user');
    $isAdmin  = ($authUser['role'] ?? '') === 'admin';
    $initial  = strtoupper(mb_substr($authUser['name'] ?? 'U', 0, 1));
    $current  = request()->route()->getName();
@endphp

<div class="app">

    {{-- ─── Sidebar ─────────────────────────────────────────────────────────── --}}
    <aside class="sidebar">
        <div class="side-brand">
            <div class="logo"><span class="material-icons">anchor</span></div>
            <div>
                <strong>DURI REJANG</strong>
                <small>BERSERI • E-ADMINISTRASI</small>
            </div>
        </div>

        <div class="nav-label">Menu Utama</div>
        <nav class="nav">
            <a href="{{ route('dashboard') }}" class="{{ str_starts_with($current, 'dashboard') ? 'active' : '' }}">
                <span class="ico material-icons">grid_view</span><span class="nav-text">Dashboard</span>
            </a>
            <a href="{{ route('surat-masuk.index') }}" class="{{ str_starts_with($current, 'surat-masuk') ? 'active' : '' }}">
                <span class="ico material-icons">move_to_inbox</span><span class="nav-text">Surat Masuk</span>
            </a>
            <a href="{{ route('surat-keluar.index') }}" class="{{ str_starts_with($current, 'surat-keluar') ? 'active' : '' }}">
                <span class="ico material-icons">outbox</span><span class="nav-text">Surat Keluar</span>
            </a>
            <a href="{{ route('arsip.index') }}" class="{{ str_starts_with($current, 'arsip') ? 'active' : '' }}">
                <span class="ico material-icons">folder_open</span><span class="nav-text">Arsip Surat</span>
            </a>
            <a href="{{ route('laporan.index') }}" class="{{ str_starts_with($current, 'laporan') ? 'active' : '' }}">
                <span class="ico material-icons">bar_chart</span><span class="nav-text">Laporan</span>
            </a>

            @if($isAdmin)
                <a href="{{ route('template.index') }}" class="{{ str_starts_with($current, 'template') ? 'active' : '' }}">
                    <span class="ico material-icons">article</span><span class="nav-text">Template Surat</span>
                </a>
                <a href="{{ route('pengguna.index') }}" class="{{ str_starts_with($current, 'pengguna') ? 'active' : '' }}">
                    <span class="ico material-icons">manage_accounts</span><span class="nav-text">Manajemen Pengguna</span>
                </a>
            @endif
        </nav>

        <div class="side-bottom">
            Sistem Informasi Administrasi Surat-Menyurat<br>
            PT Duri Rejang Berseri &copy; 2026
        </div>
    </aside>

    {{-- ─── Main Area ───────────────────────────────────────────────────────── --}}
    <div class="main">

        {{-- Topbar --}}
        <header class="topbar">
            <div class="crumb">
                Sistem / <strong>@yield('page-title', 'Dashboard')</strong>
            </div>
            <div class="user-info">
                <div class="avatar">{{ $initial }}</div>
                <div>
                    <b>{{ $authUser['name'] ?? '-' }}</b>
                    <small>{{ $isAdmin ? 'Admin' : 'Staf Administrasi' }}</small>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button type="submit" class="btn btn-light" style="padding:7px 12px;font-size:12px;margin-left:6px">
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        {{-- Content --}}
        <main class="content">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="flash flash-success">✓ &nbsp;{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash flash-error">✕ &nbsp;{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
// Simple live-search for tables
function liveSearch(inputId, tableId) {
    var input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('input', function () {
        var q = this.value.toLowerCase();
        document.querySelectorAll('#' + tableId + ' tbody tr').forEach(function (tr) {
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}
</script>

@yield('scripts')
</body>
</html>
