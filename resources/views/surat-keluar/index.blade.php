@extends('layouts.app')

@section('title', 'Surat Keluar')
@section('page-title', 'Surat Keluar')

@section('content')

<div class="page-header">
    <h1>Surat Keluar</h1>
    <p>Kelola pencatatan dan penerbitan surat resmi perusahaan.</p>
</div>

<section class="panel">
    <div class="panel-head">
        <h3>Daftar Surat Keluar</h3>
        <div class="toolbar">
            <input id="searchKeluar" type="text" placeholder="Cari nomor, tujuan, perihal…" autocomplete="off">
            <a href="{{ route('surat-keluar.create') }}" class="btn btn-success">
                <span class="material-icons" style="font-size:16px">edit_document</span> Buat via Template
            </a>
            <button class="btn btn-primary" onclick="document.getElementById('modalTambah').style.display='grid'">
                <span class="material-icons" style="font-size:16px">add</span> Input Manual
            </button>
        </div>
    </div>

    <div class="table-wrap">
        <table id="tableKeluar">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nomor Surat</th>
                    <th>Tanggal</th>
                    <th>Tujuan</th>
                    <th>Perihal</th>
                    <th>Jenis</th>
                    <th>Status</th>
                    <th class="no-print">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $r)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><b>{{ $r->nomorSurat }}</b></td>
                        <td>{{ $r->tanggalFormatted() }}</td>
                        <td>{{ $r->tujuan }}</td>
                        <td>{{ Str::limit($r->perihal, 48) }}</td>
                        <td>
                            <span class="badge {{ $r->isTemplate() ? 'badge-blue' : 'badge-gray' }}">
                                {{ $r->isTemplate() ? 'Template' : 'Manual' }}
                            </span>
                        </td>
                        <td><x-status-badge :status="$r->status" /></td>
                        <td class="no-print">
                            <div class="actions">
                                @if($r->filePath)
                                    <a href="{{ route('surat-keluar.dokumen', $r->id) }}"
                                       target="_blank" class="action view"
                                       title="Lihat / Unduh Dokumen Surat">
                                        <span class="material-icons" style="font-size:13px">picture_as_pdf</span> Dokumen
                                    </a>
                                @endif
                                <button class="action"
                                    onclick="openEdit({{ $r->id }}, '{{ addslashes($r->nomorSurat) }}', '{{ $r->tanggalSurat }}', '{{ addslashes($r->tujuan) }}', '{{ addslashes($r->perihal) }}', '{{ $r->status }}', {{ $r->filePath ? "'".route('surat-keluar.dokumen', $r->id)."'" : 'null' }})">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('surat-keluar.destroy', $r->id) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action del">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty">
                            Belum ada surat keluar. Gunakan "Buat via Template" atau "Input Manual".
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

{{-- ─── Modal Input Manual ──────────────────────────────────────────────────── --}}
<div id="modalTambah" class="modal-backdrop" style="display:none" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal">
        <div class="modal-head">
            <h3>Input Manual Surat Keluar</h3>
            <button class="btn-close" onclick="document.getElementById('modalTambah').style.display='none'">×</button>
        </div>
        <form method="POST" action="{{ route('surat-keluar.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                @if($errors->any())
                    <div class="flash flash-error" style="margin-bottom:12px">✕ &nbsp;{{ $errors->first() }}</div>
                @endif
                <div class="notice">
                    Mode ini untuk mencatat arsip surat keluar yang sudah dibuat secara manual (scan / upload).
                </div>
                <input type="hidden" name="jenis_pembuatan" value="Manual (Upload Scan)">
                <div class="form-grid">
                    <div class="field">
                        <label>Nomor Surat (otomatis, dapat diedit)</label>
                        <input name="nomor_surat" value="{{ old('nomor_surat', \App\Models\OutgoingLetter::nextLetterNumber()) }}" required>
                    </div>
                    <div class="field">
                        <label>Tanggal Surat</label>
                        <input name="tanggal_surat" type="date" value="{{ old('tanggal_surat', date('Y-m-d')) }}" required>
                    </div>
                    <div class="field wide">
                        <label>Tujuan / Instansi Penerima</label>
                        <input name="tujuan" placeholder="Nama instansi / perusahaan penerima" value="{{ old('tujuan') }}" required>
                    </div>
                    <div class="field wide">
                        <label>Perihal Surat</label>
                        <textarea name="perihal" rows="3" placeholder="Tuliskan perihal surat" required>{{ old('perihal') }}</textarea>
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <select name="status">
                            <option value="Draft"    {{ old('status') === 'Draft'    ? 'selected' : '' }}>Draft</option>
                            <option value="Terkirim" {{ old('status') === 'Terkirim' ? 'selected' : '' }}>Terkirim</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Berkas Surat <span style="font-weight:400;color:var(--muted)">(opsional)</span></label>
                        <input type="file" name="file_dokumen" accept=".pdf,.jpg,.jpeg,.png">
                        <small>PDF, JPG, PNG &middot; Maks. 5 MB</small>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

{{-- ─── Modal Edit ──────────────────────────────────────────────────────────── --}}
<div id="modalEdit" class="modal-backdrop" style="display:none" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal">
        <div class="modal-head">
            <h3>Edit Surat Keluar</h3>
            <button class="btn-close" onclick="document.getElementById('modalEdit').style.display='none'">×</button>
        </div>
        <form id="editForm" method="POST" action="">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label>Nomor Surat</label>
                        <input id="edit_nomor" name="nomor_surat" required>
                    </div>
                    <div class="field">
                        <label>Tanggal Surat</label>
                        <input id="edit_tanggal" name="tanggal_surat" type="date" required>
                    </div>
                    <div class="field wide">
                        <label>Tujuan / Instansi Penerima</label>
                        <input id="edit_tujuan" name="tujuan" required>
                    </div>
                    <div class="field wide">
                        <label>Perihal Surat</label>
                        <textarea id="edit_perihal" name="perihal" rows="3" required></textarea>
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <select id="edit_status" name="status">
                            <option value="Draft">Draft</option>
                            <option value="Terkirim">Terkirim</option>
                        </select>
                    </div>
                    <div class="field wide" id="edit_dokumen_wrap" style="display:none">
                        <label>Dokumen Tersimpan</label>
                        <div style="font-size:12px;color:var(--muted)">
                            <a id="edit_dokumen_link" href="#" target="_blank"
                               style="color:var(--blue);display:inline-flex;align-items:center;gap:4px">
                                <span class="material-icons" style="font-size:14px">picture_as_pdf</span>
                                Lihat / Unduh Dokumen PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openEdit(id, nomor, tanggal, tujuan, perihal, status, dokumenUrl) {
    document.getElementById('editForm').action = '/surat-keluar/' + id;
    document.getElementById('edit_nomor').value   = nomor;
    document.getElementById('edit_tanggal').value = tanggal;
    document.getElementById('edit_tujuan').value  = tujuan;
    document.getElementById('edit_perihal').value = perihal;
    document.getElementById('edit_status').value  = status;

    var wrapEl = document.getElementById('edit_dokumen_wrap');
    var linkEl = document.getElementById('edit_dokumen_link');
    if (dokumenUrl) {
        wrapEl.style.display = 'block';
        linkEl.href = dokumenUrl;
    } else {
        wrapEl.style.display = 'none';
    }

    document.getElementById('modalEdit').style.display = 'grid';
}
document.addEventListener('turbolinks:load', function () {
    liveSearch('searchKeluar', 'tableKeluar');
    @if($errors->any())
        document.getElementById('modalTambah').style.display = 'grid';
    @endif
});
</script>
@endsection
