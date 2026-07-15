<x-app-layout>
    <x-slot name="title">Detail Laporan Bulanan</x-slot>

    <div class="w-full font-sans bg-white p-6 md:p-10 min-h-[calc(100vh-64px)] md:min-h-[calc(100vh-80px)]">
        
        <div class="flex justify-between items-center border-b border-gray-200 pb-6 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Detail Laporan Bulanan</h1>
                <p class="text-sm text-[#64748B] mt-1">{{ $laporanBulanan->proyek->nama_proyek ?? '-' }} | {{ $laporanBulanan->bulan_label }} {{ $laporanBulanan->tahun }}</p>
            </div>
            <div>
                <a href="{{ route('laporan-bulanan.index') }}" class="text-sm text-gray-500 hover:text-gray-900 border border-gray-300 px-4 py-2 rounded-md">← Kembali</a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-600 text-green-700 p-4 mb-6 rounded shadow-sm text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-600 text-red-700 p-4 mb-6 rounded shadow-sm text-sm">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-600 text-red-700 p-4 mb-6 rounded shadow-sm text-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Kiri: Detail Data -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status Bar -->
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 flex items-center justify-between">
                    <div>
                        <div class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Status Laporan</div>
                        <div class="text-lg font-black text-[#0F172B]">{{ strtoupper($laporanBulanan->status) }}</div>
                    </div>
                    <div>
                        @if($laporanBulanan->status == 'approved')
                            <span class="px-4 py-2 bg-green-100 text-green-800 rounded font-bold text-sm">SELESAI (APPROVED)</span>
                        @elseif($laporanBulanan->status == 'rejected')
                            <span class="px-4 py-2 bg-red-100 text-red-800 rounded font-bold text-sm">DITOLAK</span>
                        @else
                            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded font-bold text-sm">DALAM PROSES</span>
                        @endif
                    </div>
                </div>

                <!-- Progress Data -->
                <div class="bg-white p-6 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-bold text-[#0F172B] mb-4">Pencapaian Fisik Kumulatif</h3>
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-md border border-blue-100 text-center">
                            <div class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-1">Rencana</div>
                            <div class="text-2xl font-black text-blue-900">{{ $laporanBulanan->bobot_rencana }}%</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-md border border-green-100 text-center">
                            <div class="text-xs text-green-600 font-bold uppercase tracking-wider mb-1">Realisasi</div>
                            <div class="text-2xl font-black text-green-900">{{ $laporanBulanan->bobot_realisasi }}%</div>
                        </div>
                        <div class="bg-{{ $laporanBulanan->deviasi >= 0 ? 'green' : 'red' }}-50 p-4 rounded-md border border-{{ $laporanBulanan->deviasi >= 0 ? 'green' : 'red' }}-100 text-center">
                            <div class="text-xs text-{{ $laporanBulanan->deviasi >= 0 ? 'green' : 'red' }}-600 font-bold uppercase tracking-wider mb-1">Deviasi</div>
                            <div class="text-2xl font-black text-{{ $laporanBulanan->deviasi >= 0 ? 'green' : 'red' }}-900">{{ $laporanBulanan->deviasi >= 0 ? '+' : '' }}{{ $laporanBulanan->deviasi }}%</div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Ringkasan Kemajuan</div>
                            <div class="text-sm text-gray-800 bg-gray-50 p-3 rounded border border-gray-100 min-h-[60px]">
                                {!! nl2br(e($laporanBulanan->ringkasan_kemajuan ?: '-')) !!}
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Kendala & Permasalahan</div>
                            <div class="text-sm text-gray-800 bg-gray-50 p-3 rounded border border-gray-100 min-h-[60px]">
                                {!! nl2br(e($laporanBulanan->kendala ?: '-')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Aksi & Dokumen -->
            <div class="space-y-6">
                <!-- File Lampiran -->
                <div class="bg-white p-6 rounded-lg border border-gray-200">
                    <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider mb-4 border-b pb-2">Dokumen Pendukung</h3>
                    @if($laporanBulanan->file_laporan)
                        <a href="{{ asset('storage/'.$laporanBulanan->file_laporan) }}" target="_blank" class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded text-blue-700 hover:bg-blue-100 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="text-sm font-bold truncate">Lihat Dokumen Laporan (PDF)</span>
                        </a>
                    @else
                        <div class="text-sm text-gray-500 italic p-3 bg-gray-50 rounded text-center">Tidak ada dokumen lampiran.</div>
                    @endif
                </div>

                <!-- Audit Trail / Catatan -->
                <div class="bg-white p-6 rounded-lg border border-gray-200">
                    <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider mb-4 border-b pb-2">Catatan Verifikasi</h3>
                    
                    <div class="mb-4">
                        <div class="text-xs text-gray-500 font-bold mb-1">Catatan Konsultan Pengawas</div>
                        <div class="text-sm p-3 rounded bg-gray-50 border border-gray-100">
                            {{ $laporanBulanan->catatan_konsultan ?: 'Belum ada catatan.' }}
                        </div>
                        @if($laporanBulanan->verified_at)
                            <div class="text-[10px] text-gray-400 mt-1">Diverifikasi oleh: {{ $laporanBulanan->verifikator->name ?? '-' }} pada {{ $laporanBulanan->verified_at->format('d M Y H:i') }}</div>
                        @endif
                    </div>

                    <div>
                        <div class="text-xs text-gray-500 font-bold mb-1">Catatan PPK / Owner</div>
                        <div class="text-sm p-3 rounded bg-gray-50 border border-gray-100">
                            {{ $laporanBulanan->catatan_ppk ?: 'Belum ada catatan.' }}
                        </div>
                        @if($laporanBulanan->approved_at)
                            <div class="text-[10px] text-gray-400 mt-1">Disetujui oleh: {{ $laporanBulanan->approver->name ?? '-' }} pada {{ $laporanBulanan->approved_at->format('d M Y H:i') }}</div>
                        @endif
                    </div>
                </div>

                <!-- Action Panel -->
                @if(in_array($laporanBulanan->status, ['draft', 'rejected']) && auth()->user()->isKontraktor())
                    <div class="bg-[#1E3A8A] p-6 rounded-lg shadow-md text-white">
                        <h3 class="text-sm font-bold text-[#FFB800] uppercase tracking-wider mb-2">Tindakan Anda</h3>
                        <p class="text-xs text-gray-300 mb-4">Laporan masih dalam status Draft. Silakan periksa kembali data di atas. Jika sudah sesuai, kirim ke Konsultan untuk diverifikasi.</p>
                        <form method="POST" action="{{ route('laporan-bulanan.submit', $laporanBulanan) }}" onsubmit="return confirm('Kirim Laporan ini ke Konsultan?')">
                            @csrf
                            <button type="submit" class="w-full bg-[#FFB800] text-[#1E3A8A] py-2 rounded text-sm font-bold hover:bg-yellow-400 transition">
                                Kirim Laporan ke Konsultan
                            </button>
                        </form>
                    </div>
                @endif

                @if($laporanBulanan->status == 'submitted' && auth()->user()->isKonsultan())
                    <div class="bg-[#1E3A8A] p-6 rounded-lg shadow-md text-white">
                        <h3 class="text-sm font-bold text-[#FFB800] uppercase tracking-wider mb-4">Tindakan Verifikasi</h3>
                        
                        <form method="POST" action="{{ route('laporan-bulanan.verify', $laporanBulanan) }}" class="mb-3">
                            @csrf
                            <textarea name="catatan_konsultan" rows="2" class="w-full rounded text-sm text-gray-900 placeholder-gray-500 border-0 focus:ring-2 focus:ring-[#FFB800] mb-3" placeholder="Opsional: Tambahkan catatan verifikasi..."></textarea>
                            <button type="submit" onclick="return confirm('Verifikasi laporan ini?')" class="w-full bg-green-500 text-white py-2 rounded text-sm font-bold hover:bg-green-600 transition">
                                Verifikasi (Setuju)
                            </button>
                        </form>

                        <form method="POST" action="{{ route('laporan-bulanan.reject', $laporanBulanan) }}">
                            @csrf
                            <div class="border-t border-white/20 pt-3 mt-1">
                                <textarea name="catatan_konsultan" rows="2" required class="w-full rounded text-sm text-gray-900 placeholder-gray-500 border-0 focus:ring-2 focus:ring-red-400 mb-3" placeholder="Wajib: Alasan penolakan..."></textarea>
                                <button type="submit" onclick="return confirm('Tolak laporan ini dan kembalikan ke Kontraktor?')" class="w-full bg-red-500 text-white py-2 rounded text-sm font-bold hover:bg-red-600 transition">
                                    Tolak / Kembalikan
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                @if($laporanBulanan->status == 'verified' && auth()->user()->isPPK())
                    <div class="bg-[#1E3A8A] p-6 rounded-lg shadow-md text-white">
                        <h3 class="text-sm font-bold text-[#FFB800] uppercase tracking-wider mb-4">Tindakan Approval</h3>
                        
                        <form method="POST" action="{{ route('laporan-bulanan.approve', $laporanBulanan) }}" class="mb-3">
                            @csrf
                            <textarea name="catatan_ppk" rows="2" class="w-full rounded text-sm text-gray-900 placeholder-gray-500 border-0 focus:ring-2 focus:ring-[#FFB800] mb-3" placeholder="Opsional: Tambahkan catatan persetujuan..."></textarea>
                            <button type="submit" onclick="return confirm('Setujui laporan ini secara final?')" class="w-full bg-green-500 text-white py-2 rounded text-sm font-bold hover:bg-green-600 transition">
                                Setujui (Approve)
                            </button>
                        </form>

                        <form method="POST" action="{{ route('laporan-bulanan.reject', $laporanBulanan) }}">
                            @csrf
                            <div class="border-t border-white/20 pt-3 mt-1">
                                <textarea name="catatan_ppk" rows="2" required class="w-full rounded text-sm text-gray-900 placeholder-gray-500 border-0 focus:ring-2 focus:ring-red-400 mb-3" placeholder="Wajib: Alasan penolakan..."></textarea>
                                <button type="submit" onclick="return confirm('Tolak laporan ini?')" class="w-full bg-red-500 text-white py-2 rounded text-sm font-bold hover:bg-red-600 transition">
                                    Tolak / Kembalikan
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
