<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanBulanan;
use App\Models\Proyek;

class LaporanBulananController extends Controller
{
    public function index()
    {
        $query = LaporanBulanan::with(['proyek', 'kontraktor']);
        
        if (auth()->user()->isKontraktor()) {
            $query->where('kontraktor_id', auth()->id());
        } elseif (auth()->user()->isKonsultan()) {
            $query->whereHas('proyek', function ($q) {
                $q->where('konsultan_id', auth()->id());
            });
        } elseif (auth()->user()->isPPK()) {
            $query->whereHas('proyek', function ($q) {
                $q->where('ppk_id', auth()->id());
            });
        }
        
        $laporans = $query->latest()->paginate(10);
        return view('laporan_bulanan.index', compact('laporans'));
    }

    public function create()
    {
        $this->authorize('create', LaporanBulanan::class);
        $proyeks = Proyek::where('kontraktor_id', auth()->id())->get();
        return view('laporan_bulanan.create', compact('proyeks'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', LaporanBulanan::class);
        
        $validated = $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
            'bobot_rencana' => 'required|numeric|min:0|max:100',
            'bobot_realisasi' => 'required|numeric|min:0|max:100',
            'ringkasan_kemajuan' => 'nullable|string',
            'kendala' => 'nullable|string',
            'file_laporan' => 'nullable|mimes:pdf|max:10240',
        ]);

        $validated['kontraktor_id'] = auth()->id();
        $validated['deviasi'] = $validated['bobot_realisasi'] - $validated['bobot_rencana'];
        $validated['status'] = 'draft';

        if ($request->hasFile('file_laporan')) {
            $validated['file_laporan'] = $request->file('file_laporan')->store('laporan_bulanan', 'public');
        }

        LaporanBulanan::create($validated);
        return redirect()->route('laporan-bulanan.index')->with('success', 'Laporan bulanan berhasil disimpan sebagai draft.');
    }

    public function show(LaporanBulanan $laporanBulanan)
    {
        $this->authorize('view', $laporanBulanan);
        return view('laporan_bulanan.show', compact('laporanBulanan'));
    }

    public function submit(Request $request, LaporanBulanan $laporanBulanan)
    {
        $this->authorize('submit', $laporanBulanan);
        $laporanBulanan->update(['status' => 'submitted']);
        return back()->with('success', 'Laporan berhasil dikirim ke Konsultan.');
    }

    public function verify(Request $request, LaporanBulanan $laporanBulanan)
    {
        $this->authorize('verify', $laporanBulanan);
        
        $request->validate(['catatan_konsultan' => 'nullable|string']);
        
        $laporanBulanan->update([
            'status' => 'verified',
            'catatan_konsultan' => $request->catatan_konsultan,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);
        return back()->with('success', 'Laporan berhasil diverifikasi.');
    }

    public function approve(Request $request, LaporanBulanan $laporanBulanan)
    {
        $this->authorize('approve', $laporanBulanan);
        
        $request->validate(['catatan_ppk' => 'nullable|string']);
        
        $laporanBulanan->update([
            'status' => 'approved',
            'catatan_ppk' => $request->catatan_ppk,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'Laporan berhasil disetujui (Approved).');
    }

    public function reject(Request $request, LaporanBulanan $laporanBulanan)
    {
        if (auth()->user()->isKonsultan()) {
            $this->authorize('verify', $laporanBulanan);
            $request->validate(['catatan_konsultan' => 'required|string']);
            $laporanBulanan->update([
                'status' => 'rejected',
                'catatan_konsultan' => $request->catatan_konsultan,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);
            return back()->with('error', 'Laporan ditolak oleh Konsultan.');
        }

        if (auth()->user()->isPPK()) {
            $this->authorize('approve', $laporanBulanan);
            $request->validate(['catatan_ppk' => 'required|string']);
            $laporanBulanan->update([
                'status' => 'rejected',
                'catatan_ppk' => $request->catatan_ppk,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            return back()->with('error', 'Laporan ditolak oleh PPK.');
        }

        abort(403);
    }
}
