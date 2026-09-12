<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanPengawas;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;

class LaporanPengawasController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = LaporanPengawas::with(['proyek', 'konsultan', 'verifier'])->latest();

        if ($user->isKonsultan()) {
            $query->where('konsultan_id', $user->id);
            $proyeks = Proyek::where('konsultan_id', $user->id)->get();
        } elseif ($user->isPPK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('ppk_id', $user->id);
            });
            $proyeks = collect();
        } elseif ($user->isPPTK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('pptk_id', $user->id);
            });
            $proyeks = collect();
        } else {
            $proyeks = collect();
        }

        $laporans = $query->get();

        return view('laporan_pengawas.index', compact('laporans', 'proyeks'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isKonsultan()) {
            abort(403, 'Hanya konsultan pengawas yang dapat membuat laporan pengawas.');
        }

        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'jenis' => 'required|in:mingguan,bulanan,akhir',
            'file_laporan' => 'required|file|mimes:pdf,zip,rar|max:20480', // max 20MB
            'catatan' => 'nullable|string',
        ]);

        $proyek = Proyek::findOrFail($request->proyek_id);
        if ($proyek->konsultan_id !== Auth::id()) {
            abort(403, 'Anda tidak ditugaskan pada proyek ini.');
        }

        $path = $request->file('file_laporan')->store('laporan_pengawas', 'public');

        LaporanPengawas::create([
            'proyek_id' => $request->proyek_id,
            'konsultan_id' => Auth::id(),
            'jenis' => $request->jenis,
            'file_laporan' => $path,
            'catatan' => $request->catatan,
            'status' => 'diajukan',
        ]);

        return redirect()->route('laporan-pengawas.index')->with('success', 'Laporan pengawas berhasil diajukan.');
    }

    public function approve(Request $request, LaporanPengawas $laporanPengawa)
    {
        if (!Auth::user()->isPPTK()) {
            abort(403, 'Hanya PPTK yang diizinkan untuk memvalidasi laporan ini.');
        }

        $laporanPengawa->update([
            'status' => 'disetujui',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->route('laporan-pengawas.index')->with('success', 'Laporan disetujui.');
    }

    public function reject(Request $request, LaporanPengawas $laporanPengawa)
    {
        if (!Auth::user()->isPPTK()) {
            abort(403, 'Hanya PPTK yang diizinkan untuk memvalidasi laporan ini.');
        }

        $laporanPengawa->update([
            'status' => 'ditolak',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->route('laporan-pengawas.index')->with('error', 'Laporan ditolak.');
    }
}
