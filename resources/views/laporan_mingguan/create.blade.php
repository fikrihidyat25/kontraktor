<x-app-layout>
    <x-slot name="title">Input Laporan Mingguan</x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Input Laporan Mingguan</h1>
                <p class="text-sm text-[#64748B] mt-1">Unggah Dokumen Laporan Mingguan</p>
            </div>
            <div>
                <a href="{{ route('laporan-mingguan.index') }}" class="text-sm font-semibold text-[#64748B] hover:text-[#0F172B] bg-white border border-[#E7E3DC] px-4 py-2 rounded shadow-sm">
                    Batal & Kembali
                </a>
            </div>
        </div>

        <div class="bg-[#EFF6FF] border-l-4 border-[#1D4ED8] text-[#1E3A8A] p-4 mb-6 rounded shadow-sm text-sm font-medium flex items-center">
            <svg class="w-5 h-5 mr-3 text-[#1D4ED8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div>Proyek: <strong class="font-bold">{{ $proyek->nama_proyek }}</strong> <span class="text-[#3B82F6] mx-2">|</span> Input untuk: <strong>Minggu ke-{{ $lastMinggu + 1 }}</strong></div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-[#E7E3DC] p-6 mb-6">
            <h2 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider mb-4 border-b border-[#E7E3DC] pb-2">Data Kemajuan Mingguan</h2>
            
            <form method="POST" action="{{ route('laporan-mingguan.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="proyek_id" value="{{ $proyek->id }}">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Minggu Ke-</label>
                        <input type="number" name="minggu_ke" class="w-full border-[#E7E3DC] rounded text-sm font-bold text-[#0F172B] bg-gray-100 cursor-not-allowed" value="{{ old('minggu_ke', $lastMinggu + 1) }}" readonly required>
                        @error('minggu_ke')<div class="text-[11px] text-[#DC2626] mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Tanggal Mulai (Senin)</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm" value="{{ old('tanggal_mulai') }}" required>
                        @error('tanggal_mulai')<div class="text-[11px] text-[#DC2626] mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Tanggal Selesai (Minggu)</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="w-full border-[#E7E3DC] rounded bg-gray-100 text-[#64748B] cursor-not-allowed text-sm" value="{{ old('tanggal_selesai') }}" readonly required>
                        @error('tanggal_selesai')<div class="text-[11px] text-[#DC2626] mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="bg-[#F8FAFC] border border-[#E7E3DC] p-5 rounded-lg mb-6">
                    <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider mb-4 border-b border-[#E7E3DC] pb-2">Akumulasi Progress</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                        <div class="bg-white p-3 border border-[#E7E3DC] rounded">
                            <label class="block text-[10px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Akumulasi Realisasi S/D Minggu Lalu</label>
                            <div class="text-lg font-bold text-[#0F172B]">{{ number_format($bobotLaluRealisasi, 2, ',', '.') }}%</div>
                            <input type="hidden" id="bobot_lalu_realisasi" value="{{ $bobotLaluRealisasi }}">
                        </div>
                        <div class="bg-white p-3 border border-[#E7E3DC] rounded">
                            <label class="block text-[10px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Akumulasi Rencana S/D Minggu Lalu</label>
                            <div class="text-lg font-bold text-[#0F172B]">{{ number_format($bobotLaluRencana, 2, ',', '.') }}%</div>
                            <input type="hidden" id="bobot_lalu_rencana" value="{{ $bobotLaluRencana }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Progress Realisasi Minggu Ini (%)</label>
                            <input type="number" name="progress_minggu_ini" id="progress_minggu_ini" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm" value="{{ old('progress_minggu_ini') }}" step="0.01" min="0" max="100" required>
                            @error('progress_minggu_ini')<div class="text-[11px] text-[#DC2626] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Rencana Progress Minggu Ini (%)</label>
                            <input type="number" name="rencana_minggu_ini" id="rencana_minggu_ini" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm" value="{{ old('rencana_minggu_ini') }}" step="0.01" min="0" max="100" required>
                            @error('rencana_minggu_ini')<div class="text-[11px] text-[#DC2626] mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-[#EFF6FF] border border-[#BFDBFE] rounded text-sm text-[#1E3A8A]">
                        Akumulasi Realisasi S/D Minggu Ini: <strong id="total_realisasi">{{ number_format($bobotLaluRealisasi, 2, ',', '.') }}</strong>%
                        <br>
                        Akumulasi Rencana S/D Minggu Ini: <strong id="total_rencana">{{ number_format($bobotLaluRencana, 2, ',', '.') }}</strong>%
                        <br>
                        Deviasi: <strong id="deviasi_text">0,00</strong>%
                    </div>
                </div>

                <div class="bg-[#F8FAFC] border border-[#E7E3DC] p-5 rounded-lg mb-6">
                    <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider mb-4 border-b border-[#E7E3DC] pb-2">Dokumen & Lampiran</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Unggah File Laporan (PDF/Excel)</label>
                            <input type="file" name="file_laporan" class="w-full border-[#E7E3DC] bg-white rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm p-2" accept=".pdf,.xls,.xlsx,.doc,.docx">
                            <div class="text-[10px] text-[#94A3B8] mt-1 italic">Opsional. Maksimal ukuran file: 10MB.</div>
                            @error('file_laporan')<div class="text-[11px] text-[#DC2626] mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Lampiran Tambahan (Opsional)</label>
                            <input type="file" name="lampiran_tambahan" class="w-full border-[#E7E3DC] bg-white rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm p-2" accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png">
                            <div class="text-[10px] text-[#94A3B8] mt-1 italic">Opsional. (PDF, ZIP, JPG, dll).</div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Dokumentasi (Foto-foto)</label>
                            <input type="file" name="dokumentasi[]" multiple accept="image/*" class="w-full border-[#E7E3DC] bg-white rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm p-2">
                            <div class="text-[10px] text-[#94A3B8] mt-1 italic">Opsional. Bisa pilih banyak foto (Maks 10MB per file).</div>
                            @error('dokumentasi')<div class="text-[11px] text-[#DC2626] mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Ringkasan Kemajuan Pekerjaan</label>
                    <textarea name="ringkasan_kemajuan" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm" rows="3" placeholder="Deskripsikan pekerjaan utama yang diselesaikan minggu ini...">{{ old('ringkasan_kemajuan') }}</textarea>
                </div>
                <div class="mb-8">
                    <label class="block text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-2">Kendala & Hambatan</label>
                    <textarea name="kendala" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm" rows="2" placeholder="Catat kendala cuaca, material, atau teknis di lapangan (jika ada)...">{{ old('kendala') }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-[#E7E3DC]">
                    <button type="submit" name="action" value="draft" class="bg-white border border-[#CBD5E1] text-[#475569] px-6 py-2.5 rounded font-bold text-sm hover:bg-[#F1F5F9] transition shadow-sm">
                        Simpan Draft
                    </button>
                    <button type="submit" name="action" value="submit" class="bg-[#15803D] text-white px-6 py-2.5 rounded font-bold text-sm hover:bg-opacity-90 transition shadow-sm" onclick="return confirm('Kirim laporan ini ke Konsultan Pengawas?')">
                        Kirim ke Konsultan
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const progressMingguIni = document.getElementById('progress_minggu_ini');
            const rencanaMingguIni = document.getElementById('rencana_minggu_ini');
            const bobotLaluRealisasi = parseFloat(document.getElementById('bobot_lalu_realisasi').value) || 0;
            const bobotLaluRencana = parseFloat(document.getElementById('bobot_lalu_rencana').value) || 0;
            
            const totalRealisasiEl = document.getElementById('total_realisasi');
            const totalRencanaEl = document.getElementById('total_rencana');
            const deviasiEl = document.getElementById('deviasi_text');

            function calculate() {
                const prog = parseFloat(progressMingguIni.value) || 0;
                const renc = parseFloat(rencanaMingguIni.value) || 0;
                
                const totReal = bobotLaluRealisasi + prog;
                const totRenc = bobotLaluRencana + renc;
                const dev = totReal - totRenc;
                
                totalRealisasiEl.textContent = totReal.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                totalRencanaEl.textContent = totRenc.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                deviasiEl.textContent = dev.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                
                if (dev >= 0) {
                    deviasiEl.className = 'text-[#15803D]';
                } else {
                    deviasiEl.className = 'text-[#DC2626]';
                }
            }

            progressMingguIni.addEventListener('input', calculate);
            rencanaMingguIni.addEventListener('input', calculate);

            // Auto-calculate Tanggal Selesai (Mulai + 6 hari)
            const tanggalMulaiEl = document.getElementById('tanggal_mulai');
            const tanggalSelesaiEl = document.getElementById('tanggal_selesai');

            tanggalMulaiEl.addEventListener('change', function() {
                if (this.value) {
                    const date = new Date(this.value);
                    date.setDate(date.getDate() + 6);
                    const yyyy = date.getFullYear();
                    const mm = String(date.getMonth() + 1).padStart(2, '0');
                    const dd = String(date.getDate()).padStart(2, '0');
                    tanggalSelesaiEl.value = `${yyyy}-${mm}-${dd}`;
                } else {
                    tanggalSelesaiEl.value = '';
                }
            });
        });
    </script>
</x-app-layout>
