<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
@page {
    size: A4;
    margin: 15mm 20mm 20mm 20mm;
}
* { box-sizing: border-box; }
body {
    font-family: "Times New Roman", Times, serif;
    font-size: 12pt;
    color: #111;
    line-height: 1.5;
    margin: 0;
    padding: 0;
}

/* ── Kop Surat ── */
/* Kop spans the full A4 width, cancelling the @page top/side margins */
.kop-wrap {
    margin: -15mm -20mm 6mm -20mm;
    width: 210mm;
}
.kop-wrap img {
    display: block;
    width: 210mm;
}

/* ── Meta Surat ── */
.meta-table {
    border-collapse: collapse;
    margin-bottom: 14px;
    font-size: 11pt;
}
.meta-table td {
    padding: 2px 8px 2px 0;
    vertical-align: top;
}
.meta-table td:first-child {
    width: 100px;
    white-space: nowrap;
}

/* ── Penerima ── */
.penerima {
    font-size: 11pt;
    margin-bottom: 14px;
    line-height: 1.6;
}

/* ── Tanggal kanan ── */
.kota-tgl {
    text-align: right;
    margin-bottom: 16px;
    font-size: 11pt;
}

/* ── Hormat ── */
.salam-pembuka {
    font-size: 11pt;
    margin-bottom: 12px;
}

/* ── Body surat ── */
.surat-body {
    font-size: 11pt;
    line-height: 1.8;
    text-align: justify;
    margin-bottom: 24px;
    white-space: pre-wrap;
}

/* ── TTD ── */
.ttd-wrap {
    font-size: 11pt;
    margin-top: 10px;
}
.ttd-title {
    margin-bottom: 2px;
}
.ttd-company {
    font-weight: bold;
    margin-bottom: 50px;
}
.ttd-name {
    display: inline-block;
    font-weight: bold;
    border-top: 1px solid #333;
    padding-top: 4px;
    min-width: 180px;
}
.ttd-jabatan {
    margin-top: 2px;
}
</style>
</head>
<body>

{{-- ─── Kop Surat ─────────────────────────────────────────────────────────── --}}
<div class="kop-wrap">
    <img src="{{ public_path('images/kop-surat.png') }}" alt="Kop Surat PT. Duri Rejang Berseri">
</div>

{{-- ─── Meta Surat ─────────────────────────────────────────────────────────── --}}
<table class="meta-table">
    <tr>
        <td>Nomor</td>
        <td>: {{ $nomor_surat ?? '—' }}</td>
    </tr>
    <tr>
        <td>Lampiran</td>
        <td>: {{ $lampiran ?? '-' }}</td>
    </tr>
    <tr>
        <td>Perihal</td>
        <td>: {{ $perihal ?? '—' }}</td>
    </tr>
</table>

{{-- ─── Penerima ───────────────────────────────────────────────────────────── --}}
<div class="penerima">
    Kepada Yth.<br>
    <strong>{{ $kepada ?? '—' }}</strong><br>
    {{ $instansi_tujuan ?? '—' }}<br>
    {{ $alamat_tujuan ?? 'di Tempat' }}
</div>

{{-- ─── Tanggal ────────────────────────────────────────────────────────────── --}}
<div class="kota-tgl">
    Jakarta, {{ $tanggal_surat ?? now()->locale('id')->isoFormat('D MMMM Y') }}
</div>

{{-- ─── Salam Pembuka ──────────────────────────────────────────────────────── --}}
<div class="salam-pembuka">Dengan hormat,</div>

{{-- ─── Isi Surat ──────────────────────────────────────────────────────────── --}}
<div class="surat-body">{!! nl2br(e($isi_surat ?? '')) !!}</div>

{{-- ─── TTD ────────────────────────────────────────────────────────────────── --}}
<div class="ttd-wrap">
    <div class="ttd-title">Hormat kami,</div>
    <div class="ttd-company">PT. DURI REJANG BERSERI</div>
    <div>
        <div class="ttd-name">{{ $penandatangan_nama ?? '—' }}</div>
        <div class="ttd-jabatan">{{ $penandatangan_jabatan ?? '—' }}</div>
    </div>
</div>

</body>
</html>
