<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UangMuka;
use App\Models\Proyek;

class UangMukaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $query = UangMuka::with(['proyek', 'kontraktor'])->latest();

        // RBAC filtering
        if ($user->isKontraktor()) {
            $query->where('kontraktor_id', $user->id);
            $proyeks = Proyek::where('kontraktor_id', $user->id)->get();
        } elseif ($user->isPPK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('ppk_id', $user->id);
            });
            $proyeks = collect(); // Not needed for PPK creation
        } elseif ($user->isKonsultan()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('konsultan_id', $user->id);
            });
            $proyeks = collect();
        } else {
            $proyeks = collect();
        }

        $uangMukas = $query->get();

        return view('uang_muka.index', compact('uangMukas', 'proyeks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'nilai_pengajuan' => 'required|numeric|min:1',
            'dokumen_pendukung' => 'required|file|mimes:pdf,doc,docx,zip,rar|max:10240',
        ]);

        $path = null;
        if ($request->hasFile('dokumen_pendukung')) {
            $path = $request->file('dokumen_pendukung')->store('uang_muka_docs', 'public');
        }

        UangMuka::create([
            'proyek_id' => $request->proyek_id,
            'kontraktor_id' => auth()->id(),
            'tanggal_pengajuan' => now(),
            'nilai_pengajuan' => $request->nilai_pengajuan,
            'dokumen_pendukung' => $path,
            'status' => 'menunggu_persetujuan',
        ]);

        if (auth()->user()->isKontraktor()) {
            return redirect()->route('kontraktor.dashboard')->with(['success' => 'Pengajuan uang muka berhasil dibuat.', 'currentMenu' => 'uang_muka']);
        }
        return redirect()->route('uang-muka.index')->with('success', 'Pengajuan uang muka berhasil dibuat.');
    }

    public function approve(Request $request, UangMuka $uangMuka)
    {
        if (!auth()->user()->isPPK()) {
            abort(403);
        }

        $uangMuka->update([
            'status' => 'disetujui',
            'catatan_ppk' => $request->catatan_ppk,
        ]);

        return redirect()->route('uang-muka.index')->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function reject(Request $request, UangMuka $uangMuka)
    {
        if (!auth()->user()->isPPK()) {
            abort(403);
        }

        $uangMuka->update([
            'status' => 'ditolak',
            'catatan_ppk' => $request->catatan_ppk,
        ]);

        return redirect()->route('uang-muka.index')->with('error', 'Pengajuan telah ditolak.');
    }
}
