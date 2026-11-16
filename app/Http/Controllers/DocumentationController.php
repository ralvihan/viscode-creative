<?php

namespace App\Http\Controllers;

use App\Models\Documentation;
use App\Models\Project;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentationController extends Controller
{
    public function index()
    {
        abort_if(! auth()->user()->canAccessModule('documentation'), 403, 'Anda tidak memiliki hak akses modul Dokumentasi.');

        $documentations = Documentation::with(['project', 'uploader'])
            ->latest()
            ->paginate(15);

        $projects = Project::orderBy('project_code', 'desc')->get();

        return view('documentations.index', compact('documentations', 'projects'));
    }

    public function store(Request $request)
    {
        abort_if(! auth()->user()->canAccessModule('documentation'), 403, 'Anda tidak memiliki hak akses.');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:project_deliverable,contract,sop,asset,presentation,other',
            'project_id' => 'nullable|exists:projects,id',
            'file_url' => 'nullable|url',
            'file' => 'nullable|file|max:10240', // 10MB
            'description' => 'nullable|string',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('documentations', 'public');
        }

        $doc = Documentation::create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'project_id' => $validated['project_id'] ?? null,
            'file_url' => $validated['file_url'] ?? null,
            'file_path' => $filePath,
            'description' => $validated['description'] ?? null,
            'uploaded_by' => Auth::id(),
        ]);

        ActivityLogger::log('Documentation', (string) $doc->id, 'CREATE', null, $doc->toArray(), 'Dokumen/Aset baru diunggah');

        return back()->with('success', 'Dokumen/aset "'.$doc->title.'" berhasil disimpan!');
    }

    public function destroy(Documentation $documentation)
    {
        abort_if(! auth()->user()->canAccessModule('documentation'), 403, 'Anda tidak memiliki hak akses.');

        $old = $documentation->toArray();
        if ($documentation->file_path && Storage::disk('public')->exists($documentation->file_path)) {
            Storage::disk('public')->delete($documentation->file_path);
        }

        $documentation->delete();
        ActivityLogger::log('Documentation', (string) $old['id'], 'DELETE', $old, null, 'Hapus dokumen/aset');

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }
}
