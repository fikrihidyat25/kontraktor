<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UangMuka;
use App\Models\Proyek;
use App\Models\DokumenProyek;

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
        } elseif ($user->isPPTK()) {
            $query->whereHas('proyek', function ($q) use ($user) {
                $q->where('pptk_id', $user->id);
            });
            $proyeks = collect();
        } else {
            $proyeks = collect();
        }

        $uangMukas = $query->get();

        $sisaBisaDiajukan = 0;
        $canSubmit = false;
        if ($user->isKontraktor() && $proyeks->isNotEmpty()) {
            $proyek = $proyeks->first();
            $totalDiajukan = UangMuka::where('proyek_id', $proyek->id)
                ->whereIn('status', ['menunggu_persetujuan', 'disetujui'])
                ->sum('nilai_pengajuan');
            $sisaBisaDiajukan = $proyek->nilai_kontrak - $totalDiajukan;

            $existingActive = UangMuka::where('proyek_id', $proyek->id)
                ->whereIn('status', ['menunggu_persetujuan', 'disetujui'])
                ->exists();
            $canSubmit = !$existingActive;
        }

        return view('uang_muka.index', compact('uangMukas', 'proyeks', 'sisaBisaDiajukan', 'canSubmit'));
    }

    public function store(Request $request)
    {
        $proyek = Proyek::findOrFail($request->proyek_id);
        
        $existingActive = UangMuka::where('proyek_id', $request->proyek_id)
            ->whereIn('status', ['menunggu_persetujuan', 'disetujui'])
            ->exists();

        if ($existingActive) {
            return redirect()->back()->with('error', 'Pengajuan uang muka sedang diproses atau sudah disetujui. Anda tidak dapat mengajukan lagi.');
        }

        $spmkExists = DokumenProyek::where('proyek_id', $request->proyek_id)
            ->where('tipe_dokumen', 'SPMK')
            ->exists();

        if (!$spmkExists) {
            return redirect()->back()->with('error', 'Tidak dapat mengajukan Uang Muka. Dokumen SPMK belum diunggah oleh PPK.');
        }

        $request->validate([
            'proyek_id' => 'required|exists:proyeks,id',
            'nilai_pengajuan' => ['required', 'numeric', 'min:1'],
            'surat_permohonan' => 'required|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:10240',
            'lampiran' => 'nullable|array',
            'lampiran.*' => 'file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:10240',
        ], [
            'proyek_id.required' => 'Silakan pilih proyek.',
            'nilai_pengajuan.required' => 'Nilai pengajuan uang muka wajib diisi.',
            'nilai_pengajuan.numeric' => 'Nilai pengajuan hanya boleh berisi angka tanpa titik atau koma (contoh: 15000000).',
            'nilai_pengajuan.min' => 'Nilai pengajuan minimal 1.',
            'surat_permohonan.required' => 'Surat permohonan wajib diunggah.',
            'surat_permohonan.mimes' => 'Format surat permohonan harus berupa PDF, Word, atau Gambar.',
            'surat_permohonan.max' => 'Ukuran file surat permohonan maksimal 10MB.',
            'lampiran.*.mimes' => 'Format lampiran tidak valid.',
            'lampiran.*.max' => 'Satu atau lebih file lampiran melebihi 10MB.'
        ]);

        $suratPath = null;
        if ($request->hasFile('surat_permohonan')) {
            $suratPath = $request->file('surat_permohonan')->store('uang_muka_docs', 'public');
        }

        $lampiranPaths = [];
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $lampiranPaths[] = $file->store('uang_muka_docs', 'public');
            }
        }

        UangMuka::create([
            'proyek_id' => $request->proyek_id,
            'kontraktor_id' => auth()->id(),
            'tanggal_pengajuan' => now(),
            'nilai_pengajuan' => $request->nilai_pengajuan,
            'surat_permohonan' => $suratPath,
            'lampiran' => empty($lampiranPaths) ? null : $lampiranPaths,
            'status' => 'menunggu_persetujuan',
        ]);

        if (auth()->user()->isKontraktor()) {
            return redirect()->route('kontraktor.dashboard')->with(['success' => 'Pengajuan uang muka berhasil dibuat.', 'currentMenu' => 'uang_muka']);
        }
        return redirect()->route('uang-muka.index')->with('success', 'Pengajuan uang muka berhasil dibuat.');
    }

    public function approve(Request $request, UangMuka $uangMuka)
    {
        if (!auth()->user()->isPPTK()) {
            abort(403);
        }

        if ($request->filled('nomor_kontrak')) {
            $uangMuka->proyek->update(['nomor_kontrak' => $request->nomor_kontrak]);
        }

        $uangMuka->update([
            'status' => 'disetujui',
            'catatan_ppk' => $request->catatan_ppk,
        ]);

        return redirect()->route('uang-muka.index')->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function reject(Request $request, UangMuka $uangMuka)
    {
        if (!auth()->user()->isPPTK()) {
            abort(403);
        }

        if ($request->filled('nomor_kontrak')) {
            $uangMuka->proyek->update(['nomor_kontrak' => $request->nomor_kontrak]);
        }

        $uangMuka->update([
            'status' => 'ditolak',
            'catatan_ppk' => $request->catatan_ppk,
        ]);

        return redirect()->route('uang-muka.index')->with('error', 'Pengajuan telah ditolak.');
    }
}
