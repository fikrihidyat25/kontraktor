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
        $admin = User::create([
            'name'     => 'Administrator DIPRODA',
            'email'    => 'admin@diproda.test',
            'password' => Hash::make('password'),
            'role'     => 'super_admin',
        ]);

        $kontraktor = User::create([
            'name'     => 'PT Maju Jaya Konstruksi',
            'email'    => 'kontraktor@diproda.test',
            'password' => Hash::make('password'),
            'role'     => 'kontraktor',
        ]);

        $konsultan = User::create([
            'name'     => 'CV Andalan Konsultan Pengawas',
            'email'    => 'konsultan@diproda.test',
            'password' => Hash::make('password'),
            'role'     => 'konsultan',
        ]);

        $ppk = User::create([
            'name'     => 'Ir. Budi Santoso, M.T. (PPK)',
            'email'    => 'ppk@diproda.test',
            'password' => Hash::make('password'),
            'role'     => 'ppk',
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
            'status'          => 'aktif',
            'deskripsi'       => 'Pekerjaan peningkatan struktur perkerasan jalan nasional Padang—Solok Segmen III sepanjang 12,5 km, mencakup pekerjaan tanah, perkerasan berbutir, perkerasan aspal, dan pekerjaan drainase.',
        ]);

        // ── 3. BUAT LAPORAN HARIAN SAMPLE ────────────────────────────────
        // Laporan 1: Status APPROVED
        $l1 = LaporanHarian::create([
            'proyek_id'        => $proyek->id,
            'kontraktor_id'    => $kontraktor->id,
            'tanggal'          => '2026-05-26',
            'kondisi_cuaca'    => 'cerah',
            'catatan'          => 'Pekerjaan galian tanah berjalan lancar. Produksi melebihi target harian.',
            'status'           => 'approved',
            'catatan_konsultan'=> 'Data lapangan sesuai hasil pengukuran. Volume galian terverifikasi.',
            'verified_by'      => $konsultan->id,
            'verified_at'      => now()->subDays(4),
            'catatan_ppk'      => 'Disetujui. Progress sesuai jadwal.',
            'approved_by'      => $ppk->id,
            'approved_at'      => now()->subDays(3),
        ]);
        TenagaKerja::insert([
            ['laporan_harian_id'=>$l1->id,'klasifikasi'=>'mandor','keterangan'=>'Mandor Galian','jumlah'=>2,'created_at'=>now(),'updated_at'=>now()],
            ['laporan_harian_id'=>$l1->id,'klasifikasi'=>'tukang','keterangan'=>null,'jumlah'=>12,'created_at'=>now(),'updated_at'=>now()],
            ['laporan_harian_id'=>$l1->id,'klasifikasi'=>'pembantu_tukang','keterangan'=>null,'jumlah'=>20,'created_at'=>now(),'updated_at'=>now()],
        ]);
        Material::insert([
            ['laporan_harian_id'=>$l1->id,'jenis_material'=>'Batu Kali','satuan'=>'m³','kuantitas_datang'=>80,'kuantitas_digunakan'=>75,'keterangan'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['laporan_harian_id'=>$l1->id,'jenis_material'=>'Pasir Urug','satuan'=>'m³','kuantitas_datang'=>40,'kuantitas_digunakan'=>38,'keterangan'=>null,'created_at'=>now(),'updated_at'=>now()],
        ]);
        Peralatan::insert([
            ['laporan_harian_id'=>$l1->id,'jenis_alat'=>'Excavator PC200','jumlah'=>2,'kondisi'=>'baik','jam_operasi'=>14.5,'keterangan'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['laporan_harian_id'=>$l1->id,'jenis_alat'=>'Dump Truck 8 Ton','jumlah'=>5,'kondisi'=>'baik','jam_operasi'=>60,'keterangan'=>null,'created_at'=>now(),'updated_at'=>now()],
        ]);
        RealisasiBiaya::insert([
            ['laporan_harian_id'=>$l1->id,'divisi_pekerjaan'=>'Divisi 3 - Pekerjaan Tanah','nilai_realisasi'=>45_000_000,'bobot_fisik'=>0.36,'keterangan'=>null,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // Laporan 2: Status VERIFIED (menunggu approval PPK)
        $l2 = LaporanHarian::create([
            'proyek_id'        => $proyek->id,
            'kontraktor_id'    => $kontraktor->id,
            'tanggal'          => '2026-05-27',
            'kondisi_cuaca'    => 'berawan',
            'catatan'          => 'Pekerjaan lanjutan galian sub-grade. Ditemukan lapisan tanah lempung lunak di STA 3+200.',
            'status'           => 'verified',
            'catatan_konsultan'=> 'Lapisan lempung lunak STA 3+200 perlu perbaikan tanah dasar. Data volume sudah sesuai.',
            'verified_by'      => $konsultan->id,
            'verified_at'      => now()->subDays(2),
        ]);
        TenagaKerja::insert([
            ['laporan_harian_id'=>$l2->id,'klasifikasi'=>'mandor','keterangan'=>null,'jumlah'=>2,'created_at'=>now(),'updated_at'=>now()],
            ['laporan_harian_id'=>$l2->id,'klasifikasi'=>'tukang','keterangan'=>null,'jumlah'=>10,'created_at'=>now(),'updated_at'=>now()],
            ['laporan_harian_id'=>$l2->id,'klasifikasi'=>'pembantu_tukang','keterangan'=>null,'jumlah'=>18,'created_at'=>now(),'updated_at'=>now()],
        ]);
        Material::insert([
            ['laporan_harian_id'=>$l2->id,'jenis_material'=>'Batu Pecah 2/3','satuan'=>'m³','kuantitas_datang'=>60,'kuantitas_digunakan'=>55,'keterangan'=>null,'created_at'=>now(),'updated_at'=>now()],
        ]);
        Peralatan::insert([
            ['laporan_harian_id'=>$l2->id,'jenis_alat'=>'Excavator PC200','jumlah'=>2,'kondisi'=>'baik','jam_operasi'=>13,'keterangan'=>null,'created_at'=>now(),'updated_at'=>now()],
            ['laporan_harian_id'=>$l2->id,'jenis_alat'=>'Motor Grader','jumlah'=>1,'kondisi'=>'baik','jam_operasi'=>7,'keterangan'=>null,'created_at'=>now(),'updated_at'=>now()],
        ]);
        RealisasiBiaya::insert([
            ['laporan_harian_id'=>$l2->id,'divisi_pekerjaan'=>'Divisi 3 - Pekerjaan Tanah','nilai_realisasi'=>38_500_000,'bobot_fisik'=>0.31,'keterangan'=>null,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // Laporan 3: Status SUBMITTED (menunggu Konsultan)
        $l3 = LaporanHarian::create([
            'proyek_id'     => $proyek->id,
            'kontraktor_id' => $kontraktor->id,
            'tanggal'       => '2026-05-29',
            'kondisi_cuaca' => 'hujan_ringan',
            'catatan'       => 'Pekerjaan sempat berhenti 2 jam akibat hujan ringan. Produksi dibawah target hari ini.',
            'status'        => 'submitted',
        ]);
        TenagaKerja::create(['laporan_harian_id'=>$l3->id,'klasifikasi'=>'mandor','jumlah'=>1]);
        TenagaKerja::create(['laporan_harian_id'=>$l3->id,'klasifikasi'=>'tukang','jumlah'=>8]);
        Material::create(['laporan_harian_id'=>$l3->id,'jenis_material'=>'Sirtu Pilahan','satuan'=>'m³','kuantitas_datang'=>30,'kuantitas_digunakan'=>28]);
        Peralatan::create(['laporan_harian_id'=>$l3->id,'jenis_alat'=>'Vibro Compactor','jumlah'=>1,'kondisi'=>'baik','jam_operasi'=>5.5]);
        RealisasiBiaya::create(['laporan_harian_id'=>$l3->id,'divisi_pekerjaan'=>'Divisi 4 - Perkerasan Berbutir','nilai_realisasi'=>22_000_000,'bobot_fisik'=>0.18]);

        // Laporan 4: Draft
        LaporanHarian::create([
            'proyek_id'     => $proyek->id,
            'kontraktor_id' => $kontraktor->id,
            'tanggal'       => '2026-05-30',
            'kondisi_cuaca' => 'cerah',
            'catatan'       => 'Draft laporan — belum dikirim.',
            'status'        => 'draft',
        ]);

        // ── 4. LAPORAN MINGGUAN SAMPLE (S-CURVE DATA) ─────────────────────
        $mingguanData = [
            ['minggu_ke'=>1, 'mulai'=>'2026-01-06', 'selesai'=>'2026-01-10', 'rencana'=>1.50, 'realisasi'=>1.45, 'status'=>'approved'],
            ['minggu_ke'=>2, 'mulai'=>'2026-01-13', 'selesai'=>'2026-01-17', 'rencana'=>3.20, 'realisasi'=>3.10, 'status'=>'approved'],
            ['minggu_ke'=>3, 'mulai'=>'2026-01-20', 'selesai'=>'2026-01-24', 'rencana'=>5.00, 'realisasi'=>4.75, 'status'=>'approved'],
            ['minggu_ke'=>4, 'mulai'=>'2026-01-27', 'selesai'=>'2026-01-31', 'rencana'=>7.10, 'realisasi'=>6.80, 'status'=>'approved'],
            ['minggu_ke'=>5, 'mulai'=>'2026-02-03', 'selesai'=>'2026-02-07', 'rencana'=>9.50, 'realisasi'=>9.20, 'status'=>'approved'],
            ['minggu_ke'=>6, 'mulai'=>'2026-02-10', 'selesai'=>'2026-02-14', 'rencana'=>12.00, 'realisasi'=>11.50, 'status'=>'approved'],
            ['minggu_ke'=>7, 'mulai'=>'2026-02-17', 'selesai'=>'2026-02-21', 'rencana'=>15.00, 'realisasi'=>14.20, 'status'=>'approved'],
            ['minggu_ke'=>8, 'mulai'=>'2026-02-24', 'selesai'=>'2026-02-28', 'rencana'=>18.50, 'realisasi'=>17.80, 'status'=>'approved'],
            ['minggu_ke'=>9, 'mulai'=>'2026-03-03', 'selesai'=>'2026-03-07', 'rencana'=>22.00, 'realisasi'=>21.00, 'status'=>'approved'],
            ['minggu_ke'=>10,'mulai'=>'2026-03-10', 'selesai'=>'2026-03-14', 'rencana'=>26.00, 'realisasi'=>24.50, 'status'=>'approved'],
            ['minggu_ke'=>11,'mulai'=>'2026-03-17', 'selesai'=>'2026-03-21', 'rencana'=>30.00, 'realisasi'=>28.20, 'status'=>'approved'],
            ['minggu_ke'=>12,'mulai'=>'2026-03-24', 'selesai'=>'2026-03-28', 'rencana'=>34.50, 'realisasi'=>32.10, 'status'=>'approved'],
            ['minggu_ke'=>13,'mulai'=>'2026-03-31', 'selesai'=>'2026-04-04', 'rencana'=>39.00, 'realisasi'=>36.80, 'status'=>'approved'],
            ['minggu_ke'=>14,'mulai'=>'2026-04-07', 'selesai'=>'2026-04-11', 'rencana'=>43.50, 'realisasi'=>41.50, 'status'=>'approved'],
            ['minggu_ke'=>15,'mulai'=>'2026-04-14', 'selesai'=>'2026-04-18', 'rencana'=>48.00, 'realisasi'=>46.20, 'status'=>'approved'],
            ['minggu_ke'=>16,'mulai'=>'2026-04-21', 'selesai'=>'2026-04-25', 'rencana'=>52.50, 'realisasi'=>50.80, 'status'=>'approved'],
            ['minggu_ke'=>17,'mulai'=>'2026-04-28', 'selesai'=>'2026-05-02', 'rencana'=>57.00, 'realisasi'=>55.50, 'status'=>'approved'],
            ['minggu_ke'=>18,'mulai'=>'2026-05-05', 'selesai'=>'2026-05-09', 'rencana'=>61.50, 'realisasi'=>60.20, 'status'=>'approved'],
            ['minggu_ke'=>19,'mulai'=>'2026-05-12', 'selesai'=>'2026-05-16', 'rencana'=>66.00, 'realisasi'=>65.00, 'status'=>'approved'],
            ['minggu_ke'=>20,'mulai'=>'2026-05-19', 'selesai'=>'2026-05-23', 'rencana'=>70.50, 'realisasi'=>69.80, 'status'=>'approved'],
            ['minggu_ke'=>21,'mulai'=>'2026-05-26', 'selesai'=>'2026-05-30', 'rencana'=>75.00, 'realisasi'=>73.50, 'status'=>'verified'],
        ];

        foreach ($mingguanData as $m) {
            $deviasi = $m['realisasi'] - $m['rencana'];
            LaporanMingguan::create([
                'proyek_id'          => $proyek->id,
                'kontraktor_id'      => $kontraktor->id,
                'minggu_ke'          => $m['minggu_ke'],
                'tanggal_mulai'      => $m['mulai'],
                'tanggal_selesai'    => $m['selesai'],
                'bobot_rencana'      => $m['rencana'],
                'bobot_realisasi'    => $m['realisasi'],
                'deviasi'            => $deviasi,
                'ringkasan_kemajuan' => 'Pekerjaan berlangsung sesuai jadwal minggu ini.',
                'kendala'            => $deviasi < -2 ? 'Terdapat keterlambatan akibat cuaca dan ketersediaan material.' : null,
                'status'             => $m['status'],
                'catatan_konsultan'  => $m['status'] !== 'draft' ? 'Data S-Curve sesuai hasil pengukuran lapangan.' : null,
                'verified_by'        => $m['status'] !== 'draft' ? $konsultan->id : null,
                'verified_at'        => $m['status'] !== 'draft' ? now()->subDays(rand(1, 3)) : null,
                'approved_by'        => $m['status'] === 'approved' ? $ppk->id : null,
                'approved_at'        => $m['status'] === 'approved' ? now()->subDays(rand(1, 2)) : null,
            ]);
        }
    }
}
