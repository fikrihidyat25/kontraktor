<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Proyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_proyek'     => Proyek::count(),
            'proyek_aktif'     => Proyek::where('status', 'aktif')->count(),
            'total_user'       => User::count(),
            'total_kontraktor' => User::where('role', 'kontraktor')->count(),
            'total_konsultan'  => User::where('role', 'konsultan')->count(),
            'total_ppk'        => User::where('role', 'ppk')->count(),
        ];

        $proyeks = Proyek::with(['kontraktor', 'konsultan', 'ppk'])->latest()->get();
        $users   = User::whereNot('role', 'super_admin')->latest()->get();

        return view('super_admin.dashboard', compact('stats', 'proyeks', 'users'));
    }

    // USER MANAGEMENT
    public function userIndex()
    {
        $users = User::whereNot('role', 'super_admin')->latest()->paginate(15);
        return view('super_admin.users.index', compact('users'));
    }

    public function userCreate()
    {
        return view('super_admin.users.create');
    }

    public function userStore(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:kontraktor,konsultan,ppk',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun pengguna berhasil dibuat.');
    }

    public function userDestroy(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Tidak dapat menghapus Super Admin.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    // PROYEK MANAGEMENT
    public function proyekIndex()
    {
        $proyeks = Proyek::with(['kontraktor', 'konsultan', 'ppk'])->latest()->paginate(15);
        return view('super_admin.proyeks.index', compact('proyeks'));
    }

    public function proyekCreate()
    {
        $kontraktors = User::where('role', 'kontraktor')->get();
        $konsultans  = User::where('role', 'konsultan')->get();
        $ppks        = User::where('role', 'ppk')->get();
        return view('super_admin.proyeks.create', compact('kontraktors', 'konsultans', 'ppks'));
    }

    public function proyekStore(Request $request)
    {
        $validated = $request->validate([
            'nama_proyek'     => 'required|string|max:255',
            'nomor_kontrak'   => 'nullable|string|max:100',
            'lokasi'          => 'required|string',
            'nilai_kontrak'   => 'required|numeric|min:0',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'kontraktor_id'   => 'required|exists:users,id',
            'konsultan_id'    => 'required|exists:users,id',
            'ppk_id'          => 'required|exists:users,id',
            'deskripsi'       => 'nullable|string',
        ]);

        Proyek::create($validated);

        return redirect()->route('admin.proyeks.index')
            ->with('success', 'Proyek berhasil dibuat.');
    }

    public function proyekDestroy(Proyek $proyek)
    {
        $proyek->delete();
        return redirect()->route('admin.proyeks.index')
            ->with('success', 'Proyek berhasil dihapus.');
    }
}
