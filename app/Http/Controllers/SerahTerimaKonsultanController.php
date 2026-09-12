<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SerahTerimaKonsultan;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;

class SerahTerimaKonsultanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = SerahTerimaKonsultan::with(['proyek', 'konsultan'])->latest();

        if ($user->isKonsultan()) {
            $query->where('konsultan_id', $user->id);
            $proyeks = Proyek::where('konsultan_id', $user->id)->get();
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
        } else {
            $proyeks = collect();
        }

        $serahTerimas = $query->get();

        return view('serah_terima_konsultan.index', compact('serahTerimas', 'proyeks'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isKonsultan()) abort(403);

        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'jenis' => 'required|in:Pengawasan,Lainnya',
            'keterangan' => 'nullable|string',
            'surat_permohonan' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'dokumen_lampiran' => 'nullable|array',
            'dokumen_lampiran.*' => 'file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:5120',
        ]);

        $suratPath = null;
        if ($request->hasFile('surat_permohonan')) {
            $suratPath = $request->file('surat_permohonan')->store('serah_terima_konsultan_docs', 'public');
        }

        $lampiranPaths = [];
        if ($request->hasFile('dokumen_lampiran')) {
            foreach ($request->file('dokumen_lampiran') as $file) {
                $lampiranPaths[] = $file->store('serah_terima_konsultan_docs', 'public');
            }
        }

        SerahTerimaKonsultan::create([
            'proyek_id' => $request->proyek_id,
            'konsultan_id' => Auth::id(),
            'jenis' => $request->jenis,
            'tanggal_pengajuan' => now(),
            'keterangan' => $request->keterangan,
            'surat_permohonan' => $suratPath,
            'dokumen_lampiran' => empty($lampiranPaths) ? null : $lampiranPaths,
            'status' => 'diajukan',
        ]);

        return redirect()->route('serah-terima-konsultan.index')->with('success', 'Pengajuan Serah Terima Konsultan berhasil diajukan.');
    }

    public function approve(Request $request, SerahTerimaKonsultan $serahTerimaKonsultan)
    {
        if (!Auth::user()->isPPTK()) abort(403);

        $serahTerimaKonsultan->update([
            'status' => 'disetujui',
            'catatan_ppk' => $request->catatan_ppk,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('serah-terima-konsultan.index')->with('success', 'Pengajuan Serah Terima Konsultan disetujui.');
    }

    public function reject(Request $request, SerahTerimaKonsultan $serahTerimaKonsultan)
    {
        if (!Auth::user()->isPPTK()) abort(403);

        $serahTerimaKonsultan->update([
            'status' => 'ditolak',
            'catatan_ppk' => $request->catatan_ppk,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('serah-terima-konsultan.index')->with('error', 'Pengajuan Serah Terima Konsultan ditolak.');
    }
}
