<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyek;
use App\Models\UangMuka;
use App\Models\PermintaanPembayaran;
use App\Models\KerjaTambahKurang;
use App\Models\SerahTerima;
use App\Models\ShopDrawing;
use App\Models\SiteMeeting;
use App\Models\LaporanPengawas;
use Carbon\Carbon;

class AllFeaturesDummySeeder extends Seeder
{
    public function run(): void
    {
        $proyeks = Proyek::where('id', '>', 1)->get(); // Get the 4 dummy projects

        foreach ($proyeks as $proyek) {
            $kontraktorId = $proyek->kontraktor_id;
            $konsultanId = $proyek->konsultan_id;

            // 1. Uang Muka (1-2 data)
            for ($i=0; $i<2; $i++) {
                UangMuka::create([
                    'proyek_id' => $proyek->id,
                    'kontraktor_id' => $kontraktorId,
                    'tanggal_pengajuan' => Carbon::now()->subDays(rand(10, 30)),
                    'nilai_pengajuan' => rand(100000000, 500000000),
                    'surat_permohonan' => 'dummy_surat_permohonan.pdf',
                    'lampiran' => 'dummy_lampiran_uang_muka.pdf',
                    'status' => 'menunggu_persetujuan',
                ]);
            }

            // 2. Permintaan Pembayaran (1-2 data)
            for ($i=0; $i<2; $i++) {
                PermintaanPembayaran::create([
                    'proyek_id' => $proyek->id,
                    'kontraktor_id' => $kontraktorId,
                    'pembayaran_ke' => $i + 1,
                    'progres_tagihan' => ($i + 1) * 20,
                    'nomor_tagihan' => 'INV-2026-' . rand(100, 999),
                    'tanggal_pengajuan' => Carbon::now()->subDays(rand(5, 20)),
                    'nilai_tagihan' => rand(500000000, 1500000000),
                    'status' => 'diajukan',
                    'dokumen_pendukung' => 'dummy_invoice.pdf',
                ]);
            }

            // 3. Kerja Tambah Kurang (1-2 data)
            for ($i=0; $i<2; $i++) {
                KerjaTambahKurang::create([
                    'proyek_id' => $proyek->id,
                    'kontraktor_id' => $kontraktorId,
                    'nomor_surat_pengajuan' => 'KTK-2026-' . rand(100, 999),
                    'tanggal_pengajuan' => Carbon::now()->subDays(rand(5, 15)),
                    'usulan_dari' => 'Kontraktor',
                    'deskripsi_pekerjaan' => 'Usulan penambahan volume pekerjaan pada segmen ' . ($i+1) . ' karena kondisi lapangan.',
                    'status' => 'diajukan',
                ]);
            }

            // 4. Serah Terima (1 data PHO)
            SerahTerima::create([
                'proyek_id' => $proyek->id,
                'kontraktor_id' => $kontraktorId,
                'jenis' => 'PHO',
                'tanggal_pengajuan' => Carbon::now()->subDays(rand(1, 5)),
                'keterangan' => 'Permohonan PHO tahap pertama.',
                'surat_permohonan' => 'dummy_pho.pdf',
                'status' => 'diajukan',
                'status_konsultan' => 'pending',
                'status_pptk' => 'pending',
            ]);

            // 5. Shop Drawing (2 data)
            for ($i=0; $i<2; $i++) {
                ShopDrawing::create([
                    'proyek_id' => $proyek->id,
                    'kontraktor_id' => $kontraktorId,
                    'judul' => 'Shop Drawing Segmen ' . ($i+1),
                    'keterangan' => 'Detail penulangan dan dimensi struktur utama',
                    'file_gambar' => 'dummy_shop_drawing.pdf',
                    'status' => 'diajukan',
                ]);
            }

            // 6. Site Meeting / Berita Acara (2 data)
            for ($i=0; $i<2; $i++) {
                SiteMeeting::create([
                    'proyek_id' => $proyek->id,
                    'created_by' => $konsultanId,
                    'jenis_rapat' => 'Site Meeting',
                    'pertemuan_ke' => $i + 1,
                    'tanggal_rapat' => Carbon::now()->subWeeks(3 - $i),
                    'agenda_rapat' => 'Evaluasi progres minggu ke-' . ($i+1) . ' dan kendala teknis.',
                    'dokumen_berita_acara' => 'dummy_berita_acara.pdf',
                ]);
            }

            // 7. Laporan Pengawas (2 data)
            for ($i=0; $i<2; $i++) {
                LaporanPengawas::create([
                    'proyek_id' => $proyek->id,
                    'konsultan_id' => $konsultanId,
                    'jenis' => 'Bulanan',
                    'file_laporan' => 'dummy_laporan_pengawas.pdf',
                    'catatan' => 'Laporan pengawasan independen untuk progres bulan ke-' . ($i+1),
                    'status' => 'diajukan',
                ]);
            }
        }
        
        $this->command->info('✅ Data Dummy Lanjutan berhasil ditambahkan ke semua fitur/entitas!');
    }
}
