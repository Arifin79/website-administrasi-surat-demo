@extends('layouts.app')

@section('title', 'Surat Masuk')
@section('page-title', 'Surat Masuk')

@section('content')

<div class="page-header">
    <h1>Surat Masuk</h1>
    <p>Kelola pencatatan dan informasi surat yang diterima perusahaan.</p>
</div>

<section class="panel">
    <div class="panel-head">
        <h3>Daftar Surat Masuk</h3>
        <div class="toolbar">
            <input id="searchMasuk" type="text" placeholder="Cari nomor, pengirim, perihal…" autocomplete="off">
            <button class="btn btn-primary" onclick="document.getElementById('modalTambah').style.display='grid'">
                <span class="material-icons" style="font-size:16px">add</span> Tambah Data
            </button>
        </div>
    </div>

    <div class="table-wrap">
        <table id="tableMasuk">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>No. Agenda</th>
                    <th>Nomor Surat</th>
                    <th>Tanggal Surat</th>
                    <th>Diterima</th>
                    <th>Pengirim</th>
                    <th>Perihal</th>
                    <th>Status</th>
                    <th class="no-print">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $i => $r)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><b>{{ $r->nomorAgenda }}</b></td>
                        <td>{{ $r->nomorSuratAsal }}</td>
                        <td>{{ $r->tanggalSuratFormatted() }}</td>
                        <td>{{ $r->tanggalDiterimaFormatted() }}</td>
                        <td>{{ $r->pengirim }}</td>
                        <td>{{ Str::limit($r->perihal, 50) }}</td>
                        <td><x-status-badge :status="$r->status" /></td>
                        <td class="no-print">
                            <div class="actions">
                                @if($r->filePath)
                                    <a href="{{ route('surat-masuk.berkas', $r->id) }}"
                                       target="_blank" class="action view"
                                       title="Lihat / Unduh Berkas Lampiran">
                                        <span class="material-icons" style="font-size:13px">attach_file</span> Berkas
                                    </a>
                                @endif
                                <button class="action"
                                    onclick="openEdit({{ $r->id }}, '{{ addslashes($r->nomorSuratAsal) }}', '{{ $r->tanggalSurat }}', '{{ $r->tanggalDiterima }}', '{{ addslashes($r->pengirim) }}', '{{ addslashes($r->perihal) }}', '{{ $r->status }}', {{ $r->filePath ? "'".route('surat-masuk.berkas', $r->id)."'" : 'null' }})">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('surat-masuk.destroy', $r->id) }}"
                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action del">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty">
                            Belum ada surat masuk. Klik "Tambah Data" untuk memulai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

{{-- ─── Modal Tambah ────────────────────────────────────────────────────────── --}}
<div id="modalTambah" class="modal-backdrop" style="display:none" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal">
        <div class="modal-head">
            <h3>Tambah Surat Masuk</h3>
            <button class="btn-close" onclick="document.getElementById('modalTambah').style.display='none'">×</button>
        </div>
        <form method="POST" action="{{ route('surat-masuk.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                @if($errors->any())
                    <div class="flash flash-error" style="margin-bottom:12px">✕ &nbsp;{{ $errors->first() }}</div>
                @endif
                <div class="form-grid">
                    <div class="field">
                        <label>Nomor Surat Asal</label>
                        <input name="nomor_surat_asal" placeholder="mis. 014/MSJ/X/2026" value="{{ old('nomor_surat_asal') }}" required>
                    </div>
                    <div class="field">
                        <label>Tanggal Surat</label>
                        <input name="tanggal_surat" type="date" value="{{ old('tanggal_surat') }}" required>
                    </div>
                    <div class="field">
                        <label>Tanggal Diterima</label>
                        <input name="tanggal_diterima" type="date" value="{{ old('tanggal_diterima', date('Y-m-d')) }}" required>
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <select name="status">
                            <option value="Diterima" {{ old('status') === 'Diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="Diproses" {{ old('status') === 'Diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="Selesai"  {{ old('status') === 'Selesai'  ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="field wide">
                        <label>Asal Surat / Pengirim</label>
                        <input name="pengirim" placeholder="Nama instansi / perusahaan pengirim" value="{{ old('pengirim') }}" required>
                    </div>
                    <div class="field wide">
                        <label>Perihal Surat</label>
                        <textarea name="perihal" rows="3" placeholder="Tuliskan perihal surat" required>{{ old('perihal') }}</textarea>
                    </div>
                    <div class="field wide">
                        <label>Berkas Scan <span style="font-weight:400;color:var(--muted)">(opsional)</span></label>
                        <input type="file" name="file_lampiran" accept=".pdf,.jpg,.jpeg,.png">
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
            <h3>Edit Surat Masuk</h3>
            <button class="btn-close" onclick="document.getElementById('modalEdit').style.display='none'">×</button>
        </div>
        <form id="editForm" method="POST" action="" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label>Nomor Surat Asal</label>
                        <input id="edit_nomor" name="nomor_surat_asal" required>
                    </div>
                    <div class="field">
                        <label>Tanggal Surat</label>
                        <input id="edit_tgl_surat" name="tanggal_surat" type="date" required>
                    </div>
                    <div class="field">
                        <label>Tanggal Diterima</label>
                        <input id="edit_tgl_terima" name="tanggal_diterima" type="date" required>
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <select id="edit_status" name="status">
                            <option value="Diterima">Diterima</option>
                            <option value="Diproses">Diproses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="field wide">
                        <label>Asal Surat / Pengirim</label>
                        <input id="edit_pengirim" name="pengirim" required>
                    </div>
                    <div class="field wide">
                        <label>Perihal Surat</label>
                        <textarea id="edit_perihal" name="perihal" rows="3" required></textarea>
                    </div>
                    <div class="field wide">
                        <label>Ganti Berkas Scan (opsional)</label>
                        <div id="edit_berkas_info" style="display:none;margin-bottom:6px;font-size:12px;color:var(--muted)">
                            Berkas saat ini:
                            <a id="edit_berkas_link" href="#" target="_blank" style="color:var(--blue)">Lihat Berkas</a>
                        </div>
                        <input type="file" name="file_lampiran" accept=".pdf,.jpg,.jpeg,.png">
                        <small>Kosongkan jika tidak ingin mengganti berkas. Format: PDF, JPG, PNG · Maks. 5 MB</small>
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
function openEdit(id, nomor, tglSurat, tglTerima, pengirim, perihal, status, berkasUrl) {
    document.getElementById('editForm').action = '/surat-masuk/' + id;
    document.getElementById('edit_nomor').value      = nomor;
    document.getElementById('edit_tgl_surat').value  = tglSurat;
    document.getElementById('edit_tgl_terima').value = tglTerima;
    document.getElementById('edit_pengirim').value   = pengirim;
    document.getElementById('edit_perihal').value    = perihal;
    document.getElementById('edit_status').value     = status;

    var infoEl = document.getElementById('edit_berkas_info');
    var linkEl = document.getElementById('edit_berkas_link');
    if (berkasUrl) {
        infoEl.style.display = 'block';
        linkEl.href = berkasUrl;
    } else {
        infoEl.style.display = 'none';
    }

    document.getElementById('modalEdit').style.display = 'grid';
}
document.addEventListener('turbolinks:load', function () {
    liveSearch('searchMasuk', 'tableMasuk');
    @if($errors->any())
        document.getElementById('modalTambah').style.display = 'grid';
    @endif
});
</script>
@endsection
