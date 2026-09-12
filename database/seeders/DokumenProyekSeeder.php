<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyek;
use App\Models\DokumenProyek;

class DokumenProyekSeeder extends Seeder
{
    public function run(): void
    {
        $proyeks = Proyek::all();

        foreach ($proyeks as $proyek) {
            // Check if already exists to avoid duplicates if run multiple times
            if ($proyek->dokumenProyeks()->where('tipe_dokumen', 'SPMK')->doesntExist()) {
                DokumenProyek::create([
                    'proyek_id' => $proyek->id,
                    'tipe_dokumen' => 'SPMK',
                    'nama_dokumen' => 'SPMK Tahap 1 - ' . $proyek->nama_proyek,
                    'file_path' => 'dummy_path_spmk.pdf',
                    'uploaded_by' => $proyek->ppk_id,
                ]);
            }

            if ($proyek->dokumenProyeks()->where('tipe_dokumen', 'Kontrak')->doesntExist()) {
                DokumenProyek::create([
                    'proyek_id' => $proyek->id,
                    'tipe_dokumen' => 'Kontrak',
                    'nama_dokumen' => 'Dokumen Kontrak Utama - ' . $proyek->nama_proyek,
                    'file_path' => 'dummy_path_kontrak.pdf',
                    'uploaded_by' => $proyek->ppk_id,
                ]);
            }
            
            if ($proyek->dokumenProyeks()->where('tipe_dokumen', 'Addendum')->doesntExist()) {
                DokumenProyek::create([
                    'proyek_id' => $proyek->id,
                    'tipe_dokumen' => 'Addendum',
                    'nama_dokumen' => 'Addendum 01 - ' . $proyek->nama_proyek,
                    'file_path' => 'dummy_path_addendum.pdf',
                    'uploaded_by' => $proyek->ppk_id,
                ]);
            }
        }
        
        $this->command->info('✅ Data Dokumen Kontrak & SPMK berhasil ditambahkan untuk semua proyek!');
    }
}
