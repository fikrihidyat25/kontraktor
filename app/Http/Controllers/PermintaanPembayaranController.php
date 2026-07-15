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

        return view('pembayaran.index', compact('pembayarans', 'proyeks'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isKontraktor()) abort(403);

        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'nomor_tagihan' => 'required|string|max:255',
            'termin_ke' => 'required|integer|min:1',
            'nilai_tagihan' => 'required|numeric|min:0',
            'persentase_kemajuan' => 'required|numeric|min:0|max:100',
        ]);

        PermintaanPembayaran::create([
            'proyek_id' => $request->proyek_id,
            'kontraktor_id' => Auth::id(),
            'nomor_tagihan' => $request->nomor_tagihan,
            'tanggal_pengajuan' => now(),
            'termin_ke' => $request->termin_ke,
            'nilai_tagihan' => $request->nilai_tagihan,
            'persentase_kemajuan' => $request->persentase_kemajuan,
            'status' => 'diajukan',
        ]);

        return redirect()->route('permintaan-pembayaran.index')->with('success', 'Permintaan Pembayaran berhasil diajukan.');
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
        if (!Auth::user()->isPPK()) abort(403);

        $pembayaran->update([
            'status' => 'disetujui_ppk',
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('permintaan-pembayaran.index')->with('success', 'Permintaan Pembayaran disetujui.');
    }

    public function rejectPPK(Request $request, PermintaanPembayaran $pembayaran)
    {
        if (!Auth::user()->isPPK()) abort(403);

        $pembayaran->update([
            'status' => 'ditolak',
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('permintaan-pembayaran.index')->with('error', 'Permintaan Pembayaran ditolak.');
    }
}
