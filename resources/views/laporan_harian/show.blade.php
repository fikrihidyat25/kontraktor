<x-app-layout>
    <x-slot name="title">Detail Laporan Harian</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-8 border-b border-[#E7E3DC] pb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Detail Laporan Harian</h1>
                <p class="text-sm text-[#64748B] mt-1">{{ $laporanHarian->proyek->nama_proyek }} — {{ $laporanHarian->tanggal->isoFormat('D MMMM Y') }}</p>
            </div>
            <div>
                <a href="{{ route('laporan-harian.index') }}" class="text-sm font-semibold text-[#64748B] hover:text-[#0F172B] bg-white border border-[#E7E3DC] px-4 py-2 rounded shadow-sm">
                    Kembali ke Daftar
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-[#F0FDF4] border-l-4 border-[#15803D] text-[#15803D] p-4 mb-6 rounded shadow-sm text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-[#FEF2F2] border-l-4 border-[#DC2626] text-[#DC2626] p-4 mb-6 rounded shadow-sm text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <!-- INFO UMUM -->
        <div class="bg-white rounded-lg shadow-sm border border-[#E7E3DC] p-6 mb-6">
            <h2 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider mb-4 border-b border-[#E7E3DC] pb-2">Informasi Umum</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Status</div>
                    @if($laporanHarian->status == 'approved')
                        <span class="inline-block px-3 py-1 bg-[#F0FDF4] text-[#15803D] border border-[#BBF7D0] rounded-full text-[11px] font-bold uppercase tracking-wider">Approved</span>
                    @elseif($laporanHarian->status == 'verified')
                        <span class="inline-block px-3 py-1 bg-[#EFF6FF] text-[#1D4ED8] border border-[#BFDBFE] rounded-full text-[11px] font-bold uppercase tracking-wider">Verified</span>
                    @elseif($laporanHarian->status == 'submitted')
                        <span class="inline-block px-3 py-1 bg-[#FFF8E1] text-[#F57F17] border border-[#FFE082] rounded-full text-[11px] font-bold uppercase tracking-wider">Submitted</span>
                    @elseif($laporanHarian->status == 'rejected')
                        <span class="inline-block px-3 py-1 bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] rounded-full text-[11px] font-bold uppercase tracking-wider">Rejected</span>
                    @else
                        <span class="inline-block px-3 py-1 bg-[#F1F5F9] text-[#475569] border border-[#CBD5E1] rounded-full text-[11px] font-bold uppercase tracking-wider">Draft</span>
                    @endif
                </div>
                <div>
                    <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Cuaca</div>
                    <div class="text-sm font-semibold text-[#0F172B]">{{ $laporanHarian->cuaca_label }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Kontraktor</div>
                    <div class="text-sm font-semibold text-[#0F172B]">{{ $laporanHarian->kontraktor->name }}</div>
                </div>
                <div>
                    <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider mb-1">Catatan Lapangan</div>
                    <div class="text-sm text-[#0F172B]">{{ $laporanHarian->catatan ?? 'Tidak ada catatan.' }}</div>
                </div>
            </div>
        </div>

        <!-- DETAIL DATA -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Tenaga Kerja -->
            <div class="bg-white rounded-lg shadow-sm border border-[#E7E3DC] overflow-hidden">
                <div class="bg-[#F8FAFC] border-b border-[#E7E3DC] px-5 py-3">
                    <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">A. Tenaga Kerja</h3>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[11px] text-[#64748B] uppercase bg-white border-b border-[#E7E3DC]">
                            <tr><th class="px-5 py-3 font-semibold">Klasifikasi</th><th class="px-5 py-3 font-semibold text-center">Jumlah</th></tr>
                        </thead>
                        <tbody>
                            @forelse($laporanHarian->tenagaKerjas as $tk)
                            <tr class="border-b border-[#E7E3DC] hover:bg-[#F8FAFC]">
                                <td class="px-5 py-3 text-[#0F172B]">
                                    <div class="font-medium">{{ $tk->klasifikasi_label }}</div>
                                    @if($tk->keterangan)<div class="text-[11px] text-[#64748B]">{{ $tk->keterangan }}</div>@endif
                                </td>
                                <td class="px-5 py-3 text-center font-bold text-[#FFA000]">{{ $tk->jumlah }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="px-5 py-4 text-center text-[#64748B] italic">Tidak ada data tenaga kerja.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Peralatan -->
            <div class="bg-white rounded-lg shadow-sm border border-[#E7E3DC] overflow-hidden">
                <div class="bg-[#F8FAFC] border-b border-[#E7E3DC] px-5 py-3">
                    <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">B. Peralatan</h3>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[11px] text-[#64748B] uppercase bg-white border-b border-[#E7E3DC]">
                            <tr><th class="px-5 py-3 font-semibold">Jenis Alat</th><th class="px-5 py-3 font-semibold text-center">Jml</th><th class="px-5 py-3 font-semibold text-center">Jam</th></tr>
                        </thead>
                        <tbody>
                            @forelse($laporanHarian->peralatans as $p)
                            <tr class="border-b border-[#E7E3DC] hover:bg-[#F8FAFC]">
                                <td class="px-5 py-3 text-[#0F172B] font-medium">{{ $p->jenis_alat }}</td>
                                <td class="px-5 py-3 text-center font-bold text-[#FFA000]">{{ $p->jumlah }}</td>
                                <td class="px-5 py-3 text-center text-[#64748B]">{{ $p->jam_operasi }} jam</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-5 py-4 text-center text-[#64748B] italic">Tidak ada data peralatan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Material -->
            <div class="bg-white rounded-lg shadow-sm border border-[#E7E3DC] overflow-hidden">
                <div class="bg-[#F8FAFC] border-b border-[#E7E3DC] px-5 py-3">
                    <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">C. Material Terpakai</h3>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[11px] text-[#64748B] uppercase bg-white border-b border-[#E7E3DC]">
                            <tr><th class="px-5 py-3 font-semibold">Jenis Material</th><th class="px-5 py-3 font-semibold text-right">Datang</th><th class="px-5 py-3 font-semibold text-right">Pakai</th></tr>
                        </thead>
                        <tbody>
                            @forelse($laporanHarian->materials as $m)
                            <tr class="border-b border-[#E7E3DC] hover:bg-[#F8FAFC]">
                                <td class="px-5 py-3 text-[#0F172B] font-medium">{{ $m->jenis_material }}</td>
                                <td class="px-5 py-3 text-right text-[#64748B]">{{ number_format($m->kuantitas_datang, 2, ',', '.') }} {{ $m->satuan }}</td>
                                <td class="px-5 py-3 text-right font-bold text-[#15803D]">{{ number_format($m->kuantitas_digunakan, 2, ',', '.') }} {{ $m->satuan }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-5 py-4 text-center text-[#64748B] italic">Tidak ada data material.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Realisasi -->
            <div class="bg-white rounded-lg shadow-sm border border-[#E7E3DC] overflow-hidden">
                <div class="bg-[#F8FAFC] border-b border-[#E7E3DC] px-5 py-3">
                    <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">D. Realisasi Biaya & Bobot</h3>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-[11px] text-[#64748B] uppercase bg-white border-b border-[#E7E3DC]">
                            <tr><th class="px-5 py-3 font-semibold">Divisi</th><th class="px-5 py-3 font-semibold text-right">Nilai (Rp)</th><th class="px-5 py-3 font-semibold text-right">Bobot</th></tr>
                        </thead>
                        <tbody>
                            @forelse($laporanHarian->realisasiBiayas as $r)
                            <tr class="border-b border-[#E7E3DC] hover:bg-[#F8FAFC]">
                                <td class="px-5 py-3 text-[#0F172B] font-medium">{{ $r->divisi_pekerjaan }}</td>
                                <td class="px-5 py-3 text-right text-[#64748B]">{{ number_format($r->nilai_realisasi, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-right font-bold text-[#FFA000]">{{ number_format($r->bobot_fisik, 2, ',', '.') }}%</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-5 py-4 text-center text-[#64748B] italic">Tidak ada data realisasi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ==========================================
             RBAC ACTION PANELS (SINGLE INTERFACE LOGIC)
             ========================================== -->
             
        <!-- KONSULTAN ACTION: VERIFY/REJECT -->
        @if(auth()->user()->isKonsultan() && $laporanHarian->status === 'submitted')
        <div class="bg-white rounded-lg shadow-sm border-2 border-[#FFA000] p-6 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 bg-[#FFA000] text-[#0F172B] text-[10px] font-bold px-3 py-1 uppercase tracking-wider rounded-bl-lg">Audit Mode</div>
            <h2 class="text-lg font-bold text-[#0F172B] mb-2">Audit Konsultan Pengawas</h2>
            <p class="text-sm text-[#64748B] mb-6">Verifikasi kesesuaian data input Kontraktor dengan kondisi lapangan.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-[#F0FDF4] border border-[#BBF7D0] p-5 rounded-lg">
                    <h3 class="text-sm font-bold text-[#15803D] mb-4">Verifikasi & Teruskan ke PPK</h3>
                    <form method="POST" action="{{ route('laporan-harian.verify', $laporanHarian) }}">
                        @csrf
                        <textarea name="catatan_konsultan" rows="3" class="w-full border-[#BBF7D0] rounded focus:ring-[#15803D] focus:border-[#15803D] text-sm mb-4 bg-white" placeholder="Catatan audit (opsional)..."></textarea>
                        <button type="submit" class="w-full bg-[#15803D] text-white px-4 py-2 rounded text-sm font-bold hover:bg-opacity-90 shadow-sm">
                            Validasi Laporan
                        </button>
                    </form>
                </div>
                <div class="bg-[#FEF2F2] border border-[#FECACA] p-5 rounded-lg">
                    <h3 class="text-sm font-bold text-[#DC2626] mb-4">Tolak & Kembalikan ke Kontraktor</h3>
                    <form method="POST" action="{{ route('laporan-harian.reject-konsultan', $laporanHarian) }}">
                        @csrf
                        <textarea name="catatan_konsultan" rows="3" required class="w-full border-[#FECACA] rounded focus:ring-[#DC2626] focus:border-[#DC2626] text-sm mb-4 bg-white" placeholder="Alasan penolakan (Wajib)..."></textarea>
                        <button type="submit" class="w-full bg-[#DC2626] text-white px-4 py-2 rounded text-sm font-bold hover:bg-opacity-90 shadow-sm">
                            Tolak Laporan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- PPK ACTION: APPROVE/REJECT -->
        @if(auth()->user()->isPPK() && $laporanHarian->status === 'verified')
        <div class="bg-white rounded-lg shadow-sm border-2 border-[#1D4ED8] p-6 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 bg-[#1D4ED8] text-white text-[10px] font-bold px-3 py-1 uppercase tracking-wider rounded-bl-lg">Approval Mode</div>
            <h2 class="text-lg font-bold text-[#0F172B] mb-2">Final Approval Owner / PPK</h2>
            <p class="text-sm text-[#64748B] mb-6">Laporan ini telah diaudit Konsultan Pengawas. Berikan persetujuan final.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-[#F0FDF4] border border-[#BBF7D0] p-5 rounded-lg">
                    <h3 class="text-sm font-bold text-[#15803D] mb-4">Setujui Progress Ini</h3>
                    <form method="POST" action="{{ route('laporan-harian.approve', $laporanHarian) }}">
                        @csrf
                        <textarea name="catatan_ppk" rows="3" class="w-full border-[#BBF7D0] rounded focus:ring-[#15803D] focus:border-[#15803D] text-sm mb-4 bg-white" placeholder="Catatan approval (opsional)..."></textarea>
                        <button type="submit" class="w-full bg-[#15803D] text-white px-4 py-2 rounded text-sm font-bold hover:bg-opacity-90 shadow-sm">
                            Approve Laporan
                        </button>
                    </form>
                </div>
                <div class="bg-[#FEF2F2] border border-[#FECACA] p-5 rounded-lg">
                    <h3 class="text-sm font-bold text-[#DC2626] mb-4">Batalkan / Kembalikan</h3>
                    <form method="POST" action="{{ route('laporan-harian.reject-ppk', $laporanHarian) }}">
                        @csrf
                        <textarea name="catatan_ppk" rows="3" required class="w-full border-[#FECACA] rounded focus:ring-[#DC2626] focus:border-[#DC2626] text-sm mb-4 bg-white" placeholder="Alasan pembatalan (Wajib)..."></textarea>
                        <button type="submit" class="w-full bg-[#DC2626] text-white px-4 py-2 rounded text-sm font-bold hover:bg-opacity-90 shadow-sm">
                            Reject Laporan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- HISTORY & TRACKING -->
        <div class="bg-white rounded-lg shadow-sm border border-[#E7E3DC] p-6">
            <h2 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider mb-4 border-b border-[#E7E3DC] pb-2">Riwayat Review</h2>
            <div class="space-y-4">
                @if($laporanHarian->catatan_konsultan)
                    <div class="bg-[#F8FAFC] border border-[#E7E3DC] p-4 rounded text-sm">
                        <div class="font-bold text-[#0F172B] mb-1">Catatan Audit Konsultan</div>
                        <div class="text-[#64748B] italic">"{{ $laporanHarian->catatan_konsultan }}"</div>
                        <div class="text-[10px] text-[#94A3B8] mt-2">{{ $laporanHarian->verifiedBy->name ?? 'Konsultan' }} — {{ $laporanHarian->verified_at?->format('d/m/Y H:i') }}</div>
                    </div>
                @endif
                
                @if($laporanHarian->catatan_ppk)
                    <div class="bg-[#F8FAFC] border border-[#E7E3DC] p-4 rounded text-sm">
                        <div class="font-bold text-[#0F172B] mb-1">Catatan Keputusan PPK</div>
                        <div class="text-[#64748B] italic">"{{ $laporanHarian->catatan_ppk }}"</div>
                        <div class="text-[10px] text-[#94A3B8] mt-2">{{ $laporanHarian->approvedBy->name ?? 'PPK' }} — {{ $laporanHarian->approved_at?->format('d/m/Y H:i') }}</div>
                    </div>
                @endif

                @if(!$laporanHarian->catatan_konsultan && !$laporanHarian->catatan_ppk)
                    <div class="text-sm text-[#64748B] italic">Belum ada riwayat review pada laporan ini.</div>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
