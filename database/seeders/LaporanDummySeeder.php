<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaporanHarian;
use App\Models\LaporanMingguan;
use App\Models\LaporanBulanan;
use App\Models\TenagaKerja;
use App\Models\Material;
use App\Models\Peralatan;

class LaporanDummySeeder extends Seeder
{
    public function run(): void
    {
        $proyekId     = 2;  // Pelebaran Jalan Maransi
        $kontraktorId = 7;  // PT Adhi Karya Tbk

        // ─── LAPORAN HARIAN (10 data) ──────────────────────────────────
        $cuacas = ['cerah', 'berawan', 'hujan_ringan', 'cerah', 'berawan', 'cerah', 'cerah', 'hujan_lebat', 'berawan', 'cerah'];
        $catatans = [
            'Pekerjaan galian tanah berjalan sesuai rencana.',
            'Pengecoran pondasi titik A1-A5 selesai.',
            'Hujan ringan menyebabkan penundaan 1 jam, pekerjaan dilanjutkan sore hari.',
            'Pemasangan bekisting untuk kolom lantai 1.',
            'Pengiriman material besi tulangan tiba di lokasi.',
            'Pekerjaan pasangan batu kali untuk dinding penahan tanah.',
            'Pemadatan tanah timbunan dengan vibro compactor.',
            'Hujan lebat, pekerjaan dihentikan lebih awal pukul 13.00.',
            'Pekerjaan plesteran dinding area selatan berjalan lancar.',
            'Finishing pekerjaan pengaspalan lapis pertama (AC-BC).',
        ];

        $startDate = now()->subDays(30);

        for ($i = 0; $i < 10; $i++) {
            $tanggal = $startDate->copy()->addDays($i * 2);

            $laporan = LaporanHarian::create([
                'proyek_id'     => $proyekId,
                'kontraktor_id' => $kontraktorId,
                'tanggal'       => $tanggal->toDateString(),
                'kondisi_cuaca' => $cuacas[$i],
                'waktu_cuaca'   => '07:00 - 17:00',
                'catatan'       => $catatans[$i],
                'dokumentasi'   => null,
                'status'        => 'submitted', // sudah disubmit, belum divalidasi konsultan
            ]);

            // Tenaga Kerja
            TenagaKerja::create([
                'laporan_harian_id' => $laporan->id,
                'klasifikasi'       => 'mandor',
                'keterangan'        => 'Pengawas lapangan',
                'jumlah'            => 2,
            ]);
            TenagaKerja::create([
                'laporan_harian_id' => $laporan->id,
                'klasifikasi'       => 'tukang',
                'keterangan'        => 'Tukang batu dan besi',
                'jumlah'            => rand(8, 15),
            ]);
            TenagaKerja::create([
                'laporan_harian_id' => $laporan->id,
                'klasifikasi'       => 'pembantu_tukang',
                'keterangan'        => 'Tenaga kasar',
                'jumlah'            => rand(10, 20),
            ]);

            // Material
            Material::create([
                'laporan_harian_id'  => $laporan->id,
                'jenis_material'     => 'Batu Split 2/3',
                'satuan'             => 'm³',
                'kuantitas_datang'   => rand(5, 15),
                'kuantitas_digunakan'=> rand(3, 10),
            ]);
            Material::create([
                'laporan_harian_id'  => $laporan->id,
                'jenis_material'     => 'Semen Portland',
                'satuan'             => 'zak',
                'kuantitas_datang'   => rand(20, 50),
                'kuantitas_digunakan'=> rand(15, 40),
            ]);

            // Peralatan
            Peralatan::create([
                'laporan_harian_id' => $laporan->id,
                'jenis_alat'        => 'Excavator',
                'jumlah'            => 1,
                'kondisi'           => 'baik',
                'jam_operasi'       => rand(6, 8),
            ]);
            Peralatan::create([
                'laporan_harian_id' => $laporan->id,
                'jenis_alat'        => 'Dump Truck',
                'jumlah'            => rand(2, 4),
                'kondisi'           => 'baik',
                'jam_operasi'       => rand(5, 7),
            ]);
        }

        // ─── LAPORAN MINGGUAN (4 data) ─────────────────────────────────
        $ringkasans = [
            'Minggu pertama pelaksanaan pekerjaan. Fokus pada pekerjaan persiapan, pembersihan lahan, dan mobilisasi alat berat ke lokasi proyek.',
            'Pekerjaan galian tanah mencapai 60% dari rencana. Pemasangan pondasi di area A telah selesai dikerjakan.',
            'Pengecoran kolom dan balok lantai 1 berjalan sesuai jadwal. Terdapat keterlambatan 1 hari akibat curah hujan tinggi.',
            'Pekerjaan dinding bata dan plesteran mencapai 40%. Pengiriman material besi untuk lantai 2 telah tiba di lokasi.',
        ];
        $kendalas = [
            'Tidak ada kendala berarti pada minggu ini.',
            'Curah hujan di beberapa hari menyebabkan penurunan produktivitas tukang.',
            'Hujan deras selama 2 hari berturut-turut menghambat pekerjaan pengecoran.',
            'Material semen sempat terlambat pengiriman 1 hari dari supplier.',
        ];

        for ($i = 0; $i < 4; $i++) {
            $tglMulai   = now()->subWeeks(8 - ($i * 2))->startOfWeek();
            $tglSelesai = $tglMulai->copy()->endOfWeek();
            $rencana    = 10 + ($i * 5);
            $realisasi  = $rencana + rand(-3, 3);
            $deviasi    = $realisasi - $rencana;

            LaporanMingguan::create([
                'proyek_id'         => $proyekId,
                'kontraktor_id'     => $kontraktorId,
                'minggu_ke'         => $i + 1,
                'tanggal_mulai'     => $tglMulai->toDateString(),
                'tanggal_selesai'   => $tglSelesai->toDateString(),
                'bobot_rencana'     => $rencana,
                'bobot_realisasi'   => $realisasi,
                'deviasi'           => $deviasi,
                'ringkasan_kemajuan'=> $ringkasans[$i],
                'kendala'           => $kendalas[$i],
                'file_laporan'      => null,
                'dokumentasi'       => null,
                'status'            => 'submitted', // sudah disubmit, belum divalidasi
            ]);
        }

        // ─── LAPORAN BULANAN (2 data) ──────────────────────────────────
        $ringkasanBulanan = [
            'Bulan pertama pelaksanaan proyek. Pekerjaan persiapan, mobilisasi, dan galian tanah telah selesai 100%. Pekerjaan pondasi mencapai 80% dari rencana.',
            'Bulan kedua pelaksanaan proyek. Pekerjaan pondasi telah diselesaikan. Pekerjaan struktur kolom dan balok lantai 1 berjalan dengan progres 65% dari target bulan ini.',
        ];

        for ($i = 0; $i < 2; $i++) {
            $bulan = now()->subMonths(2 - $i)->month;
            $tahun = now()->subMonths(2 - $i)->year;
            $rencana = 15 + ($i * 10);
            $realisasi = $rencana + rand(-2, 4);
            $deviasi = $realisasi - $rencana;

            LaporanBulanan::create([
                'proyek_id'          => $proyekId,
                'kontraktor_id'      => $kontraktorId,
                'bulan'              => $bulan,
                'tahun'              => $tahun,
                'bobot_rencana'      => $rencana,
                'bobot_realisasi'    => $realisasi,
                'deviasi'            => $deviasi,
                'ringkasan_kemajuan' => $ringkasanBulanan[$i],
                'kendala'            => 'Kendala cuaca dan pengiriman material menjadi tantangan utama bulan ini.',
                'status'             => 'submitted', // sudah disubmit, belum divalidasi
            ]);
        }

        $this->command->info('✅ Dummy data laporan berhasil dibuat!');
        $this->command->info('   - 10 Laporan Harian (status: submitted)');
        $this->command->info('   - 4 Laporan Mingguan (status: submitted)');
        $this->command->info('   - 2 Laporan Bulanan (status: submitted)');
    }
}
