@extends('layouts.app')

@section('title', 'Arsip Surat')
@section('page-title', 'Arsip Surat')

@section('content')

<div class="page-header">
    <h1>Arsip Digital Terpadu</h1>
    <p>Pusat penyimpanan dan pencarian seluruh dokumen surat masuk maupun surat keluar.</p>
</div>

<div class="notice">
    Arsip menampilkan seluruh data surat yang telah dicatat di dalam sistem.
    Gunakan kolom pencarian dan filter untuk menemukan dokumen dengan cepat.
</div>

<section class="panel">
    <div class="panel-head">
        <h3>Seluruh Arsip Dokumen</h3>
        <button class="btn btn-outline no-print" onclick="window.print()">
            <span class="material-icons" style="font-size:16px">print</span> Cetak Daftar
        </button>
    </div>

    <div class="filter-bar">
        <input id="searchArsip" type="text" placeholder="Cari arsip surat…" autocomplete="off">
        <select id="filterJenis" onchange="filterArsip()">
            <option value="">Semua Jenis</option>
            <option value="masuk">Surat Masuk</option>
            <option value="keluar">Surat Keluar</option>
        </select>
        <select id="filterTahun" onchange="filterArsip()">
            <option value="">Semua Tahun</option>
            @foreach($tahunList as $tahun)
                <option value="{{ $tahun }}">{{ $tahun }}</option>
            @endforeach
        </select>
        <select id="filterBulan" onchange="filterArsip()">
            <option value="">Semua Bulan</option>
            <option value="1">Januari</option>
            <option value="2">Februari</option>
            <option value="3">Maret</option>
            <option value="4">April</option>
            <option value="5">Mei</option>
            <option value="6">Juni</option>
            <option value="7">Juli</option>
            <option value="8">Agustus</option>
            <option value="9">September</option>
            <option value="10">Oktober</option>
            <option value="11">November</option>
            <option value="12">Desember</option>
        </select>
        <select id="filterStatus" onchange="filterArsip()">
            <option value="">Semua Status</option>
            <option value="Diterima">Diterima</option>
            <option value="Diproses">Diproses</option>
            <option value="Selesai">Selesai</option>
            <option value="Draft">Draft</option>
            <option value="Terkirim">Terkirim</option>
        </select>
    </div>

    <div class="table-wrap">
        <table id="tableArsip">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Jenis</th>
                    <th>Nomor Surat</th>
                    <th>Tanggal</th>
                    <th>Pengirim / Tujuan</th>
                    <th>Perihal</th>
                    <th>Status</th>
                    <th class="no-print">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($arsip as $i => $item)
                    @php
                        $nomor      = $item['jenis'] === 'masuk' ? $item['nomor_surat_asal'] : $item['nomor_surat'];
                        $pihak      = $item['jenis'] === 'masuk' ? $item['pengirim'] : $item['tujuan'];
                        $tanggalFmt = \Carbon\Carbon::parse($item['tanggal_surat'])->locale('id')->isoFormat('D MMM Y');
                        $tahunRow   = date('Y', strtotime($item['tanggal_surat']));
                        $bulanRow   = (int) date('n', strtotime($item['tanggal_surat']));

                        // URL berkas jika ada
                        $berkasUrl  = null;
                        if ($item['file_path']) {
                            $berkasUrl = $item['jenis'] === 'masuk'
                                ? route('surat-masuk.berkas', $item['id'])
                                : route('surat-keluar.dokumen', $item['id']);
                        }
                    @endphp
                    <tr data-jenis="{{ $item['jenis'] }}"
                        data-status="{{ $item['status'] }}"
                        data-tahun="{{ $tahunRow }}"
                        data-bulan="{{ $bulanRow }}">
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <span class="badge {{ $item['jenis'] === 'masuk' ? 'badge-blue' : 'badge-green' }}">
                                <span class="material-icons" style="font-size:11px;vertical-align:middle">
                                    {{ $item['jenis'] === 'masuk' ? 'south_west' : 'north_east' }}
                                </span>
                                {{ $item['jenis'] === 'masuk' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                        <td><b>{{ $nomor }}</b></td>
                        <td>{{ $tanggalFmt }}</td>
                        <td>{{ Str::limit($pihak, 35) }}</td>
                        <td>{{ Str::limit($item['perihal'], 48) }}</td>
                        <td><x-status-badge :status="$item['status']" /></td>
                        <td class="no-print">
                            <div class="actions">
                                <button class="action view"
                                    onclick="openDetail(
                                        '{{ $item['jenis'] }}',
                                        '{{ addslashes($nomor ?? '') }}',
                                        '{{ addslashes($item['nomor_agenda'] ?? '') }}',
                                        '{{ $tanggalFmt }}',
                                        '{{ addslashes($pihak ?? '') }}',
                                        '{{ addslashes($item['perihal']) }}',
                                        '{{ $item['status'] }}',
                                        {{ $berkasUrl ? "'$berkasUrl'" : 'null' }}
                                    )">
                                    <span class="material-icons" style="font-size:13px">info</span> Detail
                                </button>
                                @if($berkasUrl)
                                    <a href="{{ $berkasUrl }}" target="_blank" class="action"
                                       title="{{ $item['jenis'] === 'masuk' ? 'Lihat Berkas Lampiran' : 'Lihat Dokumen PDF' }}">
                                        <span class="material-icons" style="font-size:13px">
                                            {{ $item['jenis'] === 'masuk' ? 'attach_file' : 'picture_as_pdf' }}
                                        </span> Berkas
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty">Belum ada arsip dokumen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

{{-- ─── Modal Detail ────────────────────────────────────────────────────────── --}}
<div id="modalDetail" class="modal-backdrop" style="display:none"
     onclick="if(event.target===this)this.style.display='none'">
    <div class="modal modal-sm">
        <div class="modal-head">
            <h3>Detail Arsip Surat</h3>
            <button class="btn-close" onclick="document.getElementById('modalDetail').style.display='none'">×</button>
        </div>
        <div class="modal-body">
            <table style="width:100%;border-collapse:collapse;font-size:13px">
                <tr>
                    <td style="padding:7px 0;color:var(--muted);width:130px;vertical-align:top">Jenis Surat</td>
                    <td style="padding:7px 0;vertical-align:top" id="d_jenis_badge">—</td>
                </tr>
                <tr id="d_row_agenda" style="display:none">
                    <td style="padding:7px 0;color:var(--muted);border-top:1px solid var(--line);vertical-align:top">No. Agenda</td>
                    <td style="padding:7px 0;border-top:1px solid var(--line);vertical-align:top" id="d_agenda">—</td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:var(--muted);border-top:1px solid var(--line);vertical-align:top">Nomor Surat</td>
                    <td style="padding:7px 0;border-top:1px solid var(--line);font-weight:600;vertical-align:top" id="d_nomor">—</td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:var(--muted);border-top:1px solid var(--line);vertical-align:top">Tanggal</td>
                    <td style="padding:7px 0;border-top:1px solid var(--line);vertical-align:top" id="d_tanggal">—</td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:var(--muted);border-top:1px solid var(--line);vertical-align:top" id="d_pihak_label">Pengirim</td>
                    <td style="padding:7px 0;border-top:1px solid var(--line);vertical-align:top" id="d_pihak">—</td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:var(--muted);border-top:1px solid var(--line);vertical-align:top">Perihal</td>
                    <td style="padding:7px 0;border-top:1px solid var(--line);vertical-align:top" id="d_perihal">—</td>
                </tr>
                <tr>
                    <td style="padding:7px 0;color:var(--muted);border-top:1px solid var(--line);vertical-align:top">Status</td>
                    <td style="padding:7px 0;border-top:1px solid var(--line);vertical-align:top" id="d_status">—</td>
                </tr>
            </table>

            <div id="d_berkas_wrap" style="display:none;margin-top:14px;padding-top:14px;border-top:1px solid var(--line)">
                <a id="d_berkas_link" href="#" target="_blank"
                   class="btn btn-outline" style="font-size:12px;padding:8px 14px">
                    <span class="material-icons" style="font-size:15px">open_in_new</span>
                    <span id="d_berkas_label">Lihat Berkas</span>
                </a>
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn btn-outline"
                    onclick="document.getElementById('modalDetail').style.display='none'">Tutup</button>
        </div>
    </div>
</div>

{{-- Print header (only visible when printing) --}}
<div class="print-header" style="margin-bottom:20px">
    <h2 style="font-size:16px;margin:0 0 4px">Daftar Arsip Surat</h2>
    <p style="font-size:12px;color:#555">PT Duri Rejang Berseri &nbsp;·&nbsp; Dicetak pada {{ date('d M Y') }}</p>
    <hr>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('turbolinks:load', function () {
    var input = document.getElementById('searchArsip');
    if (input) input.addEventListener('input', filterArsip);
});

function filterArsip() {
    var q      = document.getElementById('searchArsip').value.toLowerCase();
    var jenis  = document.getElementById('filterJenis').value.toLowerCase();
    var status = document.getElementById('filterStatus').value;
    var tahun  = document.getElementById('filterTahun').value;
    var bulan  = document.getElementById('filterBulan').value;

    document.querySelectorAll('#tableArsip tbody tr').forEach(function (tr) {
        var matchText   = tr.textContent.toLowerCase().includes(q);
        var matchJenis  = !jenis  || tr.dataset.jenis  === jenis;
        var matchStatus = !status || tr.dataset.status === status;
        var matchTahun  = !tahun  || tr.dataset.tahun  === tahun;
        var matchBulan  = !bulan  || tr.dataset.bulan  === bulan;
        tr.style.display = (matchText && matchJenis && matchStatus && matchTahun && matchBulan) ? '' : 'none';
    });
}

var statusColors = {
    'Diterima': 'badge-blue',
    'Diproses': 'badge-orange',
    'Selesai':  'badge-green',
    'Draft':    'badge-orange',
    'Terkirim': 'badge-green',
};

function openDetail(jenis, nomor, agenda, tanggal, pihak, perihal, status, berkasUrl) {
    // Badge jenis
    var jenisHtml = jenis === 'masuk'
        ? '<span class="badge badge-blue">Surat Masuk</span>'
        : '<span class="badge badge-green">Surat Keluar</span>';
    document.getElementById('d_jenis_badge').innerHTML = jenisHtml;

    // No. Agenda (hanya masuk)
    var agendaRow = document.getElementById('d_row_agenda');
    if (agenda) {
        agendaRow.style.display = '';
        document.getElementById('d_agenda').textContent = agenda;
    } else {
        agendaRow.style.display = 'none';
    }

    document.getElementById('d_nomor').textContent   = nomor   || '—';
    document.getElementById('d_tanggal').textContent = tanggal || '—';
    document.getElementById('d_perihal').textContent = perihal || '—';

    // Label pengirim vs tujuan
    document.getElementById('d_pihak_label').textContent = jenis === 'masuk' ? 'Pengirim' : 'Tujuan';
    document.getElementById('d_pihak').textContent = pihak || '—';

    // Badge status
    var badgeClass = statusColors[status] || 'badge-gray';
    document.getElementById('d_status').innerHTML = '<span class="badge ' + badgeClass + '">' + status + '</span>';

    // Berkas
    var berkasWrap  = document.getElementById('d_berkas_wrap');
    var berkasLink  = document.getElementById('d_berkas_link');
    var berkasLabel = document.getElementById('d_berkas_label');
    if (berkasUrl) {
        berkasWrap.style.display = 'block';
        berkasLink.href = berkasUrl;
        berkasLabel.textContent = jenis === 'masuk' ? 'Lihat Berkas Lampiran' : 'Lihat Dokumen PDF';
    } else {
        berkasWrap.style.display = 'none';
    }

    document.getElementById('modalDetail').style.display = 'grid';
}
</script>
@endsection
