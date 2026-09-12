<?php

namespace App\Http\Controllers;

use App\Models\Proyek;
use App\Notifications\ProyekBaruNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Models\LaporanHarian;
use App\Models\LaporanMingguan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PPKController extends Controller
{
    private function getAssignedProyeks()
    {
        return Proyek::where('ppk_id', Auth::id())->get();
    }

    public function dashboard()
    {
        $proyeks   = $this->getAssignedProyeks();
        $proyekIds = $proyeks->pluck('id');

        // S-Curve data dari laporan mingguan yang sudah approved
        $sCurveData = [];
        foreach ($proyeks as $proyek) {
            $mingguans = LaporanMingguan::where('proyek_id', $proyek->id)
                ->where('status', 'approved')
                ->orderBy('minggu_ke')
                ->get(['minggu_ke', 'bobot_rencana', 'bobot_realisasi', 'deviasi']);

            $sCurveData[$proyek->id] = $mingguans;
        }

        $stats = [
            'total_proyek'      => $proyekIds->count(),
            'proyek_aktif'      => $proyeks->where('status', 'aktif')->count(),
            'total_user'        => User::count(),
            'menunggu_approval' => LaporanHarian::whereIn('proyek_id', $proyekIds)->where('status', 'verified')->count(),
            'sudah_diapprove'   => LaporanHarian::whereIn('proyek_id', $proyekIds)->where('status', 'approved')->count(),
            'total_kontraktor'  => User::where('role', 'kontraktor')->count(),
            'total_konsultan'   => User::where('role', 'konsultan')->count(),
            'total_ppk'         => User::where('role', 'ppk')->count(),
            'total_pptk'        => User::where('role', 'pptk')->count(),
        ];

        $laporanTerbaru = LaporanHarian::whereIn('proyek_id', $proyekIds)
            ->with(['kontraktor', 'proyek', 'verifiedBy'])
            ->latest('tanggal')
            ->take(10)
            ->get();

        return view('ppk.dashboard', compact('proyeks', 'stats', 'laporanTerbaru', 'sCurveData'));
    }

    public function laporanHarianIndex()
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        $laporans  = LaporanHarian::whereIn('proyek_id', $proyekIds)
            ->with(['kontraktor', 'proyek', 'verifiedBy'])
            ->latest('tanggal')
            ->paginate(20);
        return view('ppk.laporan_harian.index', compact('laporans'));
    }

    public function laporanHarianShow(LaporanHarian $laporanHarian)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanHarian->proyek_id)) {
            abort(403);
        }
        $laporanHarian->load(['tenagaKerjas', 'materials', 'peralatans', 'realisasiBiayas', 'kontraktor', 'proyek', 'verifiedBy']);
        return view('ppk.laporan_harian.show', compact('laporanHarian'));
    }

    public function approve(Request $request, LaporanHarian $laporanHarian)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanHarian->proyek_id)) {
            abort(403);
        }

        if ($laporanHarian->status !== 'verified') {
            return back()->with('error', 'Laporan ini belum diverifikasi oleh Konsultan.');
        }

        $validated = $request->validate([
            'catatan_ppk' => 'nullable|string',
        ]);

        $laporanHarian->update([
            'status'      => 'approved',
            'catatan_ppk' => $validated['catatan_ppk'] ?? null,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('ppk.laporan-harian.index')
            ->with('success', 'Laporan disetujui. Data bobot resmi masuk perhitungan kemajuan proyek.');
    }

    public function reject(Request $request, LaporanHarian $laporanHarian)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanHarian->proyek_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'catatan_ppk' => 'required|string',
        ]);

        $laporanHarian->update([
            'status'      => 'rejected',
            'catatan_ppk' => $validated['catatan_ppk'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('ppk.laporan-harian.index')
            ->with('success', 'Laporan dikembalikan untuk perbaikan.');
    }

    // LAPORAN MINGGUAN
    public function laporanMingguanIndex()
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        $laporans  = LaporanMingguan::whereIn('proyek_id', $proyekIds)
            ->with(['kontraktor', 'proyek'])
            ->latest()
            ->paginate(20);
        return view('ppk.laporan_mingguan.index', compact('laporans'));
    }

    public function approveMingguan(Request $request, LaporanMingguan $laporanMingguan)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanMingguan->proyek_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'catatan_ppk' => 'nullable|string',
        ]);

        $laporanMingguan->update([
            'status'      => 'approved',
            'catatan_ppk' => $validated['catatan_ppk'] ?? null,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('ppk.laporan-mingguan.index')
            ->with('success', 'Laporan mingguan disetujui.');
    }

    public function rejectMingguan(Request $request, LaporanMingguan $laporanMingguan)
    {
        $proyekIds = $this->getAssignedProyeks()->pluck('id');
        if (!$proyekIds->contains($laporanMingguan->proyek_id)) {
            abort(403);
        }

        $validated = $request->validate([
            'catatan_ppk' => 'required|string',
        ]);

        $laporanMingguan->update([
            'status'      => 'rejected',
            'catatan_ppk' => $validated['catatan_ppk'],
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('ppk.laporan-mingguan.index')
            ->with('success', 'Laporan mingguan dikembalikan.');
    }

    public function editTim(Proyek $proyek)
    {
        if ($proyek->ppk_id !== Auth::id()) {
            abort(403);
        }

        $kontraktors = User::where('role', 'kontraktor')->get();
        $konsultans  = User::where('role', 'konsultan')->get();

        return view('ppk.proyeks.edit_tim', compact('proyek', 'kontraktors', 'konsultans'));
    }

    public function updateTim(Request $request, Proyek $proyek)
    {
        if ($proyek->ppk_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'kontraktor_id' => 'required|exists:users,id',
            'konsultan_id'  => 'required|exists:users,id',
        ]);

        $proyek->update([
            'kontraktor_id' => $validated['kontraktor_id'],
            'konsultan_id'  => $validated['konsultan_id'],
        ]);

        return redirect()->route('ppk.dashboard')
            ->with('success', 'Tim Proyek (Kontraktor & Konsultan) berhasil ditentukan.');
    }

    // ==========================================
    // MANAJEMEN MASTER DATA (EX-ADMIN)
    // ==========================================

    // USER MANAGEMENT
    public function userIndex()
    {
        $users = User::latest()->paginate(15);
        return view('ppk.users.index', compact('users'));
    }

    public function userCreate()
    {
        return view('ppk.users.create');
    }

    public function userStore(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'email'            => 'required|email|unique:users,email',
            'password'         => 'required|string|min:8|confirmed',
            'role'             => 'required|in:kontraktor,konsultan,pptk',
            'penanggung_jawab' => 'nullable|string|max:255',
            'kab_kota'         => 'nullable|string|max:255',
        ], [
            'name.max' => 'Nama terlalu panjang, maksimal 100 karakter.',
        ]);

        $user = User::create([
            'name'             => $validated['name'],
            'email'            => $validated['email'],
            'password'         => Hash::make($validated['password']),
            'role'             => $validated['role'],
            'penanggung_jawab' => $validated['penanggung_jawab'] ?? null,
            'kab_kota'         => $validated['kab_kota'] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Akun pengguna berhasil dibuat.',
                'data' => $user
            ], 201);
        }

        return redirect()->route('ppk.users.index')
            ->with('success', 'Akun pengguna berhasil dibuat.');
    }

    public function userDestroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }
        $user->delete();
        return redirect()->route('ppk.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    // PROYEK MANAGEMENT
    public function proyekIndex()
    {
        // PPK hanya melihat proyeknya sendiri
        $proyeks = Proyek::where('ppk_id', Auth::id())
            ->with(['kontraktor', 'konsultan', 'pptk'])
            ->latest()
            ->paginate(15);
        return view('ppk.proyeks.index', compact('proyeks'));
    }

    public function proyekCreate()
    {
        $kontraktors = User::where('role', 'kontraktor')->get();
        $konsultans  = User::where('role', 'konsultan')->get();
        $pptks       = User::where('role', 'pptk')->get();
        
        return view('ppk.proyeks.create', compact('kontraktors', 'konsultans', 'pptks'));
    }

    public function proyekStore(Request $request)
    {
        if ($request->has('nilai_kontrak')) {
            $request->merge([
                'nilai_kontrak' => str_replace('.', '', $request->nilai_kontrak)
            ]);
        }

        $validated = $request->validate([
            'skpd'            => 'required|string|max:255',
            'kab_kota'        => 'required|string',
            'nama_proyek'     => 'required|string|max:255',
            'lokasi'          => 'required|string',
            'nilai_kontrak'   => 'required|numeric|min:0',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'kontraktor_id'   => 'nullable|exists:users,id',
            'konsultan_id'    => 'nullable|exists:users,id',
            'pptk_id'         => 'nullable|exists:users,id',
            'deskripsi'       => 'nullable|string',
        ]);

        // Otomatis assign PPK pembuat
        $validated['ppk_id'] = Auth::id();

        $proyek = Proyek::create($validated);

                // Kirim Notifikasi ke PPTK, Kontraktor, dan Konsultan
        $usersToNotify = User::whereIn('id', array_filter([
            $proyek->pptk_id,
            $proyek->kontraktor_id,
            $proyek->konsultan_id
        ]))->get();
        
        if($usersToNotify->isNotEmpty()) {
            \Illuminate\Support\Facades\Notification::send($usersToNotify, new \App\Notifications\ProyekBaruNotification($proyek));
        }

        return redirect()->route('ppk.proyeks.index')
            ->with('success', 'Proyek berhasil dibuat.');
    }

    public function proyekDestroy(Proyek $proyek)
    {
        if ($proyek->ppk_id !== Auth::id()) {
            abort(403);
        }
        
        $proyek->delete();
        return redirect()->route('ppk.proyeks.index')
            ->with('success', 'Proyek berhasil dihapus.');
    }
}
