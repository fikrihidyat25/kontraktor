<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\LaporanMingguan;
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
        
        return view('laporan_mingguan.create', compact('proyek', 'lastMinggu'));
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
            'action'             => 'required|in:draft,submit',
        ]);

        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($validated['proyek_id'])) abort(403);

        $status = $validated['action'] === 'submit' ? 'submitted' : 'draft';

        $path = null;
        if ($request->hasFile('file_laporan')) {
            $path = $request->file('file_laporan')->store('laporan_mingguan_docs', 'public');
        }

        LaporanMingguan::create([
            'proyek_id'          => $validated['proyek_id'],
            'kontraktor_id'      => Auth::id(),
            'minggu_ke'          => $validated['minggu_ke'],
            'tanggal_mulai'      => $validated['tanggal_mulai'],
            'tanggal_selesai'    => $validated['tanggal_selesai'],
            'file_laporan'       => $path,
            'ringkasan_kemajuan' => $validated['ringkasan_kemajuan'] ?? null,
            'kendala'            => $validated['kendala'] ?? null,
            'status'             => $status,
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
        if (!Auth::user()->isPPK()) abort(403);
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanMingguan->proyek_id)) abort(403);

        $laporanMingguan->update([
            'status'      => 'approved',
            'catatan_ppk' => $request->catatan_ppk,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        return redirect()->route('laporan-mingguan.index')->with('success', 'Laporan mingguan disetujui.');
    }

    public function rejectPPK(Request $request, LaporanMingguan $laporanMingguan)
    {
        if (!Auth::user()->isPPK()) abort(403);
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
