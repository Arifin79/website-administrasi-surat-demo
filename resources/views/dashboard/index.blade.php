@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php $authUser = session('auth_user'); @endphp

{{-- ─── Welcome ─────────────────────────────────────────────────────────────── --}}
<div class="welcome">
    <div>
        <h1>Selamat datang, {{ $authUser['name'] }} 👋</h1>
        <p>Berikut ringkasan aktivitas administrasi surat perusahaan.</p>
    </div>
    <div class="date-chip">
        ▣ &nbsp; {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
    </div>
</div>

{{-- ─── KPI Cards ───────────────────────────────────────────────────────────── --}}
<div class="stats">
    <x-stat-card
        label="Total Surat Masuk"
        :value="$stats['total_masuk']"
        hint="Seluruh surat yang diterima"
        icon="move_to_inbox"
        icon-bg="#eaf1ff"
        icon-color="#2865d9"
    />
    <x-stat-card
        label="Total Surat Keluar"
        :value="$stats['total_keluar']"
        hint="Seluruh surat yang diterbitkan"
        icon="outbox"
        icon-bg="#e7f8f1"
        icon-color="#15966a"
    />
    <x-stat-card
        label="Total Arsip"
        :value="$stats['total_arsip']"
        hint="Dokumen digital tersimpan"
        icon="folder_open"
        icon-bg="#fff4e2"
        icon-color="#c28220"
    />
    <x-stat-card
        label="Surat Diproses"
        :value="$stats['surat_diproses']"
        hint="Memerlukan tindak lanjut"
        icon="pending"
        icon-bg="#f1eaff"
        icon-color="#8055c7"
    />
</div>

{{-- ─── Chart + Activity ────────────────────────────────────────────────────── --}}
<div class="dashboard-grid">

    {{-- Bar Chart --}}
    <section class="panel">
        <div class="panel-head">
            <h3>Statistik Surat</h3>
            <span class="muted" style="font-size:11px">Ringkasan 6 bulan terakhir</span>
        </div>
        <div class="panel-body">
            <div class="chart-wrap">
                <div class="chart-bars">
                    @php $maxVal = max(array_map(fn($m) => max($m['masuk'], $m['keluar']), $monthlyStats)) ?: 1; @endphp
                    @foreach($monthlyStats as $m)
                        <div class="bar-group">
                            <div class="bar-pair">
                                <div class="bar" style="height:{{ max(4, ($m['masuk'] / $maxVal) * 170) }}px">
                                    <span class="bar-value">{{ $m['masuk'] }}</span>
                                </div>
                                <div class="bar alt" style="height:{{ max(4, ($m['keluar'] / $maxVal) * 170) }}px">
                                    <span class="bar-value">{{ $m['keluar'] }}</span>
                                </div>
                            </div>
                            <span class="bar-label">{{ $m['bulan'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="legend">
                    <span><i class="dot"></i> Surat Masuk</span>
                    <span><i class="dot green"></i> Surat Keluar</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Recent Activity --}}
    <section class="panel">
        <div class="panel-head">
            <h3>Aktivitas Terbaru</h3>
            <span class="muted" style="font-size:11px">5 data terkini</span>
        </div>
        <div class="panel-body activity">
            @forelse($activity as $item)
                <div class="activity-item">
                    <div class="activity-icon" style="{{ $item['jenis'] === 'masuk' ? 'background:#edf3ff;color:#2463eb' : 'background:#e7f8f1;color:#15966a' }}">
                        <span class="material-icons">{{ $item['jenis'] === 'masuk' ? 'move_to_inbox' : 'outbox' }}</span>
                    </div>
                    <div>
                        <b>{{ Str::limit($item['perihal'], 45) }}</b>
                        <small>
                            {{ $item['jenis'] === 'masuk' ? 'Dari: ' . $item['pengirim'] : 'Ke: ' . $item['tujuan'] }}
                            &middot; {{ \Carbon\Carbon::parse($item['tanggal_surat'])->locale('id')->isoFormat('D MMM Y') }}
                        </small>
                    </div>
                </div>
            @empty
                <div class="empty">Belum ada aktivitas.</div>
            @endforelse
        </div>
    </section>

</div>

{{-- ─── Recent Letters Table ────────────────────────────────────────────────── --}}
<section class="panel">
    <div class="panel-head">
        <h3>Surat Terbaru</h3>
        <a href="{{ route('arsip.index') }}" class="btn btn-light" style="font-size:12px">
            Lihat semua
        </a>
    </div>
    <div class="table-wrap">
        <table>
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
                @forelse($activity as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <span class="badge {{ $item['jenis'] === 'masuk' ? 'badge-blue' : 'badge-green' }}">
                                <span class="material-icons" style="font-size:11px;vertical-align:middle">{{ $item['jenis'] === 'masuk' ? 'south_west' : 'north_east' }}</span>
                                {{ $item['jenis'] === 'masuk' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </td>
                        <td><b>{{ $item['jenis'] === 'masuk' ? $item['nomor_surat_asal'] : $item['nomor_surat'] }}</b></td>
                        <td>{{ \Carbon\Carbon::parse($item['tanggal_surat'])->locale('id')->isoFormat('D MMM Y') }}</td>
                        <td>{{ $item['jenis'] === 'masuk' ? $item['pengirim'] : $item['tujuan'] }}</td>
                        <td>{{ Str::limit($item['perihal'], 50) }}</td>
                        <td><x-status-badge :status="$item['status']" /></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@endsection
