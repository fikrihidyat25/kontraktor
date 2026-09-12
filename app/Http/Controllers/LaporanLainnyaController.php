<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanLainnya;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanLainnyaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = LaporanLainnya::with('proyek')->latest();
        
        if ($user->isKontraktor()) {
            $query->whereHas('proyek', function($q) use($user) { $q->where('kontraktor_id', $user->id); });
            $proyeks = Proyek::where('kontraktor_id', $user->id)->get();
        } elseif ($user->isKonsultan()) {
            $query->whereHas('proyek', function($q) use($user) { $q->where('konsultan_id', $user->id); });
            $proyeks = Proyek::where('konsultan_id', $user->id)->get();
        } elseif ($user->isPPTK()) {
            $query->whereHas('proyek', function($q) use($user) { $q->where('pptk_id', $user->id); });
            $proyeks = collect();
        } elseif ($user->isPPK()) {
            $query->whereHas('proyek', function($q) use($user) { $q->where('ppk_id', $user->id); });
            $proyeks = collect();
        } else {
            $proyeks = collect();
        }

        $laporans = $query->get();

        return view('laporan_lainnya.index', compact('laporans', 'proyeks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'judul_laporan' => 'required|string|max:255',
            'tanggal_laporan' => 'required|date',
            'keterangan' => 'nullable|string',
            'file_laporan' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip,rar|max:10240',
            'lampiran_tambahan.*' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:5120',
        ]);

        $laporanPath = $request->file('file_laporan')->store('laporan_lainnya', 'public');
        
        $lampiranPaths = [];
        if ($request->hasFile('lampiran_tambahan')) {
            foreach ($request->file('lampiran_tambahan') as $file) {
                $lampiranPaths[] = $file->store('laporan_lainnya', 'public');
            }
        }

        LaporanLainnya::create([
            'proyek_id' => $request->proyek_id,
            'judul_laporan' => $request->judul_laporan,
            'tanggal_laporan' => $request->tanggal_laporan,
            'keterangan' => $request->keterangan,
            'file_laporan' => $laporanPath,
            'lampiran_tambahan' => !empty($lampiranPaths) ? $lampiranPaths : null,
            'uploaded_by' => Auth::id(),
            'status' => 'menunggu_validasi',
        ]);

        return back()->with('success', 'Laporan lainnya berhasil diunggah.');
    }

    public function approve(Request $request, LaporanLainnya $laporanLainnya)
    {
        if (!Auth::user()->isPPTK()) abort(403, 'Hanya PPTK yang dapat memvalidasi laporan.');

        $laporanLainnya->update([
            'status' => 'disetujui',
            'catatan_pptk' => $request->catatan_pptk,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->route('laporan-lainnya.index')->with('success', 'Laporan Lainnya berhasil disetujui.');
    }

    public function reject(Request $request, LaporanLainnya $laporanLainnya)
    {
        if (!Auth::user()->isPPTK()) abort(403, 'Hanya PPTK yang dapat memvalidasi laporan.');

        $laporanLainnya->update([
            'status' => 'ditolak',
            'catatan_pptk' => $request->catatan_pptk,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->route('laporan-lainnya.index')->with('error', 'Laporan Lainnya ditolak.');
    }

    public function destroy(LaporanLainnya $laporanLainnya)
    {
        if ($laporanLainnya->uploaded_by !== Auth::id()) {
            abort(403, 'Tidak diizinkan');
        }

        Storage::disk('public')->delete($laporanLainnya->file_laporan);
        if ($laporanLainnya->lampiran_tambahan) {
            foreach ($laporanLainnya->lampiran_tambahan as $path) {
                Storage::disk('public')->delete($path);
            }
        }
        
        $laporanLainnya->delete();
        
        return back()->with('success', 'Laporan lainnya berhasil dihapus.');
    }
}
