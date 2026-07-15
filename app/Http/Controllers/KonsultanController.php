<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\LaporanHarian;
use App\Models\LaporanMingguan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KonsultanController extends Controller
{
    private function getAssignedProyeks()
    {
        return Proyek::where('konsultan_id', Auth::id())
            ->where('status', 'aktif')
            ->get();
    }

    public function dashboard()
    {
        $proyeks = $this->getAssignedProyeks();
        $proyekIds = $proyeks->pluck('id');

        $stats = [
            'menunggu_verifikasi' => LaporanHarian::whereIn('proyek_id', $proyekIds)
                ->where('status', 'submitted')->count(),
            'sudah_diverifikasi'  => LaporanHarian::whereIn('proyek_id', $proyekIds)
                ->where('status', 'verified')->count(),
            'sudah_diapprove'     => LaporanHarian::whereIn('proyek_id', $proyekIds)
                ->where('status', 'approved')->count(),
            'ditolak'             => LaporanHarian::whereIn('proyek_id', $proyekIds)
                ->where('status', 'rejected')->count(),
        ];

        $pendingLaporan = LaporanHarian::whereIn('proyek_id', $proyekIds)
            ->where('status', 'submitted')
            ->with(['kontraktor', 'proyek'])
            ->latest('tanggal')
            ->take(10)
            ->get();

        return view('konsultan.dashboard', compact('proyeks', 'stats', 'pendingLaporan'));
    }

    // VERIFIKASI LAPORAN HARIAN
    public function laporanHarianIndex()
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        $laporans  = LaporanHarian::whereIn('proyek_id', $proyekIds)
            ->with(['kontraktor', 'proyek'])
            ->latest('tanggal')
            ->paginate(20);
        return view('konsultan.laporan_harian.index', compact('laporans'));
    }

    public function laporanHarianShow(LaporanHarian $laporanHarian)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanHarian->proyek_id)) {
            abort(403);
        }
        $laporanHarian->load(['tenagaKerjas', 'materials', 'peralatans', 'realisasiBiayas', 'kontraktor', 'proyek']);
        return view('konsultan.laporan_harian.show', compact('laporanHarian'));
    }

    public function verify(Request $request, LaporanHarian $laporanHarian)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanHarian->proyek_id)) {
            abort(403);
        }

        if ($laporanHarian->status !== 'submitted') {
            return back()->with('error', 'Laporan ini tidak dalam status menunggu verifikasi.');
        }

        $validated = $request->validate([
            'catatan_konsultan' => 'nullable|string',
        ]);

        $laporanHarian->update([
            'status'            => 'verified',
            'catatan_konsultan' => $validated['catatan_konsultan'] ?? null,
            'verified_by'       => Auth::id(),
            'verified_at'       => now(),
        ]);

        return redirect()->route('konsultan.laporan-harian.index')
            ->with('success', 'Laporan berhasil diverifikasi dan diteruskan ke PPK.');
    }

    public function reject(Request $request, LaporanHarian $laporanHarian)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanHarian->proyek_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'catatan_konsultan' => 'required|string',
        ]);

        $laporanHarian->update([
            'status'            => 'rejected',
            'catatan_konsultan' => $validated['catatan_konsultan'],
            'verified_by'       => Auth::id(),
            'verified_at'       => now(),
        ]);

        return redirect()->route('konsultan.laporan-harian.index')
            ->with('success', 'Laporan dikembalikan ke Kontraktor untuk perbaikan.');
    }

    // VERIFIKASI LAPORAN MINGGUAN
    public function laporanMingguanIndex()
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        $laporans  = LaporanMingguan::whereIn('proyek_id', $proyekIds)
            ->with(['kontraktor', 'proyek'])
            ->latest()
            ->paginate(20);
        return view('konsultan.laporan_mingguan.index', compact('laporans'));
    }

    public function laporanMingguanShow(LaporanMingguan $laporanMingguan)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanMingguan->proyek_id)) {
            abort(403);
        }
        $laporanMingguan->load(['kontraktor', 'proyek']);
        return view('konsultan.laporan_mingguan.show', compact('laporanMingguan'));
    }

    public function verifyMingguan(Request $request, LaporanMingguan $laporanMingguan)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanMingguan->proyek_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'catatan_konsultan' => 'nullable|string',
        ]);

        $laporanMingguan->update([
            'status'            => 'verified',
            'catatan_konsultan' => $validated['catatan_konsultan'] ?? null,
            'verified_by'       => Auth::id(),
            'verified_at'       => now(),
        ]);

        return redirect()->route('konsultan.laporan-mingguan.index')
            ->with('success', 'Laporan mingguan berhasil diverifikasi.');
    }

    public function rejectMingguan(Request $request, LaporanMingguan $laporanMingguan)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanMingguan->proyek_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'catatan_konsultan' => 'required|string',
        ]);

        $laporanMingguan->update([
            'status'            => 'rejected',
            'catatan_konsultan' => $validated['catatan_konsultan'],
            'verified_by'       => Auth::id(),
            'verified_at'       => now(),
        ]);

        return redirect()->route('konsultan.laporan-mingguan.index')
            ->with('success', 'Laporan mingguan dikembalikan ke Kontraktor.');
    }
}
