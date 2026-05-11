<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TemplateProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TemplateProposalController extends Controller
{
    // ── INDEX: tampilkan daftar template ──────────
    public function index()
    {
        $templates = TemplateProposal::latest()->paginate(10);

        $stats = [
            'total'            => TemplateProposal::count(),
            'aktif'            => TemplateProposal::active()->count(),
            'terakhir_update'  => TemplateProposal::latest()->value('updated_at'),
        ];

        return view('admin.templateproposal.index', compact('templates', 'stats'));
    }

    // ── STORE: simpan template baru ───────────────
    public function store(Request $request)
    {
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'versi'        => 'required|string|max:20',
            'kategori'     => 'required|in:Biomedis,Sosial,Umum',
            'file'         => 'required|file|mimes:pdf,docx,doc|max:10240', // max 10MB
        ], [
            'nama_dokumen.required' => 'Nama dokumen wajib diisi.',
            'versi.required'        => 'Versi wajib diisi.',
            'kategori.required'     => 'Kategori wajib dipilih.',
            'file.required'         => 'File wajib diunggah.',
            'file.mimes'            => 'Format file harus PDF atau DOCX.',
            'file.max'              => 'Ukuran file maksimal 10MB.',
        ]);

        $file      = $request->file('file');
        $fileName  = $file->getClientOriginalName();
        $fileType  = strtolower($file->getClientOriginalExtension());
        $fileSize  = $file->getSize();

        // Simpan ke storage/app/public/templates/
        $filePath  = $file->storeAs(
            'templates',
            Str::slug($request->nama_dokumen) . '_' . time() . '.' . $fileType,
            'public'
        );

        TemplateProposal::create([
            'nama_dokumen' => $request->nama_dokumen,
            'versi'        => $request->versi,
            'kategori'     => $request->kategori,
            'file_path'    => $filePath,
            'file_name'    => $fileName,
            'file_type'    => $fileType,
            'file_size'    => $fileSize,
            'is_active'    => true,
            'created_by'   => auth()->id(),
        ]);

        return redirect()
            ->route('admin.templateproposal.index')
            ->with('success', 'Template "' . $request->nama_dokumen . '" berhasil dibuat dan tersimpan.');
    }

    // ── UPDATE: edit nama/versi/kategori template ─
    public function update(Request $request, TemplateProposal $template)
    {
        $request->validate([
            'nama_dokumen' => 'required|string|max:255',
            'versi'        => 'required|string|max:20',
            'kategori'     => 'required|in:Biomedis,Sosial,Umum',
            'file'         => 'nullable|file|mimes:pdf,docx,doc|max:10240',
        ]);

        $data = [
            'nama_dokumen' => $request->nama_dokumen,
            'versi'        => $request->versi,
            'kategori'     => $request->kategori,
        ];

        // Ganti file jika ada upload baru
        if ($request->hasFile('file')) {
            // Hapus file lama
            Storage::disk('public')->delete($template->file_path);

            $file           = $request->file('file');
            $fileType       = strtolower($file->getClientOriginalExtension());
            $data['file_path'] = $file->storeAs(
                'templates',
                Str::slug($request->nama_dokumen) . '_' . time() . '.' . $fileType,
                'public'
            );
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $fileType;
            $data['file_size'] = $file->getSize();
        }

        $template->update($data);

        return redirect()
            ->route('admin.templateproposal.index')
            ->with('success', 'Template "' . $template->nama_dokumen . '" berhasil diperbarui.');
    }

    // ── TOGGLE AKTIF / NONAKTIF ───────────────────
    public function toggleActive(TemplateProposal $template)
    {
        $template->update(['is_active' => !$template->is_active]);

        $status = $template->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()
            ->route('admin.templateproposal.index')
            ->with('success', 'Template "' . $template->nama_dokumen . '" berhasil ' . $status . '.');
    }

    // ── DESTROY: hapus template ───────────────────
    public function destroy(TemplateProposal $template)
    {
        // Hapus file dari storage
        Storage::disk('public')->delete($template->file_path);

        $nama = $template->nama_dokumen;
        $template->delete();

        return redirect()
            ->route('admin.templateproposal.index')
            ->with('success', 'Template "' . $nama . '" berhasil dihapus.');
    }

    // ── DOWNLOAD ──────────────────────────────────
    public function download(TemplateProposal $template)
    {
        if (!Storage::disk('public')->exists($template->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $template->file_path,
            $template->file_name
        );
    }
}