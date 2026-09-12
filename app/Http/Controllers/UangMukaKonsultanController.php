<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UangMukaKonsultan;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;

class UangMukaKonsultanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $query = UangMukaKonsultan::with(['proyek', 'konsultan', 'approver'])->latest();

        if ($user->isKonsultan()) {
            $query->where('konsultan_id', $user->id);
            $proyeks = Proyek::where('konsultan_id', $user->id)->get();
        } elseif ($user->isPPK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('ppk_id', $user->id);
            });
            $proyeks = collect();
        } else {
            // PPTK or others can view if they are part of the project
            $proyeks = collect();
            if ($user->isPPTK()) {
                $query->whereHas('proyek', function ($q) use ($user) {
                    $q->where('pptk_id', $user->id);
                });
            }
        }

        $uangMuka = $query->get();

        return view('uang_muka_konsultan.index', compact('uangMuka', 'proyeks'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isKonsultan()) abort(403, 'Hanya konsultan yang dapat mengajukan uang muka konsultan.');

        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'nilai_pengajuan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'dokumen_lampiran' => 'nullable|array',
            'dokumen_lampiran.*' => 'file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:5120',
        ]);

        $lampiranPaths = [];
        if ($request->hasFile('dokumen_lampiran')) {
            foreach ($request->file('dokumen_lampiran') as $file) {
                $lampiranPaths[] = $file->store('uang_muka_konsultan_docs', 'public');
            }
        }

        UangMukaKonsultan::create([
            'proyek_id' => $request->proyek_id,
            'konsultan_id' => Auth::id(),
            'nilai_pengajuan' => $request->nilai_pengajuan,
            'keterangan' => $request->keterangan,
            'dokumen_lampiran' => empty($lampiranPaths) ? null : $lampiranPaths,
            'status' => 'diajukan',
        ]);

        return redirect()->route('uang-muka-konsultan.index')->with('success', 'Uang Muka Konsultan berhasil diajukan.');
    }

    public function approve(Request $request, UangMukaKonsultan $uangMukaKonsultan)
    {
        if (!Auth::user()->isPPTK()) abort(403);

        $uangMukaKonsultan->update([
            'status' => 'disetujui',
            'catatan_ppk' => $request->catatan,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('uang-muka-konsultan.index')->with('success', 'Pengajuan Uang Muka Konsultan disetujui.');
    }

    public function reject(Request $request, UangMukaKonsultan $uangMukaKonsultan)
    {
        if (!Auth::user()->isPPTK()) abort(403);

        $uangMukaKonsultan->update([
            'status' => 'ditolak',
            'catatan_ppk' => $request->catatan,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('uang-muka-konsultan.index')->with('error', 'Pengajuan Uang Muka Konsultan ditolak.');
    }
}
