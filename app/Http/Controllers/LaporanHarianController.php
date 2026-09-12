<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Models\LaporanHarian;
use App\Models\TenagaKerja;
use App\Models\Material;
use App\Models\Peralatan;
use App\Models\RealisasiBiaya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaporanHarianController extends Controller
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
        
        $query = LaporanHarian::whereIn('proyek_id', $proyekIds)
            ->with(['kontraktor', 'proyek', 'verifiedBy']);

        if (Auth::user()->isKontraktor()) {
            $query->where('kontraktor_id', Auth::id());
        }

        $laporans = $query->latest('tanggal')->paginate(20);
        return view('laporan_harian.index', compact('laporans'));
    }

    public function create()
    {
        if (!Auth::user()->isKontraktor()) abort(403);
        $proyeks = $this->getAssignedProyeks();
        if ($proyeks->isEmpty()) {
            return redirect()->route('dashboard')->with('error', 'Anda belum ditugaskan ke proyek aktif manapun.');
        }
        return view('laporan_harian.create', compact('proyeks'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isKontraktor()) abort(403);

        $data = $request->all();
        
        if (isset($data['tenaga_kerja'])) {
            $data['tenaga_kerja'] = array_filter($data['tenaga_kerja'], fn($item) => (int)($item['jumlah'] ?? 0) > 0);
        }
        if (isset($data['material'])) {
            $data['material'] = array_filter($data['material'], fn($item) => !empty($item['jenis_material']));
        }
        if (isset($data['peralatan'])) {
            $data['peralatan'] = array_filter($data['peralatan'], fn($item) => !empty($item['jenis_alat']));
        }
        if (isset($data['realisasi'])) {
            $data['realisasi'] = array_filter($data['realisasi'], fn($item) => !empty($item['divisi_pekerjaan']));
        }
        $request->replace($data);

        $validated = $request->validate([
            'proyek_id'      => 'required|exists:proyeks,id',
            'tanggal'        => 'required|date',
            'kondisi_cuaca'  => 'required|string|in:cerah,berawan,hujan_ringan,hujan_lebat',
            'waktu_mulai'    => 'nullable|date_format:H:i',
            'waktu_selesai'  => 'nullable|date_format:H:i',
            'catatan'        => 'nullable|string',

            'tenaga_kerja'                => 'nullable|array',
            'tenaga_kerja.*.klasifikasi'  => 'required|string',
            'tenaga_kerja.*.keterangan'   => 'nullable|string',
            'tenaga_kerja.*.jumlah'       => 'required|integer|min:0',

            'material'                        => 'nullable|array',
            'material.*.jenis_material'       => 'required|string',
            'material.*.satuan'               => 'required|string',
            'material.*.kuantitas_datang'     => 'required|numeric|min:0',
            'material.*.kuantitas_digunakan'  => 'required|numeric|min:0',

            'peralatan'                 => 'nullable|array',
            'peralatan.*.jenis_alat'    => 'required|string',
            'peralatan.*.jumlah'        => 'required|integer|min:0',
            'peralatan.*.kondisi'       => 'required|in:baik,rusak_ringan,rusak_berat,tidak_beroperasi',
            'peralatan.*.jam_operasi'   => 'required|numeric|min:0',

            'realisasi'                        => 'nullable|array',
            'realisasi.*.divisi_pekerjaan'     => 'required|string',

            'dokumentasi' => 'nullable|array',
            'dokumentasi.*' => 'file|mimes:jpg,jpeg,png|max:10240',
            'lampiran_tambahan' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:10240',

            'action' => 'required|in:draft,submit',
        ]);

        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($validated['proyek_id'])) abort(403);

        $dokumentasiPaths = [];
        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $file) {
                $dokumentasiPaths[] = $file->store('laporan_harian_docs', 'public');
            }
        }

        $lampiranPath = null;
        if ($request->hasFile('lampiran_tambahan')) {
            $lampiranPath = $request->file('lampiran_tambahan')->store('laporan_harian_docs', 'public');
        }

        $waktuCuaca = null;
        if (!empty($validated['waktu_mulai']) && !empty($validated['waktu_selesai'])) {
            $waktuCuaca = $validated['waktu_mulai'] . ' - ' . $validated['waktu_selesai'];
        }

        DB::transaction(function () use ($validated, $dokumentasiPaths, $lampiranPath, $waktuCuaca) {
            $status = $validated['action'] === 'submit' ? 'submitted' : 'draft';

            $laporan = LaporanHarian::create([
                'proyek_id'      => $validated['proyek_id'],
                'kontraktor_id'  => Auth::id(),
                'tanggal'        => $validated['tanggal'],
                'kondisi_cuaca'  => $validated['kondisi_cuaca'],
                'waktu_cuaca'    => $waktuCuaca,
                'catatan'        => $validated['catatan'] ?? null,
                'dokumentasi'    => $dokumentasiPaths,
                'lampiran_tambahan' => $lampiranPath,
                'status'         => $status,
            ]);

            // Tenaga Kerja, dsb
            if (!empty($validated['tenaga_kerja'])) {
                foreach ($validated['tenaga_kerja'] as $tk) {
                    if ($tk['jumlah'] > 0) TenagaKerja::create(['laporan_harian_id' => $laporan->id, 'klasifikasi' => $tk['klasifikasi'], 'keterangan' => $tk['keterangan'] ?? null, 'jumlah' => $tk['jumlah']]);
                }
            }
            if (!empty($validated['material'])) {
                foreach ($validated['material'] as $mat) {
                    Material::create(['laporan_harian_id' => $laporan->id, 'jenis_material' => $mat['jenis_material'], 'satuan' => $mat['satuan'], 'kuantitas_datang' => $mat['kuantitas_datang'], 'kuantitas_digunakan' => $mat['kuantitas_digunakan']]);
                }
            }
            if (!empty($validated['peralatan'])) {
                foreach ($validated['peralatan'] as $per) {
                    Peralatan::create(['laporan_harian_id' => $laporan->id, 'jenis_alat' => $per['jenis_alat'], 'jumlah' => $per['jumlah'], 'kondisi' => $per['kondisi'], 'jam_operasi' => $per['jam_operasi']]);
                }
            }
            if (!empty($validated['realisasi'])) {
                foreach ($validated['realisasi'] as $rea) {
                    RealisasiBiaya::create(['laporan_harian_id' => $laporan->id, 'divisi_pekerjaan' => $rea['divisi_pekerjaan'], 'nilai_realisasi' => 0]);
                }
            }
        });

        $msg = $validated['action'] === 'submit' ? 'Laporan harian berhasil dikirim ke Konsultan Pengawas.' : 'Laporan harian disimpan sebagai draft.';
        return redirect()->route('kontraktor.dashboard')->with(['success' => $msg, 'currentMenu' => 'laporan']);
    }

    public function show(LaporanHarian $laporanHarian)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanHarian->proyek_id)) abort(403);
        if (Auth::user()->isKontraktor() && $laporanHarian->kontraktor_id !== Auth::id()) abort(403);

        $laporanHarian->load(['tenagaKerjas', 'materials', 'peralatans', 'realisasiBiayas', 'kontraktor', 'proyek', 'verifiedBy', 'approvedBy']);
        return view('laporan_harian.show', compact('laporanHarian'));
    }

    public function submit(LaporanHarian $laporanHarian)
    {
        if (!Auth::user()->isKontraktor() || $laporanHarian->kontraktor_id !== Auth::id()) abort(403);
        if (!in_array($laporanHarian->status, ['draft', 'rejected'])) return back()->with('error', 'Laporan ini tidak dapat dikirim ulang.');
        $laporanHarian->update([
            'status' => 'submitted',
            'is_read_konsultan' => false
        ]);
        return back()->with('success', 'Laporan berhasil dikirim ke Konsultan Pengawas.');
    }

    public function verify(Request $request, LaporanHarian $laporanHarian)
    {
        if (!Auth::user()->isKonsultan()) abort(403);
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanHarian->proyek_id)) abort(403);

        if ($laporanHarian->status !== 'submitted') return back()->with('error', 'Laporan ini tidak dalam status menunggu verifikasi.');

        $laporanHarian->update([
            'status'            => 'verified',
            'catatan_konsultan' => $request->catatan_konsultan,
            'verified_by'       => Auth::id(),
            'verified_at'       => now(),
            'is_read_pptk'      => false,
        ]);
        return redirect()->route('laporan-harian.index')->with('success', 'Laporan berhasil diverifikasi dan diteruskan ke PPK.');
    }

    public function rejectKonsultan(Request $request, LaporanHarian $laporanHarian)
    {
        if (!Auth::user()->isKonsultan()) abort(403);
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanHarian->proyek_id)) abort(403);

        $request->validate(['catatan_konsultan' => 'required|string']);

        $laporanHarian->update([
            'status'            => 'rejected',
            'catatan_konsultan' => $request->catatan_konsultan,
            'verified_by'       => Auth::id(),
            'verified_at'       => now(),
            'is_read_kontraktor'=> false,
        ]);
        return redirect()->route('laporan-harian.index')->with('success', 'Laporan dikembalikan ke Kontraktor untuk perbaikan.');
    }

    public function approve(Request $request, LaporanHarian $laporanHarian)
    {
        if (!Auth::user()->isPPTK()) abort(403);
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanHarian->proyek_id)) abort(403);

        if ($laporanHarian->status !== 'verified') return back()->with('error', 'Laporan ini belum diverifikasi oleh Konsultan.');

        $laporanHarian->update([
            'status'      => 'approved',
            'catatan_ppk' => $request->catatan_ppk,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'is_read_kontraktor'=> false,
            'is_read_konsultan' => false,
        ]);
        return redirect()->route('laporan-harian.index')->with('success', 'Laporan disetujui. Data bobot resmi masuk perhitungan kemajuan proyek.');
    }

    public function rejectPPTK(Request $request, LaporanHarian $laporanHarian)
    {
        if (!Auth::user()->isPPTK()) abort(403);
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanHarian->proyek_id)) abort(403);

        $request->validate(['catatan_ppk' => 'required|string']);

        $laporanHarian->update([
            'status'      => 'submitted', // Returns to Konsultan
            'catatan_ppk' => $request->catatan_ppk,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'is_read_konsultan' => false,
        ]);
        return redirect()->route('laporan-harian.index')->with('success', 'Laporan dikembalikan ke Konsultan untuk direvisi.');
    }

    public function markRead(LaporanHarian $laporanHarian)
    {
        $u = Auth::user();
        if ($u->isKontraktor()) $laporanHarian->update(['is_read_kontraktor' => true]);
        elseif ($u->isKonsultan()) $laporanHarian->update(['is_read_konsultan' => true]);
        elseif ($u->isPPTK()) $laporanHarian->update(['is_read_pptk' => true]);
        
        return response()->json(['success' => true]);
    }
}
