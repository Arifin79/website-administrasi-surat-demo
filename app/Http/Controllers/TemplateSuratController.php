<?php

namespace App\Http\Controllers;

use App\Data\TemplateData;
use App\Models\ActivityLog;
use App\Models\LetterTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TemplateSuratController extends Controller
{
    public function index(): View
    {
        $templates = LetterTemplate::with('creator')->latest()->get()->map(fn($m) => TemplateData::fromModel($m));

        return view('template.index', compact('templates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'template_name'   => ['required', 'string', 'max:150'],
            'template_code'   => ['required', 'string', 'max:50', 'unique:letter_templates,template_code'],
            'default_subject' => ['nullable', 'string', 'max:255'],
            'body_template'   => ['required', 'string'],
        ]);

        $template = LetterTemplate::create([
            'template_name'   => $request->template_name,
            'template_code'   => strtoupper($request->template_code),
            'default_subject' => $request->default_subject,
            'body_template'   => $request->body_template,
            'created_by'      => session('auth_user.id'),
        ]);

        ActivityLog::record(session('auth_user.id'), 'create_template', "Menambahkan template: {$template->template_code}", $request->ip());

        return redirect()->route('template.index')
            ->with('success', 'Template surat berhasil ditambahkan.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $template = LetterTemplate::findOrFail($id);
        $code     = $template->template_code;
        $template->delete();

        ActivityLog::record(session('auth_user.id'), 'delete_template', "Menghapus template: {$code}");

        return redirect()->route('template.index')
            ->with('success', 'Template surat berhasil dihapus.');
    }
}
