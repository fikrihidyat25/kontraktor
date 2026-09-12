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

class FourDummyProjectsSeeder extends Seeder
{
    public function run(): void
    {
        $ppkId = 1;
        $pptkId = 5;
        $konsultanId = 6;
        $kontraktorId = 7;

        $projects = [
            [
                'nama' => 'Pembangunan Jembatan Gantung Sungai Batanghari',
                'kontrak' => 'HK.02.01/PPK.III/BJSM/2026/002',
                'lokasi' => 'Kabupaten Dharmasraya, Sumatera Barat',
                'nilai' => 25500000000,
                'deskripsi' => 'Pekerjaan pembangunan jembatan gantung bentang 120 meter.',
            ],
            [
                'nama' => 'Rehabilitasi Jaringan Irigasi D.I. Batang Anai',
                'kontrak' => 'HK.02.01/PPK.III/BJSM/2026/003',
                'lokasi' => 'Kabupaten Padang Pariaman',
                'nilai' => 8450000000,
                'deskripsi' => 'Rehabilitasi saluran primer dan sekunder untuk meningkatkan debit air pertanian.',
            ],
            [
                'nama' => 'Pembangunan Gedung Kuliah Terpadu UNAND',
                'kontrak' => 'HK.02.01/PPK.III/BJSM/2026/004',
                'lokasi' => 'Kota Padang, Sumatera Barat',
                'nilai' => 45000000000,
                'deskripsi' => 'Pembangunan gedung kuliah terpadu 5 lantai beserta fasilitas pendukung.',
            ],
            [
                'nama' => 'Peningkatan Jalan Provinsi Ruas Payakumbuh-Lintau',
                'kontrak' => 'HK.02.01/PPK.III/BJSM/2026/005',
                'lokasi' => 'Kota Payakumbuh - Kabupaten Tanah Datar',
                'nilai' => 32000000000,
                'deskripsi' => 'Pekerjaan perkerasan lentur (AC-WC) dan pelebaran bahu jalan sepanjang 18 KM.',
            ],
        ];

        foreach ($projects as $index => $proj) {
            // 1. Buat Proyek
            $proyek = Proyek::create([
                'nama_proyek'     => $proj['nama'],
                'nomor_kontrak'   => $proj['kontrak'],
                'lokasi'          => $proj['lokasi'],
                'nilai_kontrak'   => $proj['nilai'],
                'tanggal_mulai'   => '2026-02-01',
                'tanggal_selesai' => '2026-11-30',
                'kontraktor_id'   => $kontraktorId,
                'konsultan_id'    => $konsultanId,
                'ppk_id'          => $ppkId,
                'pptk_id'         => $pptkId,
                'status'          => 'aktif',
                'deskripsi'       => $proj['deskripsi'],
            ]);

            // 2. Buat Laporan Harian (3 laporan per proyek, status submitted)
            $cuacas = ['cerah', 'berawan', 'hujan_ringan'];
            $startDate = Carbon::now()->subDays(10);
            
            for ($i = 0; $i < 3; $i++) {
                $tanggal = $startDate->copy()->addDays($i);
                
                $harian = LaporanHarian::create([
                    'proyek_id'     => $proyek->id,
                    'kontraktor_id' => $kontraktorId,
                    'tanggal'       => $tanggal->toDateString(),
                    'kondisi_cuaca' => $cuacas[$i],
                    'waktu_cuaca'   => '08:00 - 16:00',
                    'catatan'       => 'Pekerjaan harian berjalan dengan progres normal. Tidak ada kendala mayor.',
                    'dokumentasi'   => null,
                    'status'        => 'submitted', // Belum divalidasi
                ]);

                // Detail Harian
                TenagaKerja::create(['laporan_harian_id' => $harian->id, 'klasifikasi' => 'Tukang', 'keterangan' => 'Tukang utama', 'jumlah' => rand(5, 10)]);
                Material::create(['laporan_harian_id' => $harian->id, 'jenis_material' => 'Semen', 'satuan' => 'Zak', 'kuantitas_datang' => 50, 'kuantitas_digunakan' => 30]);
                Peralatan::create(['laporan_harian_id' => $harian->id, 'jenis_alat' => 'Excavator', 'jumlah' => 1, 'kondisi' => 'Baik', 'jam_operasi' => 8]);
            }

            // 3. Buat Laporan Mingguan (2 laporan per proyek, status submitted)
            for ($i = 0; $i < 2; $i++) {
                $tglMulai   = Carbon::now()->subWeeks(3 - $i)->startOfWeek();
                $tglSelesai = $tglMulai->copy()->endOfWeek();
                $rencana    = 5 + ($i * 5);
                $realisasi  = $rencana + rand(-1, 2);
                
                LaporanMingguan::create([
                    'proyek_id'         => $proyek->id,
                    'kontraktor_id'     => $kontraktorId,
                    'minggu_ke'         => $i + 1,
                    'tanggal_mulai'     => $tglMulai->toDateString(),
                    'tanggal_selesai'   => $tglSelesai->toDateString(),
                    'bobot_rencana'     => $rencana,
                    'bobot_realisasi'   => $realisasi,
                    'deviasi'           => $realisasi - $rencana,
                    'ringkasan_kemajuan'=> 'Pekerjaan minggu ke-' . ($i+1) . ' selesai dengan baik.',
                    'kendala'           => 'Tidak ada',
                    'file_laporan'      => null,
                    'dokumentasi'       => null,
                    'status'            => 'submitted', // Belum divalidasi
                ]);
            }

            // 4. Buat Laporan Bulanan (1 laporan per proyek, status submitted)
            $bulan = Carbon::now()->subMonth()->month;
            $tahun = Carbon::now()->subMonth()->year;
            $rencana = 15;
            $realisasi = 16;
            
            LaporanBulanan::create([
                'proyek_id'          => $proyek->id,
                'kontraktor_id'      => $kontraktorId,
                'bulan'              => $bulan,
                'tahun'              => $tahun,
                'bobot_rencana'      => $rencana,
                'bobot_realisasi'    => $realisasi,
                'deviasi'            => $realisasi - $rencana,
                'ringkasan_kemajuan' => 'Laporan bulanan pertama untuk proyek ini.',
                'kendala'            => 'Nihil',
                'status'             => 'submitted', // Belum divalidasi
            ]);
        }

        $this->command->info('✅ 4 Proyek Dummy beserta Laporan (Harian, Mingguan, Bulanan) berstatus UNVALIDATED berhasil dibuat!');
    }
}
