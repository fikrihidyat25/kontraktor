<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PembayaranKonsultan;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;

class PembayaranKonsultanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = PembayaranKonsultan::with(['proyek', 'konsultan', 'verifier', 'approver'])->latest();

        if ($user->isKonsultan()) {
            $query->where('konsultan_id', $user->id);
            $proyeks = Proyek::where('konsultan_id', $user->id)->get();
        } elseif ($user->isPPK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('ppk_id', $user->id);
            });
            $proyeks = collect();
        } elseif ($user->isPPTK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('pptk_id', $user->id);
            });
            $proyeks = collect();
        } else {
            $proyeks = collect();
        }

        $pembayarans = $query->get();

        return view('pembayaran_konsultan.index', compact('pembayarans', 'proyeks'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isKonsultan()) abort(403);

        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'termin' => 'required|string|max:255',
            'nilai_pembayaran' => 'required|numeric|min:0',
            'surat_permohonan' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'laporan_kemajuan' => 'required|file|mimes:pdf,doc,docx,zip,rar|max:10240',
            'lampiran_lainnya' => 'nullable|array',
            'lampiran_lainnya.*' => 'file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:5120',
        ]);

        $suratPath = $request->file('surat_permohonan')->store('pembayaran_konsultan_docs', 'public');
        $laporanPath = $request->file('laporan_kemajuan')->store('pembayaran_konsultan_docs', 'public');

        $lampiranPaths = [];
        if ($request->hasFile('lampiran_lainnya')) {
            foreach ($request->file('lampiran_lainnya') as $file) {
                $lampiranPaths[] = $file->store('pembayaran_konsultan_docs', 'public');
            }
        }

        PembayaranKonsultan::create([
            'proyek_id' => $request->proyek_id,
            'konsultan_id' => Auth::id(),
            'termin' => $request->termin,
            'nilai_pembayaran' => $request->nilai_pembayaran,
            'surat_permohonan' => $suratPath,
            'laporan_kemajuan' => $laporanPath,
            'lampiran_lainnya' => empty($lampiranPaths) ? null : $lampiranPaths,
            'status' => 'diajukan',
        ]);

        return redirect()->route('pembayaran-konsultan.index')->with('success', 'Permintaan Pembayaran Konsultan berhasil diajukan.');
    }

    public function approve(Request $request, PembayaranKonsultan $pembayaranKonsultan)
    {
        if (!Auth::user()->isPPTK()) abort(403);

        $pembayaranKonsultan->update([
            'status' => 'disetujui',
            'catatan_pptk' => $request->catatan_pptk,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('pembayaran-konsultan.index')->with('success', 'Pembayaran Konsultan disetujui.');
    }

    public function rejectPPTK(Request $request, PembayaranKonsultan $pembayaranKonsultan)
    {
        if (!Auth::user()->isPPTK()) abort(403);

        $pembayaranKonsultan->update([
            'status' => 'ditolak',
            'catatan_pptk' => $request->catatan_pptk,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        return redirect()->route('pembayaran-konsultan.index')->with('error', 'Pembayaran Konsultan ditolak oleh PPTK.');
    }


}
