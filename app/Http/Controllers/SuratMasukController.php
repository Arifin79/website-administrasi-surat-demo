<?php

namespace App\Http\Controllers;

use App\Data\SuratMasukData;
use App\Models\ActivityLog;
use App\Models\IncomingLetter;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratMasukController extends Controller
{
    public function index(): View
    {
        $records = IncomingLetter::latest()->get()->map(fn($m) => SuratMasukData::fromModel($m));

        return view('surat-masuk.index', compact('records'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nomor_surat_asal' => ['required', 'string', 'max:100'],
            'tanggal_surat'    => ['required', 'date'],
            'tanggal_diterima' => ['required', 'date'],
            'pengirim'         => ['required', 'string', 'max:200'],
            'perihal'          => ['required', 'string'],
            'status'           => ['required', 'in:Diterima,Diproses,Selesai'],
            'file_lampiran'    => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $filePath = null;
        if ($request->hasFile('file_lampiran')) {
            $filePath = $request->file('file_lampiran')->store('documents', 'public');
        }

        $letter = IncomingLetter::create([
            'agenda_number'    => IncomingLetter::nextAgendaNumber(),
            'reference_number' => $request->nomor_surat_asal,
            'letter_date'      => $request->tanggal_surat,
            'received_date'    => $request->tanggal_diterima,
            'sender'           => $request->pengirim,
            'subject'          => $request->perihal,
            'status'           => $request->status,
            'file_path'        => $filePath,
            'user_id'          => session('auth_user.id'),
        ]);

        ActivityLog::record(session('auth_user.id'), 'create_surat_masuk', "Menambahkan surat masuk: {$letter->agenda_number}", $request->ip());

        return redirect()->route('surat-masuk.index')
            ->with('success', 'Surat masuk berhasil ditambahkan.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'nomor_surat_asal' => ['required', 'string', 'max:100'],
            'tanggal_surat'    => ['required', 'date'],
            'tanggal_diterima' => ['required', 'date'],
            'pengirim'         => ['required', 'string', 'max:200'],
            'perihal'          => ['required', 'string'],
            'status'           => ['required', 'in:Diterima,Diproses,Selesai'],
            'file_lampiran'    => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $letter = IncomingLetter::findOrFail($id);

        $filePath = $letter->file_path;
        if ($request->hasFile('file_lampiran')) {
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }
            $filePath = $request->file('file_lampiran')->store('documents', 'public');
        }

        $letter->update([
            'reference_number' => $request->nomor_surat_asal,
            'letter_date'      => $request->tanggal_surat,
            'received_date'    => $request->tanggal_diterima,
            'sender'           => $request->pengirim,
            'subject'          => $request->perihal,
            'status'           => $request->status,
            'file_path'        => $filePath,
        ]);

        ActivityLog::record(session('auth_user.id'), 'update_surat_masuk', "Memperbarui surat masuk: {$letter->agenda_number}", $request->ip());

        return redirect()->route('surat-masuk.index')
            ->with('success', 'Data surat masuk berhasil diperbarui.');
    }

    public function berkas(int $id): StreamedResponse|RedirectResponse
    {
        $letter = IncomingLetter::findOrFail($id);

        if (! $letter->file_path || ! Storage::disk('public')->exists($letter->file_path)) {
            return redirect()->route('surat-masuk.index')
                ->with('error', 'Berkas lampiran tidak ditemukan.');
        }

        return Storage::disk('public')->response($letter->file_path);
    }

    public function destroy(int $id): RedirectResponse
    {
        $letter = IncomingLetter::findOrFail($id);

        if ($letter->file_path) {
            Storage::disk('public')->delete($letter->file_path);
        }

        $agendaNumber = $letter->agenda_number;
        $letter->delete();

        ActivityLog::record(session('auth_user.id'), 'delete_surat_masuk', "Menghapus surat masuk: {$agendaNumber}");

        return redirect()->route('surat-masuk.index')
            ->with('success', 'Surat masuk berhasil dihapus.');
    }
}
