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
        if (!Auth::user()->isKontraktor()) abort(403);

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

        KerjaTambahKurang::create([
            'proyek_id' => $request->proyek_id,
            'kontraktor_id' => Auth::id(),
            'nomor_surat_pengajuan' => $request->nomor_surat_pengajuan,
            'tanggal_pengajuan' => now(),
            'jenis_ktk' => $request->jenis_ktk,
            'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
            'nilai_estimasi' => $request->nilai_estimasi,
            'dokumen_pendukung' => $path,
            'status' => 'diajukan',
        ]);

        return redirect()->route('kontraktor.dashboard')->with(['success' => 'Usulan Kerja Tambah Kurang berhasil diajukan.', 'currentMenu' => 'ktk']);
    }

    public function verify(Request $request, KerjaTambahKurang $ktk)
    {
        if (!Auth::user()->isKonsultan()) abort(403);

        $ktk->update([
            'status' => 'diverifikasi_konsultan',
            'catatan_konsultan' => $request->catatan_konsultan,
        ]);

        return redirect()->route('kerja-tambah-kurang.index')->with('success', 'Usulan KTK diverifikasi dan diteruskan ke PPK.');
    }

    public function rejectKonsultan(Request $request, KerjaTambahKurang $ktk)
    {
        if (!Auth::user()->isKonsultan()) abort(403);

        $ktk->update([
            'status' => 'ditolak',
            'catatan_konsultan' => $request->catatan_konsultan,
        ]);

        return redirect()->route('kerja-tambah-kurang.index')->with('error', 'Usulan KTK dikembalikan ke kontraktor.');
    }

    public function approve(Request $request, KerjaTambahKurang $ktk)
    {
        if (!Auth::user()->isPPK()) abort(403);

        $ktk->update([
            'status' => 'disetujui_ppk',
            'catatan_ppk' => $request->catatan_ppk,
        ]);

        return redirect()->route('kerja-tambah-kurang.index')->with('success', 'Usulan KTK disetujui.');
    }

    public function rejectPPK(Request $request, KerjaTambahKurang $ktk)
    {
        if (!Auth::user()->isPPK()) abort(403);

        $ktk->update([
            'status' => 'ditolak',
            'catatan_ppk' => $request->catatan_ppk,
        ]);

        return redirect()->route('kerja-tambah-kurang.index')->with('error', 'Usulan KTK ditolak.');
    }
}
