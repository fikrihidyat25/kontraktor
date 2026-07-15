<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\LaporanHarian;
use App\Models\UangMuka;
use App\Models\KerjaTambahKurang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KontraktorController extends Controller
{
    public function dashboard()
    {
        $proyekId = session('active_proyek_id');
        
        // Jika belum ada proyek yang dipilih, otomatis pilih proyek aktif pertama
        if (!$proyekId) {
            $firstProyek = Proyek::where('kontraktor_id', Auth::id())
                ->where('status', 'aktif')
                ->latest()
                ->first();
                
            if ($firstProyek) {
                $proyekId = $firstProyek->id;
                session(['active_proyek_id' => $proyekId]);
            }
        }
        $proyek = null;
        $recentLaporan = [];
        $uangMukas = [];
        $ktks = [];
        $stats = ['draft' => 0, 'submitted' => 0, 'verified' => 0, 'approved' => 0, 'rejected' => 0];

        if ($proyekId) {
            $proyek = Proyek::find($proyekId);
            
            if ($proyek) {
                $recentLaporan = LaporanHarian::where('proyek_id', $proyek->id)
                    ->where('kontraktor_id', Auth::id())
                    ->with('proyek')
                    ->latest('tanggal')
                    ->take(7)
                    ->get();

                foreach ($stats as $key => &$val) {
                    $val = LaporanHarian::where('proyek_id', $proyek->id)
                        ->where('kontraktor_id', Auth::id())
                        ->where('status', $key)
                        ->count();
                }

                $uangMukas = UangMuka::where('proyek_id', $proyek->id)
                    ->where('kontraktor_id', Auth::id())
                    ->latest()
                    ->get();

                $ktks = KerjaTambahKurang::where('proyek_id', $proyek->id)
                    ->where('kontraktor_id', Auth::id())
                    ->latest()
                    ->get();
            }
        }

        return view('kontraktor.dashboard', compact('proyek', 'recentLaporan', 'uangMukas', 'ktks', 'stats'));
    }
}
