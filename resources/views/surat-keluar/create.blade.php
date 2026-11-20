@extends('layouts.app')

@section('title', 'Buat Surat via Template')
@section('page-title', 'Buat Surat via Template')

@section('content')

<div class="page-header">
    <h1>Generator Surat Resmi</h1>
    <p>Buat surat dinas ber-kop PT Duri Rejang Berseri dengan pratinjau langsung sebelum mencetak.</p>
</div>

<form method="POST" action="{{ route('surat-keluar.store-template') }}" id="templateForm">
@csrf

<div class="template-split">

    {{-- ─── Form Side ─────────────────────────────────────────────────────── --}}
    <div class="form-side">
        <section class="panel">
            <div class="panel-head">
                <h3>Data Surat</h3>
            </div>
            <div class="panel-body">

                <div class="field">
                    <label>Pilih Template</label>
                    <select name="template_id" id="templateSelect">
                        <option value="">— Tanpa Template —</option>
                        @foreach($templates as $t)
                            <option value="{{ $t->id }}">{{ $t->templateName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>Nomor Surat</label>
                    <input name="nomor_surat" id="f_nomor" value="{{ $nomorOtomatis }}" required
                        oninput="sync()" placeholder="mis. 005/DRB/XI/2026">
                    <small>Format otomatis: Urutan/DRB/Bulan Romawi/Tahun</small>
                </div>

                <div class="field">
                    <label>Tanggal Surat</label>
                    <input name="tanggal_surat" id="f_tanggal" type="text"
                        value="{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}"
                        required oninput="sync()" placeholder="17 November 2026">
                </div>

                <div class="field">
                    <label>Kepada Yth.</label>
                    <input name="kepada" id="f_kepada" required oninput="sync()"
                        placeholder="Nama / Jabatan penerima">
                </div>

                <div class="field">
                    <label>Instansi / Perusahaan Tujuan</label>
                    <input name="instansi_tujuan" id="f_instansi" required oninput="sync()"
                        placeholder="Nama instansi penerima">
                </div>

                {{-- Hidden tujuan for DB (combined) --}}
                <input type="hidden" name="tujuan" id="f_tujuan">

                <div class="field">
                    <label>Alamat Tujuan</label>
                    <input name="alamat_tujuan" id="f_alamat" value="di Tempat" required oninput="sync()">
                </div>

                <div class="field">
                    <label>Lampiran</label>
                    <input name="lampiran" id="f_lampiran" value="-" oninput="sync()"
                        placeholder="mis. 1 (Satu) Berkas atau -">
                </div>

                <div class="field">
                    <label>Perihal</label>
                    <input name="perihal" id="f_perihal" required oninput="sync()"
                        placeholder="Perihal surat resmi">
                </div>

                <div class="field">
                    <label>Isi Surat</label>
                    <textarea name="isi_surat" id="f_isi" rows="7" required oninput="sync()"
                        placeholder="Tuliskan paragraf pembuka, isi inti, dan penutup…"></textarea>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 16px">
                    <div class="field">
                        <label>Nama Penandatangan</label>
                        <input name="penandatangan_nama" id="f_ttd_nama"
                            value="Ir. Dedy Susanto" required oninput="sync()">
                    </div>
                    <div class="field">
                        <label>Jabatan Penandatangan</label>
                        <input name="penandatangan_jabatan" id="f_ttd_jabatan"
                            value="Direktur Utama" required oninput="sync()">
                    </div>
                </div>

                <div class="field">
                    <label>Status</label>
                    <select name="status">
                        <option value="Draft">Simpan sebagai Draft</option>
                        <option value="Terkirim">Tandai Terkirim</option>
                    </select>
                </div>
            </div>
        </section>

        <div class="no-print" style="display:flex;gap:10px;margin-top:4px">
            <a href="{{ route('surat-keluar.index') }}" class="btn btn-outline">
                <span class="material-icons" style="font-size:16px">arrow_back</span> Kembali
            </a>
            <button type="button" onclick="window.print()" class="btn btn-light">
                <span class="material-icons" style="font-size:16px">print</span> Cetak Preview
            </button>
            <button type="submit" class="btn btn-primary" onclick="syncTujuan()">
                <span class="material-icons" style="font-size:16px">save</span> Simpan Surat
            </button>
        </div>
    </div>

    {{-- ─── Preview Side ───────────────────────────────────────────────────── --}}
    <div class="preview-side">
        <div class="preview-label"><span class="material-icons" style="font-size:13px;vertical-align:middle">visibility</span> &nbsp; PRATINJAU DOKUMEN A4</div>

        <div class="a4-sheet" id="a4Preview">
            {{-- Kop Surat --}}
            <img class="kop-image" src="{{ asset('images/kop-surat.png') }}" alt="Kop Surat PT. Duri Rejang Berseri">

            {{-- Meta surat --}}
            <div class="surat-meta">
                <table>
                    <tr><td>Nomor</td><td>: <span id="p_nomor">—</span></td></tr>
                    <tr><td>Lampiran</td><td>: <span id="p_lampiran">-</span></td></tr>
                    <tr><td>Perihal</td><td>: <span id="p_perihal">—</span></td></tr>
                </table>
            </div>

            <div style="margin-bottom:14px;font-size:11px">
                <div>Kepada Yth.</div>
                <div><b id="p_kepada">—</b></div>
                <div id="p_instansi">—</div>
                <div id="p_alamat">di Tempat</div>
            </div>

            <div style="text-align:right;margin-bottom:16px;font-size:11px">
                Jakarta, <span id="p_tanggal">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}</span>
            </div>

            <div style="margin-bottom:14px;font-size:11px">
                Dengan hormat,
            </div>

            <div class="surat-body" id="p_isi">
                <span style="color:#bbb;font-style:italic">Isi surat akan muncul di sini…</span>
            </div>

            {{-- TTD --}}
            <div class="surat-ttd">
                <div>
                    <div>Hormat kami,</div>
                    <div style="margin-top:3px;font-weight:bold">PT. DURI REJANG BERSERI</div>
                </div>
                <div style="margin-top:46px" class="penandatangan">
                    <b id="p_ttd_nama">Ir. Dedy Susanto</b>
                    <div id="p_ttd_jabatan">Direktur Utama</div>
                </div>
            </div>
        </div>
    </div>

</div>
</form>

@endsection

@section('scripts')
<script>
function sync() {
    setText('p_nomor',      get('f_nomor'));
    setText('p_lampiran',   get('f_lampiran') || '-');
    setText('p_perihal',    get('f_perihal'));
    setText('p_kepada',     get('f_kepada'));
    setText('p_instansi',   get('f_instansi'));
    setText('p_alamat',     get('f_alamat') || 'di Tempat');
    setText('p_tanggal',    get('f_tanggal'));
    setText('p_ttd_nama',   get('f_ttd_nama'));
    setText('p_ttd_jabatan',get('f_ttd_jabatan'));

    const isi = get('f_isi').trim();
    document.getElementById('p_isi').innerHTML = isi
        ? isi.replace(/\n/g, '<br>')
        : '<span style="color:#bbb;font-style:italic">Isi surat akan muncul di sini…</span>';
}

function get(id) {
    return document.getElementById(id)?.value ?? '';
}

function setText(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val || '—';
}

function syncTujuan() {
    const instansi = get('f_instansi');
    document.getElementById('f_tujuan').value = instansi;
}

document.addEventListener('turbolinks:load', sync);

</script>
@endsection
