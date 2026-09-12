<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SerahTerima;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;

class SerahTerimaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = SerahTerima::with(['proyek', 'kontraktor'])->latest();

        if ($user->isKontraktor()) {
            $query->where('kontraktor_id', $user->id);
            $proyeks = Proyek::where('kontraktor_id', $user->id)->get();
        } elseif ($user->isPPTK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('pptk_id', $user->id);
            });
            $proyeks = collect();
        } elseif ($user->isPPK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('ppk_id', $user->id);
            });
            $proyeks = collect();
        } elseif ($user->isKonsultan()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('konsultan_id', $user->id);
            });
            $proyeks = collect();
        } else {
            $proyeks = collect();
        }

        $serahTerimas = $query->get();

        return view('serah_terima.index', compact('serahTerimas', 'proyeks'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isKontraktor()) abort(403);

        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'jenis' => 'required|in:PHO,FHO',
            'keterangan' => 'nullable|string',
            'surat_permohonan' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'dokumen_lampiran' => 'nullable|array',
            'dokumen_lampiran.*' => 'file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:5120',
            'lampiran_tambahan' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:5120',
        ], [
            'surat_permohonan.required' => 'Surat permohonan wajib diunggah.',
            'surat_permohonan.max' => 'Ukuran file surat permohonan maksimal 5MB.'
        ]);

        $suratPath = null;
        if ($request->hasFile('surat_permohonan')) {
            $suratPath = $request->file('surat_permohonan')->store('serah_terima_docs', 'public');
        }

        $lampiranPaths = [];
        if ($request->hasFile('dokumen_lampiran')) {
            foreach ($request->file('dokumen_lampiran') as $file) {
                $lampiranPaths[] = $file->store('serah_terima_docs', 'public');
            }
        }

        $lampiranTambahanPath = null;
        if ($request->hasFile('lampiran_tambahan')) {
            $lampiranTambahanPath = $request->file('lampiran_tambahan')->store('serah_terima_docs', 'public');
        }

        SerahTerima::create([
            'proyek_id' => $request->proyek_id,
            'kontraktor_id' => Auth::id(),
            'jenis' => $request->jenis,
            'tanggal_pengajuan' => now(),
            'keterangan' => $request->keterangan,
            'surat_permohonan' => $suratPath,
            'dokumen_lampiran' => empty($lampiranPaths) ? null : $lampiranPaths,
            'lampiran_tambahan' => $lampiranTambahanPath,
            'status' => 'diajukan',
        ]);

        return redirect()->route('serah-terima.index')->with(['success' => 'Pengajuan Serah Terima ('.$request->jenis.') berhasil diajukan.', 'currentMenu' => 'serah_terima']);
    }

    public function approve(Request $request, SerahTerima $serahTerima)
    {
        if (!Auth::user()->isPPTK()) abort(403);

        $request->validate([
            'surat_persetujuan_ppk' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'catatan_pptk' => 'nullable|string',
        ], [
            'surat_persetujuan_ppk.required' => 'Surat Persetujuan PHO/FHO wajib diunggah.',
        ]);

        $path = $request->file('surat_persetujuan_ppk')->store('serah_terima_docs', 'public');

        $serahTerima->update([
            'status_pptk' => 'disetujui',
            'status' => 'disetujui',
            'surat_persetujuan_ppk' => $path,
            'catatan_pptk' => $request->catatan_pptk,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('serah-terima.index')->with('success', 'Surat Persetujuan PHO/FHO berhasil diunggah dan pengajuan disetujui penuh.');
    }

    public function reject(Request $request, SerahTerima $serahTerima)
    {
        if (!Auth::user()->isPPTK()) abort(403);

        $request->validate([
            'catatan_pptk' => 'required|string',
        ]);

        $serahTerima->update([
            'status_pptk' => 'ditolak',
            'status' => 'ditolak',
            'catatan_pptk' => $request->catatan_pptk,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('serah-terima.index')->with('error', 'Pengajuan Serah Terima ditolak oleh PPTK.');
    }



    public function approveKonsultan(Request $request, SerahTerima $serahTerima)
    {
        if (!Auth::user()->isKonsultan()) abort(403);

        $request->validate([
            'surat_persetujuan_konsultan' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'catatan_konsultan' => 'nullable|string',
        ], [
            'surat_persetujuan_konsultan.required' => 'Surat Persetujuan Konsultan wajib diunggah.',
        ]);

        $path = $request->file('surat_persetujuan_konsultan')->store('serah_terima_docs', 'public');

        $serahTerima->update([
            'status_konsultan' => 'disetujui',
            'surat_persetujuan_konsultan' => $path,
            'catatan_konsultan' => $request->catatan_konsultan,
        ]);

        return redirect()->route('serah-terima.index')->with('success', 'Surat Persetujuan Serah Terima berhasil diunggah.');
    }

    public function rejectKonsultan(Request $request, SerahTerima $serahTerima)
    {
        if (!Auth::user()->isKonsultan()) abort(403);

        $request->validate([
            'catatan_konsultan' => 'required|string',
        ]);

        $serahTerima->update([
            'status_konsultan' => 'ditolak',
            'status' => 'ditolak', // Langsung tolak keseluruhan pengajuan
            'catatan_konsultan' => $request->catatan_konsultan,
        ]);

        return redirect()->route('serah-terima.index')->with('error', 'Pengajuan Serah Terima ditolak oleh Konsultan.');
    }
}
