<x-app-layout>
    <x-slot name="title">Input Laporan Harian</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Input Laporan Harian</h1>
                <p class="text-sm text-[#64748B] mt-1">{{ now()->isoFormat('dddd, D MMMM Y') }}</p>
            </div>
            <div>
                <a href="{{ route('laporan-harian.index') }}" class="text-sm font-semibold text-[#64748B] hover:text-[#0F172B] bg-white border border-[#E7E3DC] px-4 py-2 rounded shadow-sm">
                    Batal & Kembali
                </a>
            </div>
        </div>

        <div class="bg-[#EFF6FF] border-l-4 border-[#1D4ED8] text-[#1E3A8A] p-4 mb-6 rounded shadow-sm text-sm font-medium flex items-center">
            <svg class="w-5 h-5 mr-3 text-[#1D4ED8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>Proyek Aktif: <strong class="font-bold">{{ $proyeks->first()->nama_proyek }}</strong> <span class="text-[#3B82F6] mx-2">|</span> Lokasi: {{ $proyeks->first()->lokasi }}</div>
        </div>

        <form method="POST" action="{{ route('laporan-harian.store') }}" id="form-laporan">
            @csrf
            <input type="hidden" name="proyek_id" value="{{ $proyeks->first()->id }}">

            <!-- INFO DASAR -->
            <div class="bg-white rounded-lg shadow-sm border border-[#E7E3DC] p-6 mb-6">
                <h2 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider mb-4 border-b border-[#E7E3DC] pb-2">Informasi Dasar Laporan</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Tanggal Laporan</label>
                        <input type="date" name="tanggal" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal')<div class="text-[11px] text-[#DC2626] mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Kondisi Cuaca</label>
                        <select name="kondisi_cuaca" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm" required>
                            <option value="cerah" {{ old('kondisi_cuaca')=='cerah'?'selected':'' }}>☀️ Cerah</option>
                            <option value="berawan" {{ old('kondisi_cuaca')=='berawan'?'selected':'' }}>⛅ Berawan</option>
                            <option value="hujan_ringan" {{ old('kondisi_cuaca')=='hujan_ringan'?'selected':'' }}>🌦️ Hujan Ringan</option>
                            <option value="hujan_lebat" {{ old('kondisi_cuaca')=='hujan_lebat'?'selected':'' }}>🌧️ Hujan Lebat</option>
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Catatan Umum / Kendala Lapangan</label>
                        <textarea name="catatan" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm" rows="2" placeholder="Deskripsikan kondisi lapangan, kendala, atau catatan penting hari ini...">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- TABEL TENAGA KERJA -->
            <div class="bg-white rounded-lg shadow-sm border border-[#E7E3DC] overflow-hidden mb-6">
                <div class="bg-[#F8FAFC] border-b border-[#E7E3DC] px-5 py-3 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">A. Tenaga Kerja</h3>
                    <button type="button" onclick="addRow('tenaga-kerja-tbody', tenagaKerjaRow)" class="text-[11px] font-bold text-[#1D4ED8] hover:text-[#1E3A8A] bg-[#EFF6FF] px-3 py-1 rounded border border-[#BFDBFE] transition">+ Tambah Baris</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[11px] text-[#64748B] uppercase bg-white border-b border-[#E7E3DC]">
                            <tr>
                                <th class="px-4 py-3 font-semibold w-[35%]">Klasifikasi</th>
                                <th class="px-4 py-3 font-semibold w-[35%]">Keterangan / Sub</th>
                                <th class="px-4 py-3 font-semibold w-[15%] text-center">Jumlah</th>
                                <th class="px-4 py-3 font-semibold w-[15%] text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tenaga-kerja-tbody">
                            <tr class="border-b border-[#E7E3DC] hover:bg-[#F8FAFC]">
                                <td class="px-4 py-2"><select name="tenaga_kerja[0][klasifikasi]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm focus:ring-[#FFA000]"><option value="mandor">Mandor</option><option value="tukang">Tukang</option><option value="pembantu_tukang">Pembantu Tukang</option><option value="sub_kontraktor">Sub-Kontraktor</option><option value="lainnya">Lainnya</option></select></td>
                                <td class="px-4 py-2"><input type="text" name="tenaga_kerja[0][keterangan]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm focus:ring-[#FFA000]" placeholder="Opsional"></td>
                                <td class="px-4 py-2"><input type="number" name="tenaga_kerja[0][jumlah]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm text-center focus:ring-[#FFA000]" value="0" min="0" required></td>
                                <td class="px-4 py-2 text-center"><button type="button" onclick="removeRow(this)" class="text-[#DC2626] hover:bg-[#FEF2F2] p-1.5 rounded transition" title="Hapus baris"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TABEL MATERIAL -->
            <div class="bg-white rounded-lg shadow-sm border border-[#E7E3DC] overflow-hidden mb-6">
                <div class="bg-[#F8FAFC] border-b border-[#E7E3DC] px-5 py-3 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">B. Material Terpakai</h3>
                    <button type="button" onclick="addRow('material-tbody', materialRow)" class="text-[11px] font-bold text-[#1D4ED8] hover:text-[#1E3A8A] bg-[#EFF6FF] px-3 py-1 rounded border border-[#BFDBFE] transition">+ Tambah Baris</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[11px] text-[#64748B] uppercase bg-white border-b border-[#E7E3DC]">
                            <tr>
                                <th class="px-4 py-3 font-semibold w-[35%]">Jenis Material</th>
                                <th class="px-4 py-3 font-semibold w-[15%]">Satuan</th>
                                <th class="px-4 py-3 font-semibold w-[20%] text-center">Datang</th>
                                <th class="px-4 py-3 font-semibold w-[20%] text-center">Pakai</th>
                                <th class="px-4 py-3 font-semibold w-[10%] text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="material-tbody">
                            <tr class="border-b border-[#E7E3DC] hover:bg-[#F8FAFC]">
                                <td class="px-4 py-2"><input type="text" name="material[0][jenis_material]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm focus:ring-[#FFA000]" placeholder="Misal: Semen..."></td>
                                <td class="px-4 py-2"><input type="text" name="material[0][satuan]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm focus:ring-[#FFA000]" placeholder="sak, m³"></td>
                                <td class="px-4 py-2"><input type="number" name="material[0][kuantitas_datang]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm text-right focus:ring-[#FFA000]" value="0" min="0" step="0.01" required></td>
                                <td class="px-4 py-2"><input type="number" name="material[0][kuantitas_digunakan]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm text-right focus:ring-[#FFA000]" value="0" min="0" step="0.01" required></td>
                                <td class="px-4 py-2 text-center"><button type="button" onclick="removeRow(this)" class="text-[#DC2626] hover:bg-[#FEF2F2] p-1.5 rounded transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TABEL PERALATAN -->
            <div class="bg-white rounded-lg shadow-sm border border-[#E7E3DC] overflow-hidden mb-6">
                <div class="bg-[#F8FAFC] border-b border-[#E7E3DC] px-5 py-3 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">C. Peralatan</h3>
                    <button type="button" onclick="addRow('peralatan-tbody', peralatanRow)" class="text-[11px] font-bold text-[#1D4ED8] hover:text-[#1E3A8A] bg-[#EFF6FF] px-3 py-1 rounded border border-[#BFDBFE] transition">+ Tambah Baris</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[11px] text-[#64748B] uppercase bg-white border-b border-[#E7E3DC]">
                            <tr>
                                <th class="px-4 py-3 font-semibold w-[35%]">Jenis Alat</th>
                                <th class="px-4 py-3 font-semibold w-[15%] text-center">Jumlah</th>
                                <th class="px-4 py-3 font-semibold w-[20%]">Kondisi</th>
                                <th class="px-4 py-3 font-semibold w-[20%] text-center">Jam Operasi</th>
                                <th class="px-4 py-3 font-semibold w-[10%] text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="peralatan-tbody">
                            <tr class="border-b border-[#E7E3DC] hover:bg-[#F8FAFC]">
                                <td class="px-4 py-2"><input type="text" name="peralatan[0][jenis_alat]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm focus:ring-[#FFA000]" placeholder="Excavator..."></td>
                                <td class="px-4 py-2"><input type="number" name="peralatan[0][jumlah]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm text-center focus:ring-[#FFA000]" value="1" min="1" required></td>
                                <td class="px-4 py-2"><select name="peralatan[0][kondisi]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm focus:ring-[#FFA000]"><option value="baik">Baik</option><option value="rusak_ringan">Rusak Ringan</option><option value="rusak_berat">Rusak Berat</option><option value="tidak_beroperasi">Tidak Beroperasi</option></select></td>
                                <td class="px-4 py-2"><input type="number" name="peralatan[0][jam_operasi]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm text-center focus:ring-[#FFA000]" value="0" min="0" step="0.5" required></td>
                                <td class="px-4 py-2 text-center"><button type="button" onclick="removeRow(this)" class="text-[#DC2626] hover:bg-[#FEF2F2] p-1.5 rounded transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TABEL REALISASI -->
            <div class="bg-white rounded-lg shadow-sm border border-[#E7E3DC] overflow-hidden mb-8">
                <div class="bg-[#F8FAFC] border-b border-[#E7E3DC] px-5 py-3 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">D. Realisasi Biaya & Bobot</h3>
                    <button type="button" onclick="addRow('realisasi-tbody', realisasiRow)" class="text-[11px] font-bold text-[#1D4ED8] hover:text-[#1E3A8A] bg-[#EFF6FF] px-3 py-1 rounded border border-[#BFDBFE] transition">+ Tambah Baris</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[11px] text-[#64748B] uppercase bg-white border-b border-[#E7E3DC]">
                            <tr>
                                <th class="px-4 py-3 font-semibold w-[40%]">Divisi Pekerjaan</th>
                                <th class="px-4 py-3 font-semibold w-[25%] text-right">Nilai Realisasi (Rp)</th>
                                <th class="px-4 py-3 font-semibold w-[25%] text-right">Bobot Fisik (%)</th>
                                <th class="px-4 py-3 font-semibold w-[10%] text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="realisasi-tbody">
                            <tr class="border-b border-[#E7E3DC] hover:bg-[#F8FAFC]">
                                <td class="px-4 py-2"><input type="text" name="realisasi[0][divisi_pekerjaan]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm focus:ring-[#FFA000]" placeholder="Misal: Pekerjaan Tanah..."></td>
                                <td class="px-4 py-2"><input type="number" name="realisasi[0][nilai_realisasi]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm text-right focus:ring-[#FFA000]" value="0" min="0" step="1000" required></td>
                                <td class="px-4 py-2"><input type="number" name="realisasi[0][bobot_fisik]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm text-right focus:ring-[#FFA000]" value="0" min="0" max="100" step="0.01" required></td>
                                <td class="px-4 py-2 text-center"><button type="button" onclick="removeRow(this)" class="text-[#DC2626] hover:bg-[#FEF2F2] p-1.5 rounded transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ACTIONS -->
            <div class="flex justify-end gap-3 pt-4 border-t border-[#E7E3DC]">
                <button type="submit" name="action" value="draft" class="bg-white border border-[#CBD5E1] text-[#475569] px-6 py-2.5 rounded font-bold text-sm hover:bg-[#F1F5F9] transition shadow-sm">
                    Simpan Draft
                </button>
                <button type="submit" name="action" value="submit" class="bg-[#15803D] text-white px-6 py-2.5 rounded font-bold text-sm hover:bg-opacity-90 transition shadow-sm" onclick="return confirm('Kirim laporan ini ke Konsultan Pengawas? Pastikan semua data sudah benar.')">
                    Kirim ke Konsultan
                </button>
            </div>
        </form>
    </div>

    <script>
    function removeRow(btn) {
        const row = btn.closest('tr');
        if (row.closest('tbody').rows.length > 1) {
            row.remove();
        } else {
            alert('Minimal satu baris data diperlukan.');
        }
    }
    function addRow(tbodyId, templateFn) {
        const tbody = document.getElementById(tbodyId);
        const tr = document.createElement('tr');
        tr.className = "border-b border-[#E7E3DC] hover:bg-[#F8FAFC]";
        tr.innerHTML = templateFn(tbody.rows.length);
        tbody.appendChild(tr);
    }
    const btnHtml = '<td class="px-4 py-2 text-center"><button type="button" onclick="removeRow(this)" class="text-[#DC2626] hover:bg-[#FEF2F2] p-1.5 rounded transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></td>';

    function tenagaKerjaRow(idx) {
        return `<td class="px-4 py-2"><select name="tenaga_kerja[${idx}][klasifikasi]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm focus:ring-[#FFA000]"><option value="mandor">Mandor</option><option value="tukang">Tukang</option><option value="pembantu_tukang">Pembantu Tukang</option><option value="sub_kontraktor">Sub-Kontraktor</option><option value="lainnya">Lainnya</option></select></td><td class="px-4 py-2"><input type="text" name="tenaga_kerja[${idx}][keterangan]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm focus:ring-[#FFA000]" placeholder="Opsional"></td><td class="px-4 py-2"><input type="number" name="tenaga_kerja[${idx}][jumlah]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm text-center focus:ring-[#FFA000]" value="0" min="0" required></td>` + btnHtml;
    }
    function materialRow(idx) {
        return `<td class="px-4 py-2"><input type="text" name="material[${idx}][jenis_material]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm focus:ring-[#FFA000]" placeholder="Jenis material..."></td><td class="px-4 py-2"><input type="text" name="material[${idx}][satuan]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm focus:ring-[#FFA000]" placeholder="satuan"></td><td class="px-4 py-2"><input type="number" name="material[${idx}][kuantitas_datang]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm text-right focus:ring-[#FFA000]" value="0" min="0" step="0.01" required></td><td class="px-4 py-2"><input type="number" name="material[${idx}][kuantitas_digunakan]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm text-right focus:ring-[#FFA000]" value="0" min="0" step="0.01" required></td>` + btnHtml;
    }
    function peralatanRow(idx) {
        return `<td class="px-4 py-2"><input type="text" name="peralatan[${idx}][jenis_alat]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm focus:ring-[#FFA000]" placeholder="Jenis alat..."></td><td class="px-4 py-2"><input type="number" name="peralatan[${idx}][jumlah]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm text-center focus:ring-[#FFA000]" value="1" min="1" required></td><td class="px-4 py-2"><select name="peralatan[${idx}][kondisi]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm focus:ring-[#FFA000]"><option value="baik">Baik</option><option value="rusak_ringan">Rusak Ringan</option><option value="rusak_berat">Rusak Berat</option><option value="tidak_beroperasi">Tidak Beroperasi</option></select></td><td class="px-4 py-2"><input type="number" name="peralatan[${idx}][jam_operasi]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm text-center focus:ring-[#FFA000]" value="0" min="0" step="0.5" required></td>` + btnHtml;
    }
    function realisasiRow(idx) {
        return `<td class="px-4 py-2"><input type="text" name="realisasi[${idx}][divisi_pekerjaan]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm focus:ring-[#FFA000]" placeholder="Divisi pekerjaan..."></td><td class="px-4 py-2"><input type="number" name="realisasi[${idx}][nilai_realisasi]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm text-right focus:ring-[#FFA000]" value="0" min="0" step="1000" required></td><td class="px-4 py-2"><input type="number" name="realisasi[${idx}][bobot_fisik]" class="w-full border-[#E7E3DC] rounded py-1 px-2 text-sm text-right focus:ring-[#FFA000]" value="0" min="0" max="100" step="0.01" required></td>` + btnHtml;
    }
    </script>
</x-app-layout>
