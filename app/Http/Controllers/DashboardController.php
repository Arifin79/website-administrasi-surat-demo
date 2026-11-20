<?php

namespace App\Http\Controllers;

use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalMasuk  = IncomingLetter::count();
        $totalKeluar = OutgoingLetter::count();

        $stats = [
            'total_masuk'    => $totalMasuk,
            'total_keluar'   => $totalKeluar,
            'total_arsip'    => $totalMasuk + $totalKeluar,
            'surat_diproses' => IncomingLetter::where('status', 'Diproses')->count(),
        ];

        $monthlyStats = $this->buildMonthlyStats();
        $activity     = $this->buildActivity();

        return view('dashboard.index', compact('stats', 'activity', 'monthlyStats'));
    }

    private function buildMonthlyStats(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $year  = (int) date('Y', strtotime("-{$i} months"));
            $month = (int) date('n', strtotime("-{$i} months"));
            $months[] = [
                'bulan'  => date('M Y', strtotime("-{$i} months")),
                'masuk'  => IncomingLetter::whereYear('received_date', $year)->whereMonth('received_date', $month)->count(),
                'keluar' => OutgoingLetter::whereYear('letter_date', $year)->whereMonth('letter_date', $month)->count(),
            ];
        }
        return $months;
    }

    private function buildActivity(): array
    {
        $masuk = IncomingLetter::latest()->take(3)->get()->map(fn($m) => [
            'jenis'           => 'masuk',
            'perihal'         => $m->subject,
            'pengirim'        => $m->sender,
            'tujuan'          => null,
            'nomor_surat_asal'=> $m->reference_number,
            'nomor_surat'     => null,
            'tanggal_surat'   => $m->received_date->format('Y-m-d'),
            'status'          => $m->status,
        ])->all();

        $keluar = OutgoingLetter::latest()->take(2)->get()->map(fn($m) => [
            'jenis'           => 'keluar',
            'perihal'         => $m->subject,
            'pengirim'        => null,
            'tujuan'          => $m->recipient,
            'nomor_surat_asal'=> null,
            'nomor_surat'     => $m->letter_number,
            'tanggal_surat'   => $m->letter_date->format('Y-m-d'),
            'status'          => $m->status,
        ])->all();

        $merged = array_merge($masuk, $keluar);
        usort($merged, fn($a, $b) => strcmp($b['tanggal_surat'], $a['tanggal_surat']));
        return array_slice($merged, 0, 5);
    }
}
