<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\LaporanMingguan;
use App\Models\LaporanBulanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanMingguanController extends Controller
{
    private function getAssignedProyeks()
    {
        $user = Auth::user();
        if ($user->isKontraktor()) {
            return Proyek::where('kontraktor_id', $user->id)->where('status', 'aktif')->get();
        } elseif ($user->isKonsultan()) {
            return Proyek::where('konsultan_id', $user->id)->where('status', 'aktif')->get();
        } elseif ($user->isPPK()) {
            return Proyek::where('ppk_id', $user->id)->get();
        } elseif ($user->isPPTK()) {
            return Proyek::where('pptk_id', $user->id)->get();
        }
        return collect();
    }

    public function index()
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        
        $query = LaporanMingguan::whereIn('proyek_id', $proyekIds)
            ->with(['kontraktor', 'proyek']);

        if (Auth::user()->isKontraktor()) {
            $query->where('kontraktor_id', Auth::id());
        }

        $laporans = $query->latest('tanggal_mulai')->paginate(20);
        
        // Pass $proyek explicitly for Kontraktor's create button if they only have 1 active project
        $proyek = null;
        if (Auth::user()->isKontraktor()) {
            $proyek = $this->getAssignedProyeks()->first();
        }

        return view('laporan_mingguan.index', compact('laporans', 'proyek'));
    }

    public function create()
    {
        if (!Auth::user()->isKontraktor()) abort(403);
        $proyeks = $this->getAssignedProyeks();
        if ($proyeks->isEmpty()) {
            return redirect()->route('dashboard')->with('error', 'Anda belum ditugaskan ke proyek aktif manapun.');
        }
        $proyek = $proyeks->first();
        $lastMinggu = LaporanMingguan::where('proyek_id', $proyek->id)->max('minggu_ke') ?? 0;
        
        $laporanLalu = LaporanMingguan::where('proyek_id', $proyek->id)
            ->where('minggu_ke', $lastMinggu)
            ->first();
            
        $bobotLaluRealisasi = $laporanLalu ? $laporanLalu->bobot_realisasi : 0;
        $bobotLaluRencana = $laporanLalu ? $laporanLalu->bobot_rencana : 0;
        
        return view('laporan_mingguan.create', compact('proyek', 'lastMinggu', 'bobotLaluRealisasi', 'bobotLaluRencana'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isKontraktor()) abort(403);

        $validated = $request->validate([
            'proyek_id'          => 'required|exists:proyeks,id',
            'minggu_ke'          => 'required|integer|min:1',
            'tanggal_mulai'      => 'required|date',
            'tanggal_selesai'    => 'required|date|after_or_equal:tanggal_mulai',
            'file_laporan'       => 'nullable|file|mimes:pdf,xls,xlsx,doc,docx|max:10240',
            'ringkasan_kemajuan' => 'nullable|string',
            'kendala'            => 'nullable|string',
            'dokumentasi'        => 'nullable|array',
            'dokumentasi.*'      => 'file|mimes:jpg,jpeg,png|max:10240',
            'progress_minggu_ini'=> 'required|numeric|min:0|max:100',
            'rencana_minggu_ini' => 'required|numeric|min:0|max:100',
            'action'             => 'required|in:draft,submit',
            'lampiran_tambahan'  => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:10240',
        ]);

        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($validated['proyek_id'])) abort(403);

        $status = $validated['action'] === 'submit' ? 'submitted' : 'draft';

        $laporanLalu = LaporanMingguan::where('proyek_id', $validated['proyek_id'])
            ->where('minggu_ke', $validated['minggu_ke'] - 1)
            ->first();
            
        $bobotLaluRealisasi = $laporanLalu ? $laporanLalu->bobot_realisasi : 0;
        $bobotLaluRencana = $laporanLalu ? $laporanLalu->bobot_rencana : 0;
        
        $bobotRealisasiKumulatif = $bobotLaluRealisasi + $validated['progress_minggu_ini'];
        $bobotRencanaKumulatif = $bobotLaluRencana + $validated['rencana_minggu_ini'];
        $deviasi = $bobotRealisasiKumulatif - $bobotRencanaKumulatif;

        $path = null;
        if ($request->hasFile('file_laporan')) {
            $path = $request->file('file_laporan')->store('laporan_mingguan_docs', 'public');
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran_tambahan')) {
            $lampiranPath = $request->file('lampiran_tambahan')->store('laporan_mingguan_docs', 'public');
        }
        
        $dokumentasiPaths = [];
        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                $dokumentasiPaths[] = $file->store('laporan_mingguan_docs', 'public');
            }
        }

        LaporanMingguan::create([
            'proyek_id'          => $validated['proyek_id'],
            'kontraktor_id'      => Auth::id(),
            'minggu_ke'          => $validated['minggu_ke'],
            'tanggal_mulai'      => $validated['tanggal_mulai'],
            'tanggal_selesai'    => $validated['tanggal_selesai'],
            'bobot_rencana'      => $bobotRencanaKumulatif,
            'bobot_realisasi'    => $bobotRealisasiKumulatif,
            'deviasi'            => $deviasi,
            'file_laporan'       => $path,
            'ringkasan_kemajuan' => $validated['ringkasan_kemajuan'] ?? null,
            'kendala'            => $validated['kendala'] ?? null,
            'dokumentasi'        => $dokumentasiPaths,
            'status'             => $status,
            'lampiran_tambahan'  => $lampiranPath,
        ]);

        $msg = $validated['action'] === 'submit' ? 'Laporan mingguan berhasil dikirim ke Konsultan Pengawas.' : 'Laporan mingguan disimpan sebagai draft.';
        return redirect()->route('laporan-mingguan.index')->with('success', $msg);
    }

    public function show(LaporanMingguan $laporanMingguan)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanMingguan->proyek_id)) abort(403);
        if (Auth::user()->isKontraktor() && $laporanMingguan->kontraktor_id !== Auth::id()) abort(403);

        $laporanMingguan->load(['kontraktor', 'proyek']);
        return view('laporan_mingguan.show', compact('laporanMingguan'));
    }

    public function verify(Request $request, LaporanMingguan $laporanMingguan)
    {
        if (!Auth::user()->isKonsultan()) abort(403);
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanMingguan->proyek_id)) abort(403);

        $laporanMingguan->update([
            'status'            => 'verified',
            'catatan_konsultan' => $request->catatan_konsultan,
            'verified_by'       => Auth::id(),
            'verified_at'       => now(),
        ]);
        return redirect()->route('laporan-mingguan.index')->with('success', 'Laporan mingguan berhasil diverifikasi.');
    }

    public function rejectKonsultan(Request $request, LaporanMingguan $laporanMingguan)
    {
        if (!Auth::user()->isKonsultan()) abort(403);
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanMingguan->proyek_id)) abort(403);

        $request->validate(['catatan_konsultan' => 'required|string']);

        $laporanMingguan->update([
            'status'            => 'rejected',
            'catatan_konsultan' => $request->catatan_konsultan,
            'verified_by'       => Auth::id(),
            'verified_at'       => now(),
        ]);
        return redirect()->route('laporan-mingguan.index')->with('success', 'Laporan mingguan dikembalikan ke Kontraktor.');
    }

    public function approve(Request $request, LaporanMingguan $laporanMingguan)
    {
        if (!Auth::user()->isPPTK()) abort(403);
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanMingguan->proyek_id)) abort(403);

        $laporanMingguan->update([
            'status'      => 'approved',
            'catatan_ppk' => $request->catatan_ppk,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        if ($laporanMingguan->minggu_ke % 4 == 0) {
            $bulan_ke = $laporanMingguan->minggu_ke / 4;
            $tahun = date('Y', strtotime($laporanMingguan->tanggal_selesai));
            
            // Check if already exists to prevent duplicate
            $exists = LaporanBulanan::where('proyek_id', $laporanMingguan->proyek_id)
                ->where('bulan', $bulan_ke)
                ->where('tahun', $tahun)
                ->exists();
                
            if (!$exists) {
                // Get the 4 weekly reports for this month
                $startMinggu = $laporanMingguan->minggu_ke - 3;
                $fourWeeks = LaporanMingguan::where('proyek_id', $laporanMingguan->proyek_id)
                    ->whereBetween('minggu_ke', [$startMinggu, $laporanMingguan->minggu_ke])
                    ->get();
                
                $ringkasan = [];
                $kendala = [];
                $dokumentasi = [];
                
                foreach($fourWeeks as $week) {
                    if ($week->ringkasan_kemajuan) $ringkasan[] = "Minggu {$week->minggu_ke}: " . $week->ringkasan_kemajuan;
                    if ($week->kendala) $kendala[] = "Minggu {$week->minggu_ke}: " . $week->kendala;
                    if (is_array($week->dokumentasi)) {
                        $dokumentasi = array_merge($dokumentasi, $week->dokumentasi);
                    }
                }

                LaporanBulanan::create([
                    'proyek_id' => $laporanMingguan->proyek_id,
                    'kontraktor_id' => $laporanMingguan->kontraktor_id,
                    'bulan' => $bulan_ke,
                    'tahun' => $tahun,
                    'bobot_rencana' => $laporanMingguan->bobot_rencana,
                    'bobot_realisasi' => $laporanMingguan->bobot_realisasi,
                    'deviasi' => $laporanMingguan->deviasi,
                    'ringkasan_kemajuan' => implode("\n", $ringkasan),
                    'kendala' => implode("\n", $kendala),
                    'status' => 'draft',
                    'dokumentasi' => $dokumentasi,
                ]);
            }
        }

        return redirect()->route('laporan-mingguan.index')->with('success', 'Laporan mingguan disetujui.');
    }

    public function rejectPPK(Request $request, LaporanMingguan $laporanMingguan)
    {
        if (!Auth::user()->isPPTK()) abort(403);
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanMingguan->proyek_id)) abort(403);

        $request->validate(['catatan_ppk' => 'required|string']);

        $laporanMingguan->update([
            'status'      => 'rejected',
            'catatan_ppk' => $request->catatan_ppk,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return redirect()->route('laporan-mingguan.index')->with('success', 'Laporan mingguan dikembalikan.');
    }
}
