<?php

namespace App\Http\Controllers;

use App\Data\SuratKeluarData;
use App\Data\TemplateData;
use App\Models\ActivityLog;
use App\Models\LetterTemplate;
use App\Models\OutgoingLetter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratKeluarController extends Controller
{
    public function index(): View
    {
        $records = OutgoingLetter::latest()->get()->map(fn($m) => SuratKeluarData::fromModel($m));

        return view('surat-keluar.index', compact('records'));
    }

    public function create(): View
    {
        $templates     = LetterTemplate::with('creator')->get()->map(fn($m) => TemplateData::fromModel($m));
        $nomorOtomatis = OutgoingLetter::nextLetterNumber();

        return view('surat-keluar.create', compact('templates', 'nomorOtomatis'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nomor_surat'      => ['required', 'string', 'max:100'],
            'tanggal_surat'    => ['required', 'date'],
            'tujuan'           => ['required', 'string', 'max:200'],
            'perihal'          => ['required', 'string'],
            'jenis_pembuatan'  => ['required', 'in:Manual (Upload Scan),Template Generator'],
            'status'           => ['required', 'in:Draft,Terkirim'],
            'file_dokumen'     => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        if (OutgoingLetter::existsForYear($request->nomor_surat, (int) date('Y', strtotime($request->tanggal_surat)))) {
            return back()->withInput()->withErrors(['nomor_surat' => 'Nomor surat sudah digunakan pada tahun yang sama.']);
        }

        $filePath = null;
        if ($request->hasFile('file_dokumen')) {
            $filePath = $request->file('file_dokumen')->store('documents', 'public');
        }

        $letter = OutgoingLetter::create([
            'letter_number'    => $request->nomor_surat,
            'letter_date'      => $request->tanggal_surat,
            'recipient'        => $request->tujuan,
            'subject'          => $request->perihal,
            'creation_type'    => $request->jenis_pembuatan,
            'status'           => $request->status,
            'file_path'        => $filePath,
            'user_id'          => session('auth_user.id'),
        ]);

        ActivityLog::record(session('auth_user.id'), 'create_surat_keluar', "Menambahkan surat keluar: {$letter->letter_number}", $request->ip());

        return redirect()->route('surat-keluar.index')
            ->with('success', 'Surat keluar berhasil disimpan.');
    }

    public function storeFromTemplate(Request $request): RedirectResponse
    {
        $request->validate([
            'nomor_surat'           => ['required', 'string', 'max:100'],
            'tanggal_surat'         => ['required', 'string'],
            'tujuan'                => ['required', 'string', 'max:200'],
            'perihal'               => ['required', 'string'],
            'kepada'                => ['required', 'string'],
            'instansi_tujuan'       => ['required', 'string'],
            'alamat_tujuan'         => ['required', 'string'],
            'isi_surat'             => ['required', 'string'],
            'penandatangan_nama'    => ['required', 'string'],
            'penandatangan_jabatan' => ['required', 'string'],
            'template_id'           => ['nullable', 'integer', 'exists:letter_templates,id'],
            'status'                => ['required', 'in:Draft,Terkirim'],
            'lampiran'              => ['nullable', 'string'],
        ]);

        if (OutgoingLetter::existsForYear($request->nomor_surat, (int) date('Y'))) {
            return back()->withInput()->withErrors(['nomor_surat' => 'Nomor surat sudah digunakan pada tahun yang sama.']);
        }

        $letterHtml = view('surat-keluar.partials.letter-preview', $request->all())->render();

        $pdf      = Pdf::loadHtml($letterHtml)->setPaper('a4')->setOption('margin_top', '15mm')->setOption('margin_right', '20mm')->setOption('margin_bottom', '20mm')->setOption('margin_left', '20mm');
        $filename = 'documents/' . Str::slug($request->nomor_surat) . '-' . time() . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        $letter = OutgoingLetter::create([
            'letter_number'    => $request->nomor_surat,
            'letter_date'      => date('Y-m-d'),
            'recipient'        => $request->tujuan,
            'subject'          => $request->perihal,
            'creation_type'    => 'Template Generator',
            'template_id'      => $request->template_id ?: null,
            'letter_body_html' => $letterHtml,
            'status'           => $request->status,
            'file_path'        => $filename,
            'user_id'          => session('auth_user.id'),
        ]);

        ActivityLog::record(session('auth_user.id'), 'create_surat_keluar_template', "Membuat surat keluar via template: {$letter->letter_number}", $request->ip());

        return redirect()->route('surat-keluar.index')
            ->with('success', 'Surat keluar berhasil dibuat dan disimpan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'nomor_surat'   => ['required', 'string', 'max:100'],
            'tanggal_surat' => ['required', 'date'],
            'tujuan'        => ['required', 'string', 'max:200'],
            'perihal'       => ['required', 'string'],
            'status'        => ['required', 'in:Draft,Terkirim'],
        ]);

        $letter = OutgoingLetter::findOrFail($id);

        if (OutgoingLetter::existsForYear($request->nomor_surat, (int) date('Y', strtotime($request->tanggal_surat)), $id)) {
            return back()->withInput()->withErrors(['nomor_surat' => 'Nomor surat sudah digunakan pada tahun yang sama.']);
        }

        $letter->update([
            'letter_number' => $request->nomor_surat,
            'letter_date'   => $request->tanggal_surat,
            'recipient'     => $request->tujuan,
            'subject'       => $request->perihal,
            'status'        => $request->status,
        ]);

        ActivityLog::record(session('auth_user.id'), 'update_surat_keluar', "Memperbarui surat keluar: {$letter->letter_number}", $request->ip());

        return redirect()->route('surat-keluar.index')
            ->with('success', 'Data surat keluar berhasil diperbarui.');
    }

    public function dokumen(int $id): StreamedResponse|RedirectResponse
    {
        $letter = OutgoingLetter::findOrFail($id);

        if (! $letter->file_path || ! Storage::disk('public')->exists($letter->file_path)) {
            return redirect()->route('surat-keluar.index')
                ->with('error', 'Dokumen surat tidak ditemukan.');
        }

        return Storage::disk('public')->response($letter->file_path);
    }

    public function destroy(int $id): RedirectResponse
    {
        $letter = OutgoingLetter::findOrFail($id);

        if ($letter->file_path) {
            Storage::disk('public')->delete($letter->file_path);
        }

        $letterNumber = $letter->letter_number;
        $letter->delete();

        ActivityLog::record(session('auth_user.id'), 'delete_surat_keluar', "Menghapus surat keluar: {$letterNumber}");

        return redirect()->route('surat-keluar.index')
            ->with('success', 'Surat keluar berhasil dihapus.');
    }
}
