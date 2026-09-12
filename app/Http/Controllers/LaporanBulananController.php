<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanBulanan;
use App\Models\Proyek;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LaporanBulananController extends Controller
{
    use AuthorizesRequests;

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
        } elseif (auth()->user()->isPPTK()) {
            $query->whereHas('proyek', function ($q) {
                $q->where('pptk_id', auth()->id());
            });
        }
        
        $laporans = $query->latest()->paginate(10);
        return view('laporan_bulanan.index', compact('laporans'));
    }


    public function show(LaporanBulanan $laporanBulanan)
    {
        $this->authorize('view', $laporanBulanan);
        return view('laporan_bulanan.show', compact('laporanBulanan'));
    }

    public function edit(LaporanBulanan $laporanBulanan)
    {
        $this->authorize('update', $laporanBulanan);
        return view('laporan_bulanan.edit', compact('laporanBulanan'));
    }

    public function update(Request $request, LaporanBulanan $laporanBulanan)
    {
        $this->authorize('update', $laporanBulanan);
        
        $request->validate([
            'lampiran_tambahan' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:10240',
            'dokumentasi_tambahan.*' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
        ]);
        
        $data = [];
        
        if ($request->hasFile('lampiran_tambahan')) {
            $data['lampiran_tambahan'] = $request->file('lampiran_tambahan')->store('laporan_bulanan_docs', 'public');
        }
        
        if ($request->hasFile('dokumentasi_tambahan')) {
            $dokumentasi = is_array($laporanBulanan->dokumentasi) ? $laporanBulanan->dokumentasi : [];
            foreach ($request->file('dokumentasi_tambahan') as $file) {
                $dokumentasi[] = $file->store('laporan_bulanan_docs', 'public');
            }
            $data['dokumentasi'] = $dokumentasi;
        }
        
        if(!empty($data)) {
            $laporanBulanan->update($data);
        }
        
        return redirect()->route('laporan-bulanan.show', $laporanBulanan)->with('success', 'Laporan bulanan berhasil diperbarui.');
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
        $this->authorize('approve', $laporanBulanan); // We will need to update policies too if we have any
        if(!auth()->user()->isPPTK()) abort(403);
        
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

        if (auth()->user()->isPPTK()) {
            $this->authorize('approve', $laporanBulanan);
            $request->validate(['catatan_ppk' => 'required|string']);
            $laporanBulanan->update([
                'status' => 'rejected',
                'catatan_ppk' => $request->catatan_ppk,
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            return back()->with('error', 'Laporan ditolak oleh PPTK.');
        }

        abort(403);
    }
}
