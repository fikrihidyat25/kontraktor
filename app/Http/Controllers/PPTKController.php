<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\LaporanHarian;
use App\Models\LaporanMingguan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PPTKController extends Controller
{
    private function getAssignedProyeks()
    {
        return Proyek::where('pptk_id', Auth::id())->get();
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
            'menunggu_verifikasi' => LaporanHarian::whereIn('proyek_id', $proyekIds)
                ->where('status', 'verified')->count(),
            'sudah_diapprove'     => LaporanHarian::whereIn('proyek_id', $proyekIds)
                ->where('status', 'approved')->count(),
            'total_laporan'       => LaporanHarian::whereIn('proyek_id', $proyekIds)->count(),
        ];

        $pendingApproval = LaporanHarian::whereIn('proyek_id', $proyekIds)
            ->where('status', 'verified')
            ->with(['kontraktor', 'proyek', 'verifiedBy'])
            ->latest('tanggal')
            ->take(10)
            ->get();

        $laporanTerbaru = LaporanHarian::whereIn('proyek_id', $proyekIds)
            ->with(['kontraktor', 'proyek', 'verifiedBy'])
            ->latest('tanggal')
            ->take(10)
            ->get();

        return view('pptk.dashboard', compact('proyeks', 'stats', 'pendingApproval', 'laporanTerbaru', 'sCurveData'));
    }

    public function laporanHarianIndex()
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        $laporans  = LaporanHarian::whereIn('proyek_id', $proyekIds)
            ->with(['kontraktor', 'proyek', 'verifiedBy'])
            ->latest('tanggal')
            ->paginate(20);
        return view('laporan_harian.index', compact('laporans'));
    }

    public function laporanMingguanIndex()
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        $laporans  = LaporanMingguan::whereIn('proyek_id', $proyekIds)
            ->with(['kontraktor', 'proyek'])
            ->latest()
            ->paginate(20);
        return view('laporan_mingguan.index', compact('laporans'));
    }
}
