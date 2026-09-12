<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KerjaTambahKurang;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;

class KerjaTambahKurangController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = KerjaTambahKurang::with(['proyek', 'kontraktor'])->latest();

        if ($user->isKontraktor()) {
            $query->where('kontraktor_id', $user->id);
            $proyeks = Proyek::where('kontraktor_id', $user->id)->get();
        } elseif ($user->isPPTK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('pptk_id', $user->id);
            });
            $proyeks = Proyek::where('pptk_id', $user->id)->get();
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

        $ktks = $query->get();

        return view('ktk.index', compact('ktks', 'proyeks'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->isKontraktor() && !$user->isPPTK()) abort(403);

        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'nomor_surat_pengajuan' => 'required|string|max:255',
            'jenis_ktk' => 'required|in:tambah,kurang',
            'deskripsi_pekerjaan' => 'required|string',
            'nilai_estimasi' => 'required|numeric|min:0',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $path = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $path = $request->file('dokumen_pendukung')->store('ktk_docs', 'public');
        }

        $proyek = Proyek::findOrFail($request->proyek_id);

        KerjaTambahKurang::create([
            'proyek_id' => $proyek->id,
            'kontraktor_id' => $proyek->kontraktor_id,
            'nomor_surat_pengajuan' => $request->nomor_surat_pengajuan,
            'tanggal_pengajuan' => now(),
            'usulan_dari' => $user->isPPTK() ? 'pptk' : 'kontraktor',
            'jenis_ktk' => $request->jenis_ktk,
            'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
            'nilai_estimasi' => $request->nilai_estimasi,
            'dokumen_pendukung' => $path,
            'status' => 'menunggu_validasi',
        ]);

        return redirect()->back()->with(['success' => 'Usulan Pekerjaan Tambah Kurang berhasil diajukan.', 'currentMenu' => 'ptk']);
    }

    public function approve(Request $request, KerjaTambahKurang $ktk)
    {
        $user = Auth::user();
        if (!$user->isKontraktor() && !$user->isPPTK()) abort(403);

        if ($ktk->usulan_dari == 'kontraktor' && !$user->isPPTK()) abort(403);
        if ($ktk->usulan_dari == 'pptk' && !$user->isKontraktor()) abort(403);

        $ktk->update([
            'status' => 'disetujui',
            'catatan_evaluasi' => $request->catatan_evaluasi,
        ]);

        return redirect()->route('kerja-tambah-kurang.index')->with('success', 'Usulan PTK disetujui.');
    }

    public function reject(Request $request, KerjaTambahKurang $ktk)
    {
        $user = Auth::user();
        if (!$user->isKontraktor() && !$user->isPPTK()) abort(403);

        if ($ktk->usulan_dari == 'kontraktor' && !$user->isPPTK()) abort(403);
        if ($ktk->usulan_dari == 'pptk' && !$user->isKontraktor()) abort(403);

        $ktk->update([
            'status' => 'revisi',
            'catatan_evaluasi' => $request->catatan_evaluasi,
        ]);

        return redirect()->route('kerja-tambah-kurang.index')->with('error', 'Usulan PTK dikembalikan untuk revisi.');
    }
}
