@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan')

@section('content')

<div class="page-header">
    <h1>Laporan Administrasi Surat</h1>
    <p>Rekapitulasi data surat berdasarkan periode untuk pelaporan administrasi.</p>
</div>

{{-- ─── Filter ──────────────────────────────────────────────────────────────── --}}
<section class="panel no-print">
    <div class="panel-head">
        <h3>Filter Periode Laporan</h3>
    </div>
    <div class="panel-body">
        <form method="GET" action="{{ route('laporan.index') }}">
            <div class="toolbar" style="gap:12px;flex-wrap:wrap">
                <div style="display:flex;align-items:center;gap:8px">
                    <label style="font-size:12px;color:var(--muted);white-space:nowrap">Dari</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}" style="padding:9px 12px;font-size:12px;border:1px solid #dce4ef;border-radius:10px;outline:none">
                </div>
                <div style="display:flex;align-items:center;gap:8px">
                    <label style="font-size:12px;color:var(--muted);white-space:nowrap">Sampai</label>
                    <input type="date" name="to_date" value="{{ $toDate }}" style="padding:9px 12px;font-size:12px;border:1px solid #dce4ef;border-radius:10px;outline:none">
                </div>
                <select name="jenis" style="padding:9px 12px;font-size:12px;border:1px solid #dce4ef;border-radius:10px;outline:none;background:#fff">
                    <option value="semua" {{ $jenis === 'semua' ? 'selected' : '' }}>Semua Jenis</option>
                    <option value="masuk"  {{ $jenis === 'masuk'  ? 'selected' : '' }}>Surat Masuk</option>
                    <option value="keluar" {{ $jenis === 'keluar' ? 'selected' : '' }}>Surat Keluar</option>
                </select>
                <button type="submit" class="btn btn-primary">Tampilkan</button>
                <a href="{{ route('laporan.index') }}" class="btn btn-outline">Reset</a>
            </div>
        </form>
    </div>
</section>

{{-- ─── Rekap Cards ─────────────────────────────────────────────────────────── --}}
<div class="report-cards">
    <div class="report-card">
        <small>Jumlah Surat Masuk</small>
        <h2>{{ $stats['total_masuk'] }}</h2>
    </div>
    <div class="report-card">
        <small>Jumlah Surat Keluar</small>
        <h2>{{ $stats['total_keluar'] }}</h2>
    </div>
    <div class="report-card">
        <small>Total Dokumen</small>
        <h2>{{ $stats['total'] }}</h2>
    </div>
</div>

{{-- ─── Tabel Hasil ─────────────────────────────────────────────────────────── --}}
<section class="panel">
    <div class="panel-head no-print">
        <h3>Rincian Data</h3>
        <div class="toolbar">
            <button onclick="window.print()" class="btn btn-outline">
                <span class="material-icons" style="font-size:16px">print</span> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    {{-- Print header (only shown when printing) --}}
    <div class="print-header" style="padding:18px 22px 0">
        <h2 style="font-size:15px;margin:0 0 4px;font-weight:700">Laporan Rekapitulasi Surat</h2>
        <p style="font-size:11px;color:#555;margin:0">
            PT Duri Rejang Berseri
            @if($fromDate || $toDate)
                &nbsp;·&nbsp; Periode:
                {{ $fromDate ? date('d M Y', strtotime($fromDate)) : '—' }}
                s/d
                {{ $toDate ? date('d M Y', strtotime($toDate)) : '—' }}
            @endif
            &nbsp;·&nbsp; Dicetak: {{ date('d M Y') }}
        </p>
        <hr style="margin:12px 0 0;border-color:#999">
    </div>

    <div class="table-wrap">
        <table id="tableLaporan">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Jenis</th>
                    <th>Nomor Surat</th>
                    <th>Tanggal</th>
                    <th>Pengirim / Tujuan</th>
                    <th>Perihal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($all as $i => $item)
                    @php
                        $nomor  = $item['jenis'] === 'masuk' ? $item['nomor_surat_asal'] : $item['nomor_surat'];
                        $pihak  = $item['jenis'] === 'masuk' ? $item['pengirim'] : $item['tujuan'];
                        $tgl    = \Carbon\Carbon::parse($item['tanggal_surat'])->locale('id')->isoFormat('D MMM Y');
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <span class="badge {{ $item['jenis'] === 'masuk' ? 'badge-blue' : 'badge-green' }}">
                                <span class="material-icons" style="font-size:11px;vertical-align:middle">{{ $item['jenis'] === 'masuk' ? 'south_west' : 'north_east' }}</span>
                                {{ $item['jenis'] === 'masuk' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                        <td><b>{{ $nomor }}</b></td>
                        <td>{{ $tgl }}</td>
                        <td>{{ Str::limit($pihak, 35) }}</td>
                        <td>{{ Str::limit($item['perihal'], 48) }}</td>
                        <td><x-status-badge :status="$item['status']" /></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">Tidak ada data sesuai filter yang dipilih.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Print signature footer --}}
    <div class="print-footer" style="padding:0 22px 20px">
        <div style="text-align:right">
            <div>Jakarta, {{ date('d F Y') }}</div>
            <div style="margin-top:4px">Kepala Administrasi,</div>
            <div style="margin-top:55px;border-top:1px solid #333;padding-top:4px;width:180px;text-align:center">
                ( _____________________ )
            </div>
        </div>
    </div>
</section>

@endsection
