<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyek;
use App\Models\DokumenProyek;
use App\Models\UangMuka;
use App\Models\PermintaanPembayaran;
use App\Models\KerjaTambahKurang;
use App\Models\SerahTerima;
use App\Models\ShopDrawing;
use App\Models\SiteMeeting;
use App\Models\LaporanPengawas;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FixDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kosongkan data dari entitas yang bersangkutan
        DB::table('dokumen_proyeks')->truncate();
        DB::table('uang_mukas')->truncate();
        DB::table('permintaan_pembayarans')->truncate();
        DB::table('kerja_tambah_kurangs')->truncate();
        DB::table('serah_terimas')->truncate();
        DB::table('shop_drawings')->truncate();
        DB::table('site_meetings')->truncate();
        DB::table('laporan_pengawas')->truncate();

        // 2. Ambil semua proyek (harus ada 5)
        $proyeks = Proyek::all();

        foreach ($proyeks as $index => $proyek) {
            $kontraktorId = $proyek->kontraktor_id;
            $konsultanId = $proyek->konsultan_id;
            $ppkId = $proyek->ppk_id;
            
            // 1 data Dokumen Kontrak per proyek
            DokumenProyek::create([
                'proyek_id' => $proyek->id,
                'tipe_dokumen' => 'KONTRAK',
                'nama_dokumen' => 'Dokumen Kontrak Utama - ' . $proyek->nama_proyek,
                'file_path' => 'dummy_path_kontrak.pdf',
                'uploaded_by' => $ppkId,
            ]);

            // 1 data Uang Muka per proyek
            UangMuka::create([
                'proyek_id' => $proyek->id,
                'kontraktor_id' => $kontraktorId,
                'tanggal_pengajuan' => Carbon::now()->subDays(15),
                'nilai_pengajuan' => $proyek->nilai_kontrak * 0.2, // 20%
                'surat_permohonan' => 'dummy_surat_permohonan.pdf',
                'lampiran' => 'dummy_lampiran_uang_muka.pdf',
                'status' => 'menunggu_persetujuan',
            ]);

            // 1 data Permintaan Pembayaran per proyek
            PermintaanPembayaran::create([
                'proyek_id' => $proyek->id,
                'kontraktor_id' => $kontraktorId,
                'pembayaran_ke' => 1,
                'progres_tagihan' => 20,
                'nomor_tagihan' => 'INV-2026-00' . ($index + 1),
                'tanggal_pengajuan' => Carbon::now()->subDays(10),
                'nilai_tagihan' => $proyek->nilai_kontrak * 0.2,
                'status' => 'diajukan',
                'dokumen_pendukung' => 'dummy_invoice.pdf',
            ]);

            // 1 data KTK per proyek
            KerjaTambahKurang::create([
                'proyek_id' => $proyek->id,
                'kontraktor_id' => $kontraktorId,
                'nomor_surat_pengajuan' => 'KTK-2026-00' . ($index + 1),
                'tanggal_pengajuan' => Carbon::now()->subDays(5),
                'usulan_dari' => 'Kontraktor',
                'deskripsi_pekerjaan' => 'Usulan penyesuaian volume akibat kondisi tanah pada area ' . $proyek->lokasi,
                'status' => 'diajukan',
            ]);

            // 1 data Serah Terima (PHO) per proyek
            SerahTerima::create([
                'proyek_id' => $proyek->id,
                'kontraktor_id' => $kontraktorId,
                'jenis' => 'PHO',
                'tanggal_pengajuan' => Carbon::now()->subDays(2),
                'keterangan' => 'Permohonan PHO untuk ' . $proyek->nama_proyek,
                'surat_permohonan' => 'dummy_pho.pdf',
                'status' => 'diajukan',
                'status_konsultan' => 'pending',
                'status_pptk' => 'pending',
            ]);

            // 1 data Shop Drawing per proyek
            ShopDrawing::create([
                'proyek_id' => $proyek->id,
                'kontraktor_id' => $kontraktorId,
                'judul' => 'Shop Drawing Utama - ' . $proyek->nama_proyek,
                'keterangan' => 'Detail pelaksanaan konstruksi',
                'file_gambar' => 'dummy_shop_drawing.pdf',
                'status' => 'diajukan',
            ]);

            // 1 data Site Meeting per proyek
            SiteMeeting::create([
                'proyek_id' => $proyek->id,
                'created_by' => $konsultanId,
                'jenis_rapat' => 'Site Meeting',
                'pertemuan_ke' => 1,
                'tanggal_rapat' => Carbon::now()->subWeeks(1),
                'agenda_rapat' => 'Rapat koordinasi awal pelaksanaan',
                'dokumen_berita_acara' => 'dummy_berita_acara.pdf',
            ]);

            // 1 data Laporan Pengawas per proyek
            LaporanPengawas::create([
                'proyek_id' => $proyek->id,
                'konsultan_id' => $konsultanId,
                'jenis' => 'Bulanan',
                'file_laporan' => 'dummy_laporan_pengawas.pdf',
                'catatan' => 'Laporan pengawasan independen',
                'status' => 'diajukan',
            ]);
        }
        
        $this->command->info('✅ Data Dummy berhasil disesuaikan! (Masing-masing 1 data per Proyek untuk semua entitas)');
    }
}
