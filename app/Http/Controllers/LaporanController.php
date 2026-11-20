<?php

namespace App\Http\Controllers;

use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $fromDate = $request->query('from_date');
        $toDate   = $request->query('to_date');
        $jenis    = $request->query('jenis', 'semua');

        $masukQuery  = IncomingLetter::query();
        $keluarQuery = OutgoingLetter::query();

        if ($fromDate) {
            $masukQuery->whereDate('received_date', '>=', $fromDate);
            $keluarQuery->whereDate('letter_date', '>=', $fromDate);
        }
        if ($toDate) {
            $masukQuery->whereDate('received_date', '<=', $toDate);
            $keluarQuery->whereDate('letter_date', '<=', $toDate);
        }

        $masukRows  = $jenis !== 'keluar' ? $masukQuery->latest()->get() : collect();
        $keluarRows = $jenis !== 'masuk'  ? $keluarQuery->latest()->get() : collect();

        $masuk = $masukRows->map(fn($m) => [
            'jenis'           => 'masuk',
            'nomor_surat_asal'=> $m->reference_number,
            'nomor_surat'     => null,
            'pengirim'        => $m->sender,
            'tujuan'          => null,
            'perihal'         => $m->subject,
            'tanggal_surat'   => $m->received_date->format('Y-m-d'),
            'status'          => $m->status,
        ])->all();

        $keluar = $keluarRows->map(fn($m) => [
            'jenis'           => 'keluar',
            'nomor_surat_asal'=> null,
            'nomor_surat'     => $m->letter_number,
            'pengirim'        => null,
            'tujuan'          => $m->recipient,
            'perihal'         => $m->subject,
            'tanggal_surat'   => $m->letter_date->format('Y-m-d'),
            'status'          => $m->status,
        ])->all();

        $all = array_merge($masuk, $keluar);
        usort($all, fn($a, $b) => strcmp($b['tanggal_surat'], $a['tanggal_surat']));

        $stats = [
            'total_masuk'  => count($masuk),
            'total_keluar' => count($keluar),
            'total'        => count($all),
        ];

        return view('laporan.index', compact('all', 'stats', 'fromDate', 'toDate', 'jenis'));
    }
}
