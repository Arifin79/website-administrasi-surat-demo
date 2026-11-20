<?php

namespace App\Http\Controllers;

use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use Illuminate\View\View;

class ArsipController extends Controller
{
    public function index(): View
    {
        $masuk = IncomingLetter::latest()->get()->map(fn($m) => [
            'id'              => $m->id,
            'jenis'           => 'masuk',
            'nomor_agenda'    => $m->agenda_number,
            'nomor_surat_asal'=> $m->reference_number,
            'nomor_surat'     => null,
            'pengirim'        => $m->sender,
            'tujuan'          => null,
            'perihal'         => $m->subject,
            'tanggal_surat'   => $m->received_date->format('Y-m-d'),
            'status'          => $m->status,
            'file_path'       => $m->file_path,
        ])->all();

        $keluar = OutgoingLetter::latest()->get()->map(fn($m) => [
            'id'              => $m->id,
            'jenis'           => 'keluar',
            'nomor_agenda'    => null,
            'nomor_surat_asal'=> null,
            'nomor_surat'     => $m->letter_number,
            'pengirim'        => null,
            'tujuan'          => $m->recipient,
            'perihal'         => $m->subject,
            'tanggal_surat'   => $m->letter_date->format('Y-m-d'),
            'status'          => $m->status,
            'file_path'       => $m->file_path,
        ])->all();

        $arsip = array_merge($masuk, $keluar);
        usort($arsip, fn($a, $b) => strcmp($b['tanggal_surat'], $a['tanggal_surat']));

        // Kumpulkan daftar tahun yang tersedia dari data
        $tahunList = collect($arsip)
            ->pluck('tanggal_surat')
            ->map(fn($d) => substr($d, 0, 4))
            ->unique()->sort()->values()->all();

        return view('arsip.index', compact('arsip', 'tahunList'));
    }
}
