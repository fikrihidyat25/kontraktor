<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\LaporanHarian;
use App\Models\LaporanMingguan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PPKController extends Controller
{
    private function getAssignedProyeks()
    {
        return Proyek::where('ppk_id', Auth::id())->get();
    }

    public function dashboard()
    {
        $proyeks   = $this->getAssignedProyeks();
        $proyekIds = $proyeks->pluck('id');

        // S-Curve data dari laporan mingguan yang sudah approved
        $sCurveData = [];
        foreach ($proyeks as $proyek) {
            $mingguans = LaporanMingguan::where('proyek_id', $proyek->id)
                ->where('status', 'approved')
                ->orderBy('minggu_ke')
                ->get(['minggu_ke', 'bobot_rencana', 'bobot_realisasi', 'deviasi']);

            $sCurveData[$proyek->id] = $mingguans;
        }

        $stats = [
            'menunggu_approval' => LaporanHarian::whereIn('proyek_id', $proyekIds)
                ->where('status', 'verified')->count(),
            'sudah_diapprove'   => LaporanHarian::whereIn('proyek_id', $proyekIds)
                ->where('status', 'approved')->count(),
            'total_laporan'     => LaporanHarian::whereIn('proyek_id', $proyekIds)->count(),
        ];

        $pendingApproval = LaporanHarian::whereIn('proyek_id', $proyekIds)
            ->where('status', 'verified')
            ->with(['kontraktor', 'proyek', 'verifiedBy'])
            ->latest('tanggal')
            ->take(10)
            ->get();

        return view('ppk.dashboard', compact('proyeks', 'stats', 'pendingApproval', 'sCurveData'));
    }

    public function laporanHarianIndex()
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        $laporans  = LaporanHarian::whereIn('proyek_id', $proyekIds)
            ->with(['kontraktor', 'proyek', 'verifiedBy'])
            ->latest('tanggal')
            ->paginate(20);
        return view('ppk.laporan_harian.index', compact('laporans'));
    }

    public function laporanHarianShow(LaporanHarian $laporanHarian)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanHarian->proyek_id)) {
            abort(403);
        }
        $laporanHarian->load(['tenagaKerjas', 'materials', 'peralatans', 'realisasiBiayas', 'kontraktor', 'proyek', 'verifiedBy']);
        return view('ppk.laporan_harian.show', compact('laporanHarian'));
    }

    public function approve(Request $request, LaporanHarian $laporanHarian)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanHarian->proyek_id)) {
            abort(403);
        }

        if ($laporanHarian->status !== 'verified') {
            return back()->with('error', 'Laporan ini belum diverifikasi oleh Konsultan.');
        }

        $validated = $request->validate([
            'catatan_ppk' => 'nullable|string',
        ]);

        $laporanHarian->update([
            'status'      => 'approved',
            'catatan_ppk' => $validated['catatan_ppk'] ?? null,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('ppk.laporan-harian.index')
            ->with('success', 'Laporan disetujui. Data bobot resmi masuk perhitungan kemajuan proyek.');
    }

    public function reject(Request $request, LaporanHarian $laporanHarian)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanHarian->proyek_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'catatan_ppk' => 'required|string',
        ]);

        $laporanHarian->update([
            'status'      => 'rejected',
            'catatan_ppk' => $validated['catatan_ppk'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('ppk.laporan-harian.index')
            ->with('success', 'Laporan dikembalikan untuk perbaikan.');
    }

    // LAPORAN MINGGUAN
    public function laporanMingguanIndex()
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        $laporans  = LaporanMingguan::whereIn('proyek_id', $proyekIds)
            ->with(['kontraktor', 'proyek'])
            ->latest()
            ->paginate(20);
        return view('ppk.laporan_mingguan.index', compact('laporans'));
    }

    public function approveMingguan(Request $request, LaporanMingguan $laporanMingguan)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanMingguan->proyek_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'catatan_ppk' => 'nullable|string',
        ]);

        $laporanMingguan->update([
            'status'      => 'approved',
            'catatan_ppk' => $validated['catatan_ppk'] ?? null,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('ppk.laporan-mingguan.index')
            ->with('success', 'Laporan mingguan disetujui.');
    }

    public function rejectMingguan(Request $request, LaporanMingguan $laporanMingguan)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanMingguan->proyek_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'catatan_ppk' => 'required|string',
        ]);

        $laporanMingguan->update([
            'status'      => 'rejected',
            'catatan_ppk' => $validated['catatan_ppk'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('ppk.laporan-mingguan.index')
            ->with('success', 'Laporan mingguan dikembalikan.');
    }
}
