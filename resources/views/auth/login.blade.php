@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="login-wrap">

    {{-- ─── Left Art Panel ──────────────────────────────────────────────────── --}}
    <section class="login-art">
        <div class="brand-mark"><span class="material-icons">anchor</span></div>
        <h1>Administrasi Surat Lebih Tertata dan Efisien.</h1>
        <p>Sistem informasi pengolahan data administrasi surat-menyurat PT Duri Rejang Berseri. Kelola surat masuk, surat keluar, arsip, dan laporan dalam satu tempat.</p>
        <div class="login-points">
            <span>Pencatatan surat masuk &amp; keluar terpusat</span>
            <span>Nomor surat otomatis tanpa duplikasi</span>
            <span>Template kop surat resmi &amp; generator PDF</span>
            <span>Rekapitulasi laporan periodik praktis</span>
        </div>
    </section>

    {{-- ─── Login Form Panel ────────────────────────────────────────────────── --}}
    <section class="login-panel">
        <div class="login-card">
            <h2>Selamat Datang</h2>
            <p>Masuk ke akun administrasi Anda.</p>

            @if($errors->any())
                <div class="flash flash-error" style="margin-bottom:16px">
                    ✕ &nbsp;{{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="field">
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text"
                        value="{{ old('username') }}"
                        placeholder="Masukkan username" required autocomplete="username">
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <div class="pw-wrap">
                        <input id="password" name="password" type="password"
                            placeholder="Masukkan password" required autocomplete="current-password">
                        <button type="button" class="pw-toggle" onclick="togglePw('password', this)" tabindex="-1">
                            <span class="material-icons">visibility</span>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary login-btn">
                    Masuk ke Dashboard
                </button>
                <p class="forgot-hint">
                    Lupa password? Hubungi administrator sistem.
                </p>
            </form>

        </div>
    </section>

</div>

@section('scripts')
<script>
function togglePw(inputId, btn) {
    var input = document.getElementById(inputId);
    var icon  = btn.querySelector('.material-icons');
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}
</script>
@endsection
