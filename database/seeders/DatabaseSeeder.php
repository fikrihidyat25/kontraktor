<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Proyek;
use App\Models\LaporanHarian;
use App\Models\LaporanMingguan;
use App\Models\TenagaKerja;
use App\Models\Material;
use App\Models\Peralatan;
use App\Models\RealisasiBiaya;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. BUAT AKUN ──────────────────────────────────────────────────
        
        $ppk = User::create([
            'name'     => 'Ir. Budi Santoso, M.T. (PPK)',
            'email'    => 'ppk@diproda.test',
            'password' => Hash::make('password'),
            'role'     => 'ppk',
        ]);

        /*
        // Data di bawah ini dinonaktifkan atas permintaan user (Hanya PPK saja)
        $kontraktor = User::create([
            'name'     => 'PT Maju Jaya Konstruksi',
            'email'    => 'kontraktor@digitaproda.test',
            'password' => Hash::make('password'),
            'role'     => 'kontraktor',
        ]);

        $konsultan = User::create([
            'name'     => 'CV Andalan Konsultan Pengawas',
            'email'    => 'konsultan@digitaproda.test',
            'password' => Hash::make('password'),
            'role'     => 'konsultan',
        ]);

        $pptk = User::create([
            'name'     => 'Bapak PPTK',
            'email'    => 'pptk@digitaproda.test',
            'password' => Hash::make('password'),
            'role'     => 'pptk',
        ]);

        // ── 2. BUAT PROYEK ────────────────────────────────────────────────
        $proyek = Proyek::create([
            'nama_proyek'     => 'Peningkatan Jalan Ruas Padang—Solok, Segmen III',
            'nomor_kontrak'   => 'HK.02.01/PPK.III/BJSM/2026/001',
            'lokasi'          => 'Kabupaten Solok, Sumatera Barat',
            'nilai_kontrak'   => 12_500_000_000,
            'tanggal_mulai'   => '2026-01-06',
            'tanggal_selesai' => '2026-12-18',
            'kontraktor_id'   => $kontraktor->id,
            'konsultan_id'    => $konsultan->id,
            'ppk_id'          => $ppk->id,
            'pptk_id'         => $pptk->id,
            'status'          => 'aktif',
            'deskripsi'       => 'Pekerjaan peningkatan struktur perkerasan jalan nasional Padang—Solok Segmen III sepanjang 12,5 km, mencakup pekerjaan tanah, perkerasan berbutir, perkerasan aspal, dan pekerjaan drainase.',
        ]);
        
        ... (Data Laporan Harian dan Mingguan tidak dibuat agar database kosong) ...
        */
    }
}
