@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna')

@section('content')

<div class="page-header">
    <h1>Manajemen Pengguna</h1>
    <p>Kelola akun dan hak akses pengguna sistem administrasi.</p>
</div>

<section class="panel">
    <div class="panel-head">
        <h3>Daftar Pengguna Sistem</h3>
        <button class="btn btn-primary" onclick="document.getElementById('modalTambah').style.display='grid'">
            <span class="material-icons" style="font-size:16px">person_add</span> Tambah Pengguna
        </button>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Hak Akses</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $u)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <div class="user-row-main">
                                <div class="user-avatar-sm">{{ $u->initial() }}</div>
                                <b>{{ $u->name }}</b>
                            </div>
                        </td>
                        <td>{{ $u->username }}</td>
                        <td><x-status-badge :status="$u->roleLabel()" /></td>
                        <td>{{ $u->createdAtFormatted() }}</td>
                        <td>
                            @if($u->username === 'admin')
                                <span style="font-size:11px;color:var(--muted)">Akun utama</span>
                            @else
                                <div class="actions">
                                    <button class="action"
                                        onclick="openEdit('{{ $u->username }}', '{{ addslashes($u->name) }}', '{{ $u->role }}')">
                                        <span class="material-icons" style="font-size:13px">edit</span> Edit
                                    </button>
                                    <button class="action"
                                        onclick="openReset('{{ $u->username }}', '{{ addslashes($u->name) }}')">
                                        <span class="material-icons" style="font-size:13px">lock_reset</span> Reset
                                    </button>
                                    <form method="POST" action="{{ route('pengguna.destroy', $u->username) }}"
                                          onsubmit="return confirm('Hapus akun {{ $u->username }}?')"
                                          style="display:inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="action del">Hapus</button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">Belum ada pengguna terdaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

{{-- ─── Modal Tambah Pengguna ───────────────────────────────────────────────── --}}
<div id="modalTambah" class="modal-backdrop" style="display:none" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal modal-sm">
        <div class="modal-head">
            <h3>Tambah Pengguna Baru</h3>
            <button class="btn-close" onclick="document.getElementById('modalTambah').style.display='none'">×</button>
        </div>
        <form method="POST" action="{{ route('pengguna.store') }}">
            @csrf
            <div class="modal-body">
                @if($errors->any())
                    <div class="flash flash-error">✕ &nbsp;{{ $errors->first() }}</div>
                @endif
                <div class="field">
                    <label>Nama Lengkap</label>
                    <input name="name" value="{{ old('name') }}" placeholder="Nama lengkap pengguna" required>
                </div>
                <div class="field">
                    <label>Username</label>
                    <input name="username" value="{{ old('username') }}" placeholder="Username untuk login" required>
                    <small>Gunakan huruf kecil, angka, dan titik. Contoh: budi.santoso</small>
                </div>
                <div class="field">
                    <label>Password</label>
                    <div class="pw-wrap">
                        <input id="tambah_password" name="password" type="password"
                               placeholder="Minimal 6 karakter" required minlength="6">
                        <button type="button" class="pw-toggle"
                                onclick="togglePw('tambah_password', this)" tabindex="-1">
                            <span class="material-icons">visibility</span>
                        </button>
                    </div>
                </div>
                <div class="field">
                    <label>Hak Akses</label>
                    <select name="role">
                        <option value="staf">Staf Administrasi</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <div class="modal-foot">
                <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

{{-- ─── Modal Edit Pengguna ────────────────────────────────────────────────── --}}
<div id="modalEdit" class="modal-backdrop" style="display:none" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal modal-sm">
        <div class="modal-head">
            <h3>Edit Pengguna</h3>
            <button class="btn-close" onclick="document.getElementById('modalEdit').style.display='none'">×</button>
        </div>
        <form id="editForm" method="POST" action="">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="field">
                    <label>Nama Lengkap</label>
                    <input id="edit_name" name="name" placeholder="Nama lengkap pengguna" required>
                </div>
                <div class="field">
                    <label>Username</label>
                    <input id="edit_username" name="new_username" placeholder="Username login" required>
                    <small>Huruf kecil, angka, dan titik. Contoh: budi.santoso</small>
                </div>
                <div class="field">
                    <label>Hak Akses</label>
                    <select id="edit_role" name="role">
                        <option value="staf">Staf Administrasi</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <div class="modal-foot">
                <button type="submit" class="btn btn-primary">
                    <span class="material-icons" style="font-size:15px">save</span> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ─── Modal Reset Password ───────────────────────────────────────────────── --}}
<div id="modalReset" class="modal-backdrop" style="display:none" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal modal-sm">
        <div class="modal-head">
            <h3>Reset Password</h3>
            <button class="btn-close" onclick="document.getElementById('modalReset').style.display='none'">×</button>
        </div>
        <form id="resetForm" method="POST" action="">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="notice" style="margin-bottom:16px">
                    Reset password untuk akun: <b id="reset_nama">—</b>
                    (<code id="reset_username_label" style="font-size:11px">—</code>)
                </div>
                <div class="field">
                    <label>Password Baru</label>
                    <div class="pw-wrap">
                        <input id="reset_password" name="password_baru" type="password"
                               placeholder="Minimal 6 karakter" required minlength="6"
                               autocomplete="new-password">
                        <button type="button" class="pw-toggle"
                                onclick="togglePw('reset_password', this)" tabindex="-1">
                            <span class="material-icons">visibility</span>
                        </button>
                    </div>
                </div>
                <div class="field">
                    <label>Konfirmasi Password Baru</label>
                    <div class="pw-wrap">
                        <input id="reset_password_confirm" name="password_baru_confirmation"
                               type="password" placeholder="Ulangi password baru"
                               required minlength="6" autocomplete="new-password">
                        <button type="button" class="pw-toggle"
                                onclick="togglePw('reset_password_confirm', this)" tabindex="-1">
                            <span class="material-icons">visibility</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="submit" class="btn btn-primary">
                    <span class="material-icons" style="font-size:15px">lock_reset</span> Simpan Password
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

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

@if($errors->any())
    document.getElementById('modalTambah').style.display = 'grid';
@endif

function openEdit(username, nama, role) {
    document.getElementById('editForm').action = '/pengguna/' + username;
    document.getElementById('edit_name').value     = nama;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_role').value     = role;
    document.getElementById('modalEdit').style.display = 'grid';
}

function openReset(username, nama) {
    document.getElementById('resetForm').action = '/pengguna/' + username + '/reset-password';
    document.getElementById('reset_nama').textContent = nama;
    document.getElementById('reset_username_label').textContent = username;
    // Clear fields and reset eye icons
    ['reset_password', 'reset_password_confirm'].forEach(function(id) {
        var inp = document.getElementById(id);
        inp.value = '';
        inp.type  = 'password';
    });
    document.querySelectorAll('#modalReset .pw-toggle .material-icons').forEach(function(icon) {
        icon.textContent = 'visibility';
    });
    document.getElementById('modalReset').style.display = 'grid';
}
</script>
@endsection
