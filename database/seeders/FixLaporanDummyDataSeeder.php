<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyek;
use App\Models\LaporanHarian;
use App\Models\LaporanMingguan;
use App\Models\LaporanBulanan;
use App\Models\TenagaKerja;
use App\Models\Material;
use App\Models\Peralatan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FixLaporanDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kosongkan data Laporan beserta relasinya
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('tenaga_kerjas')->truncate();
        DB::table('materials')->truncate();
        DB::table('peralatans')->truncate();
        DB::table('realisasi_biayas')->truncate();
        DB::table('laporan_harians')->truncate();
        DB::table('laporan_mingguans')->truncate();
        DB::table('laporan_bulanans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Ambil semua proyek (harus ada 5)
        $proyeks = Proyek::all();

        foreach ($proyeks as $index => $proyek) {
            $kontraktorId = $proyek->kontraktor_id;
            
            // Tentukan status berdasarkan urutan proyek (index 0 dan 1 -> submitted, sisanya -> approved)
            $status = ($index < 2) ? 'submitted' : 'approved';

            // ==========================================
            // 1 Data Laporan Harian
            // ==========================================
            $harian = LaporanHarian::create([
                'proyek_id'     => $proyek->id,
                'kontraktor_id' => $kontraktorId,
                'tanggal'       => Carbon::now()->subDays(2)->toDateString(),
                'kondisi_cuaca' => 'cerah',
                'waktu_cuaca'   => '08:00 - 16:00',
                'catatan'       => 'Pekerjaan berjalan lancar (' . $status . ')',
                'dokumentasi'   => null,
                'status'        => $status,
                'verified_by'   => $status === 'approved' ? $proyek->konsultan_id : null,
                'approved_by'   => $status === 'approved' ? $proyek->ppk_id : null,
            ]);

            TenagaKerja::create(['laporan_harian_id' => $harian->id, 'klasifikasi' => 'Tukang', 'keterangan' => 'Pekerja utama', 'jumlah' => 10]);
            Material::create(['laporan_harian_id' => $harian->id, 'jenis_material' => 'Semen', 'satuan' => 'Zak', 'kuantitas_datang' => 50, 'kuantitas_digunakan' => 20]);
            Peralatan::create(['laporan_harian_id' => $harian->id, 'jenis_alat' => 'Excavator', 'jumlah' => 1, 'kondisi' => 'Baik', 'jam_operasi' => 8]);

            // ==========================================
            // 1 Data Laporan Mingguan (Selalu 'approved' agar S-Curve muncul di semua proyek)
            // ==========================================
            $tglMulai   = Carbon::now()->subWeeks(1)->startOfWeek();
            $tglSelesai = $tglMulai->copy()->endOfWeek();
            $rencana    = 10;
            $realisasi  = 12;
            
            LaporanMingguan::create([
                'proyek_id'         => $proyek->id,
                'kontraktor_id'     => $kontraktorId,
                'minggu_ke'         => 1,
                'tanggal_mulai'     => $tglMulai->toDateString(),
                'tanggal_selesai'   => $tglSelesai->toDateString(),
                'bobot_rencana'     => $rencana,
                'bobot_realisasi'   => $realisasi,
                'deviasi'           => $realisasi - $rencana,
                'ringkasan_kemajuan'=> 'Minggu ke-1',
                'kendala'           => 'Tidak ada',
                'status'            => 'approved',
                'verified_by'       => $proyek->konsultan_id,
                'approved_by'       => $proyek->ppk_id,
                'approved_at'       => Carbon::now()->subDays(1),
            ]);

            // ==========================================
            // 1 Data Laporan Bulanan
            // ==========================================
            LaporanBulanan::create([
                'proyek_id'          => $proyek->id,
                'kontraktor_id'      => $kontraktorId,
                'bulan'              => Carbon::now()->subMonths(1)->month,
                'tahun'              => Carbon::now()->subMonths(1)->year,
                'bobot_rencana'      => 20,
                'bobot_realisasi'    => 21,
                'deviasi'            => 1,
                'ringkasan_kemajuan' => 'Bulan ke-1 (' . $status . ')',
                'kendala'            => 'Nihil',
                'status'             => $status,
                'verified_by'        => $status === 'approved' ? $proyek->konsultan_id : null,
                'approved_by'        => $status === 'approved' ? $proyek->ppk_id : null,
            ]);
        }
        
        $this->command->info('✅ Data Laporan Harian, Mingguan, Bulanan diseragamkan!');
    }
}
