@extends('layouts.app')

@section('title', 'Template Surat')
@section('page-title', 'Template Surat')

@section('content')

<div class="page-header">
    <h1>Master Template Surat</h1>
    <p>Kelola template surat dinas ber-kop resmi PT Duri Rejang Berseri.</p>
</div>

<div class="notice">
    <b>Placeholder yang tersedia:</b>
    <code style="font-size:11px">@{{nomor_surat}}</code> &nbsp;
    <code style="font-size:11px">@{{tanggal_surat}}</code> &nbsp;
    <code style="font-size:11px">@{{lampiran}}</code> &nbsp;
    <code style="font-size:11px">@{{perihal}}</code> &nbsp;
    <code style="font-size:11px">@{{kepada}}</code> &nbsp;
    <code style="font-size:11px">@{{instansi_tujuan}}</code> &nbsp;
    <code style="font-size:11px">@{{isi_surat}}</code> &nbsp;
    <code style="font-size:11px">@{{alamat_tujuan}}</code> &nbsp;
    <code style="font-size:11px">@{{penandatangan_nama}}</code> &nbsp;
    <code style="font-size:11px">@{{penandatangan_jabatan}}</code>
</div>

<section class="panel">
    <div class="panel-head">
        <h3>Daftar Template</h3>
        <button class="btn btn-primary" onclick="document.getElementById('modalTambah').style.display='grid'">
            <span class="material-icons" style="font-size:16px">add</span> Tambah Template
        </button>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Template</th>
                    <th>Kode</th>
                    <th>Default Perihal</th>
                    <th>Dibuat oleh</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $i => $t)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td><b>{{ $t->templateName }}</b></td>
                        <td><code style="font-size:11px;background:#f1f3f6;padding:2px 6px;border-radius:4px">{{ $t->templateCode }}</code></td>
                        <td>{{ Str::limit($t->defaultSubject, 50) }}</td>
                        <td>{{ $t->createdBy }}</td>
                        <td>{{ $t->createdAtFormatted() }}</td>
                        <td>
                            <div class="actions">
                                <button class="action"
                                    onclick="openPreview('{{ addslashes($t->templateName) }}', `{{ addslashes($t->bodyTemplate) }}`)">
                                    Preview
                                </button>
                                <form method="POST" action="{{ route('template.destroy', $t->id) }}"
                                      onsubmit="return confirm('Hapus template {{ $t->templateName }}?')"
                                      style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action del">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">Belum ada template. Klik "Tambah Template" untuk membuat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

{{-- ─── Modal Tambah Template ───────────────────────────────────────────────── --}}
<div id="modalTambah" class="modal-backdrop" style="display:none" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal">
        <div class="modal-head">
            <h3>Tambah Template Surat</h3>
            <button class="btn-close" onclick="document.getElementById('modalTambah').style.display='none'">×</button>
        </div>
        <form method="POST" action="{{ route('template.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-grid">
                    <div class="field">
                        <label>Nama Template</label>
                        <input name="template_name" placeholder="mis. Surat Balasan Resmi" required>
                    </div>
                    <div class="field">
                        <label>Kode Template</label>
                        <input name="template_code" placeholder="mis. TMPL-BALASAN" required
                               style="text-transform:uppercase">
                        <small>Harus unik, huruf kapital dan tanda hubung.</small>
                    </div>
                    <div class="field wide">
                        <label>Default Perihal (opsional)</label>
                        <input name="default_subject" placeholder="mis. Balasan atas Surat Nomor @{{nomor_surat}}">
                    </div>
                    <div class="field wide">
                        <label>Body Template</label>
                        <textarea name="body_template" rows="8" required
                            placeholder="Tulis body surat. Gunakan placeholder seperti @{{isi_surat}} untuk konten dinamis…"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="submit" class="btn btn-primary">Simpan Template</button>
            </div>
        </form>
    </div>
</div>

{{-- ─── Modal Preview ───────────────────────────────────────────────────────── --}}
<div id="modalPreview" class="modal-backdrop" style="display:none" onclick="if(event.target===this)this.style.display='none'">
    <div class="modal">
        <div class="modal-head">
            <h3 id="previewTitle">Preview Template</h3>
            <button class="btn-close" onclick="document.getElementById('modalPreview').style.display='none'">×</button>
        </div>
        <div class="modal-body">
            <pre id="previewBody" style="white-space:pre-wrap;font-family:inherit;font-size:13px;line-height:1.7;background:#f8fafc;border:1px solid #e7edf5;border-radius:10px;padding:16px;margin:0;color:var(--ink)"></pre>
        </div>
        <div class="modal-foot">
            <button class="btn btn-outline" onclick="document.getElementById('modalPreview').style.display='none'">Tutup</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openPreview(name, body) {
    document.getElementById('previewTitle').textContent = name;
    document.getElementById('previewBody').textContent  = body;
    document.getElementById('modalPreview').style.display = 'grid';
}
</script>
@endsection
