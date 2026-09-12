<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanPembayaran;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;

class PermintaanPembayaranController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = PermintaanPembayaran::with(['proyek', 'kontraktor'])->latest();

        if ($user->isKontraktor()) {
            $query->where('kontraktor_id', $user->id);
            $proyeks = Proyek::where('kontraktor_id', $user->id)->get();
        } elseif ($user->isPPK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('ppk_id', $user->id);
            });
            $proyeks = collect();
        } elseif ($user->isKonsultan()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('konsultan_id', $user->id);
            });
            $proyeks = collect();
        } else {
            $proyeks = collect();
        }

        $pembayarans = $query->get();

        $sisaBisaDiajukan = 0;
        if ($user->isKontraktor() && $proyeks->isNotEmpty()) {
            $proyek = $proyeks->first();
            $totalDiajukan = PermintaanPembayaran::where('proyek_id', $proyek->id)
                ->whereIn('status', ['diajukan', 'diperiksa_konsultan', 'disetujui'])
                ->sum('nilai_tagihan');
            $sisaBisaDiajukan = $proyek->nilai_kontrak - $totalDiajukan;
        }

        return view('pembayaran.index', compact('pembayarans', 'proyeks', 'sisaBisaDiajukan'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isKontraktor()) abort(403);

        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'nilai_tagihan' => ['required', 'numeric', 'min:0'],
            'dokumen_pendukung' => 'required|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:5120',
            'lampiran_tambahan' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:5120',
        ], [
            'proyek_id.required' => 'Proyek wajib dipilih.',
            'dokumen_pendukung.required' => 'Surat / Dokumen pendukung wajib diunggah.',
            'dokumen_pendukung.max' => 'Ukuran file dokumen pendukung maksimal 5MB.'
        ]);

        $proyek = Proyek::find($request->proyek_id);
        
        $totalDiajukan = PermintaanPembayaran::where('proyek_id', $proyek->id)
            ->whereIn('status', ['diajukan', 'diperiksa_konsultan', 'disetujui'])
            ->sum('nilai_tagihan');
            
        $sisaBisaDiajukan = $proyek->nilai_kontrak - $totalDiajukan;

        $path = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $path = $request->file('dokumen_pendukung')->store('pembayaran_docs', 'public');
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran_tambahan')) {
            $lampiranPath = $request->file('lampiran_tambahan')->store('pembayaran_docs', 'public');
        }

        $pembayaranKe = PermintaanPembayaran::where('proyek_id', $request->proyek_id)->count() + 1;

        PermintaanPembayaran::create([
            'proyek_id' => $request->proyek_id,
            'kontraktor_id' => Auth::id(),
            'pembayaran_ke' => $pembayaranKe,
            'tanggal_pengajuan' => now(),
            'nilai_tagihan' => $request->nilai_tagihan,
            'dokumen_pendukung' => $path,
            'lampiran_tambahan' => $lampiranPath,
            'status' => 'diajukan',
        ]);

        return redirect()->route('permintaan-pembayaran.index')->with(['success' => 'Pembayaran berhasil diajukan.', 'currentMenu' => 'permintaan_pembayaran']);
    }

    public function verify(Request $request, PermintaanPembayaran $pembayaran)
    {
        if (!Auth::user()->isKonsultan()) abort(403);

        $pembayaran->update([
            'status' => 'diperiksa_konsultan',
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('permintaan-pembayaran.index')->with('success', 'Permintaan pembayaran diperiksa dan diteruskan ke PPK.');
    }

    public function rejectKonsultan(Request $request, PermintaanPembayaran $pembayaran)
    {
        if (!Auth::user()->isKonsultan()) abort(403);

        $pembayaran->update([
            'status' => 'ditolak',
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('permintaan-pembayaran.index')->with('error', 'Permintaan pembayaran dikembalikan ke kontraktor.');
    }

    public function approve(Request $request, PermintaanPembayaran $pembayaran)
    {
        if (!Auth::user()->isPPTK()) abort(403);

        $pembayaran->update([
            'status' => 'disetujui',
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('permintaan-pembayaran.index')->with('success', 'Permintaan Pembayaran disetujui.');
    }

    public function rejectPPTK(Request $request, PermintaanPembayaran $pembayaran)
    {
        if (!Auth::user()->isPPTK()) abort(403);

        $pembayaran->update([
            'status' => 'ditolak',
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('permintaan-pembayaran.index')->with('error', 'Permintaan Pembayaran ditolak.');
    }
}
