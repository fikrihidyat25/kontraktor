<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Proyek;
use App\Models\LaporanHarian;
use App\Models\LaporanMingguan;
use App\Models\LaporanBulanan;
use App\Models\LaporanPengawas;
use App\Models\LaporanLainnya;
use App\Models\KerjaTambahKurang;
use App\Models\SerahTerima;
use App\Models\PermintaanPembayaran;
use App\Models\DokumenProyek;
use App\Models\SiteMeeting;
use App\Models\ShopDrawing;
use App\Models\UangMuka;
use App\Models\UangMukaKonsultan;
use App\Models\BeritaAcaraKonsultan;
use App\Models\SerahTerimaKonsultan;
use App\Models\PembayaranKonsultan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        $kontraktor = User::firstOrCreate(
            ['email' => 'kontraktor@digitaproda.test'],
            ['name' => 'Kontraktor', 'password' => Hash::make('password'), 'role' => 'kontraktor']
        );
        $konsultan = User::firstOrCreate(
            ['email' => 'konsultan@digitaproda.test'],
            ['name' => 'Konsultan', 'password' => Hash::make('password'), 'role' => 'konsultan']
        );
        $ppk = User::firstOrCreate(
            ['email' => 'ppk@digitaproda.test'],
            ['name' => 'PPK', 'password' => Hash::make('password'), 'role' => 'ppk']
        );
        $pptk = User::firstOrCreate(
            ['email' => 'pptk@digitaproda.test'],
            ['name' => 'PPTK', 'password' => Hash::make('password'), 'role' => 'pptk']
        );

        $proyek = Proyek::firstOrCreate(
            ['nama_proyek' => 'Proyek Dummy Test'],
            [
                'kab_kota' => 'Padang',
                'skpd' => 'Dinas PUPR',
                'nomor_kontrak' => 'KONTRAK/001/2026',
                'lokasi' => 'Jl. Dummy',
                'nilai_kontrak' => 1000000000,
                'tanggal_mulai' => Carbon::now()->subMonths(3),
                'tanggal_selesai' => Carbon::now()->addMonths(3),
                'kontraktor_id' => $kontraktor->id,
                'konsultan_id' => $konsultan->id,
                'ppk_id' => $ppk->id,
                'pptk_id' => $pptk->id,
                'status' => 'aktif',
                'deskripsi' => 'Dummy proyek'
            ]
        );

        for ($i = 1; $i <= 5; $i++) {
            // Laporan Harian
            LaporanHarian::create([
                'proyek_id' => $proyek->id,
                'kontraktor_id' => $kontraktor->id,
                'tanggal' => Carbon::now()->subDays($i),
                'kondisi_cuaca' => 'Cerah',
                'waktu_cuaca' => 'Pagi',
                'catatan' => "Catatan harian $i",
                'status' => 'submitted',
            ]);

            // Laporan Mingguan
            LaporanMingguan::create([
                'proyek_id' => $proyek->id,
                'kontraktor_id' => $kontraktor->id,
                'minggu_ke' => $i,
                'tanggal_mulai' => Carbon::now()->subWeeks($i)->startOfWeek(),
                'tanggal_selesai' => Carbon::now()->subWeeks($i)->endOfWeek(),
                'bobot_rencana' => 10 * $i,
                'bobot_realisasi' => 9 * $i,
                'deviasi' => -1 * $i,
                'ringkasan_kemajuan' => "Ringkasan $i",
                'kendala' => 'Tidak ada',
                'status' => 'submitted',
            ]);

            // Laporan Bulanan
            LaporanBulanan::create([
                'proyek_id' => $proyek->id,
                'kontraktor_id' => $kontraktor->id,
                'bulan' => Carbon::now()->subMonths($i)->month,
                'tahun' => Carbon::now()->subMonths($i)->year,
                'bobot_rencana' => 20 * $i,
                'bobot_realisasi' => 18 * $i,
                'deviasi' => -2 * $i,
                'ringkasan_kemajuan' => "Kemajuan $i",
                'kendala' => '-',
                'status' => 'submitted',
            ]);

            // Laporan Pengawas
            LaporanPengawas::create([
                'proyek_id' => $proyek->id,
                'konsultan_id' => $konsultan->id,
                'jenis' => 'mingguan',
                'file_laporan' => 'dummy_pengawas.pdf',
                'catatan' => "Catatan pengawas $i",
                'status' => 'diajukan',
            ]);

            // Laporan Lainnya
            LaporanLainnya::create([
                'proyek_id' => $proyek->id,
                'judul_laporan' => "Laporan Lain $i",
                'tanggal_laporan' => Carbon::now()->subDays($i),
                'keterangan' => 'Keterangan dummy',
                'file_laporan' => 'dummy_lain.pdf',
                'uploaded_by' => $kontraktor->id,
                'status' => 'menunggu_validasi',
            ]);

            // Kerja Tambah Kurang
            KerjaTambahKurang::create([
                'proyek_id' => $proyek->id,
                'kontraktor_id' => $kontraktor->id,
                'nomor_surat_pengajuan' => "KTK-00$i",
                'tanggal_pengajuan' => Carbon::now()->subDays($i),
                'usulan_dari' => 'kontraktor',
                'deskripsi_pekerjaan' => "Pekerjaan KTK $i",
                'status' => 'menunggu_validasi',
            ]);

            // Serah Terima
            SerahTerima::create([
                'proyek_id' => $proyek->id,
                'kontraktor_id' => $kontraktor->id,
                'jenis' => 'PHO',
                'tanggal_pengajuan' => Carbon::now()->subDays($i),
                'keterangan' => "Serah terima $i",
                'surat_permohonan' => 'dummy_surat.pdf',
                'dokumen_lampiran' => 'dummy_lampiran.pdf',
                'status' => 'diajukan',
                'status_konsultan' => 'pending',
                'status_pptk' => 'pending',
            ]);

            // Permintaan Pembayaran
            PermintaanPembayaran::create([
                'proyek_id' => $proyek->id,
                'kontraktor_id' => $kontraktor->id,
                'pembayaran_ke' => $i,
                'progres_tagihan' => 10 * $i,
                'nomor_tagihan' => "INV-00$i",
                'tanggal_pengajuan' => Carbon::now()->subDays($i),
                'termin_ke' => $i,
                'nilai_tagihan' => 10000000 * $i,
                'persentase_kemajuan' => 10 * $i,
                'dokumen_pendukung' => 'dummy_dokumen.pdf',
                'status' => 'diajukan',
            ]);

            // Dokumen Proyek
            DokumenProyek::create([
                'proyek_id' => $proyek->id,
                'tipe_dokumen' => 'Kontrak',
                'nama_dokumen' => "Dokumen $i",
                'file_path' => 'dummy_dokumen.pdf',
                'uploaded_by' => $kontraktor->id,
                'status' => 'menunggu_validasi',
            ]);

            // Site Meeting
            SiteMeeting::create([
                'proyek_id' => $proyek->id,
                'created_by' => $konsultan->id,
                'jenis_rapat' => 'Site Meeting',
                'pertemuan_ke' => $i,
                'tanggal_rapat' => Carbon::now()->subDays($i),
                'agenda_rapat' => "Agenda $i",
                'dokumen_berita_acara' => 'dummy_ba.pdf',
            ]);

            // Shop Drawing
            ShopDrawing::create([
                'proyek_id' => $proyek->id,
                'kontraktor_id' => $kontraktor->id,
                'judul' => "Gambar $i",
                'keterangan' => "Shop drawing detail $i",
                'file_gambar' => 'dummy_shop_drawing.pdf',
                'status' => 'diajukan',
            ]);

            // Uang Muka
            UangMuka::create([
                'proyek_id' => $proyek->id,
                'kontraktor_id' => $kontraktor->id,
                'tanggal_pengajuan' => Carbon::now()->subDays($i),
                'nilai_pengajuan' => 20000000 * $i,
                'surat_permohonan' => 'dummy_surat.pdf',
                'lampiran' => 'dummy_lampiran.pdf',
                'status' => 'menunggu_persetujuan',
            ]);

            // Uang Muka Konsultan
            UangMukaKonsultan::create([
                'proyek_id' => $proyek->id,
                'konsultan_id' => $konsultan->id,
                'nilai_pengajuan' => 5000000 * $i,
                'keterangan' => "Uang muka $i",
                'dokumen_lampiran' => 'dummy_lampiran.pdf',
                'status' => 'diajukan',
            ]);

            // Berita Acara Konsultan
            BeritaAcaraKonsultan::create([
                'proyek_id' => $proyek->id,
                'konsultan_id' => $konsultan->id,
                'judul' => "Berita Acara $i",
                'tanggal' => Carbon::now()->subDays($i),
                'pembahasan' => "Pembahasan BA $i",
                'file_berita_acara' => 'dummy_ba.pdf',
            ]);

            // Serah Terima Konsultan
            SerahTerimaKonsultan::create([
                'proyek_id' => $proyek->id,
                'konsultan_id' => $konsultan->id,
                'jenis' => 'Pengawasan',
                'tanggal_pengajuan' => Carbon::now()->subDays($i),
                'keterangan' => "Serah terima $i",
                'surat_permohonan' => 'dummy_surat.pdf',
                'dokumen_lampiran' => 'dummy_lampiran.pdf',
                'status' => 'diajukan',
            ]);

            // Pembayaran Konsultan
            PembayaranKonsultan::create([
                'proyek_id' => $proyek->id,
                'konsultan_id' => $konsultan->id,
                'termin' => "Termin $i",
                'nilai_pembayaran' => 2000000 * $i,
                'surat_permohonan' => 'dummy_surat.pdf',
                'laporan_kemajuan' => 'dummy_laporan.pdf',
                'status' => 'diajukan',
            ]);
        }
    }
}
