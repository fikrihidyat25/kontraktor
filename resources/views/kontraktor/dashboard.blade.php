<x-app-layout>
    <x-slot name="title">Dashboard Kontraktor</x-slot>

    @if($proyek)
    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        @if(session('success'))
        <div class="mb-6 bg-green-50 text-green-800 border border-green-200 rounded-sm p-4 text-sm font-medium">
            {{ session('success') }}
        </div>
        @endif

        <!-- PROYEK AKTIF INFO -->
        <div class="bg-[#F9FAFB] border border-[#E5E7EB] rounded-sm p-6 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-sm">
            <div>
                <div class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-[#FFB800] text-[#1E3A8A] uppercase tracking-wider mb-2">Proyek Aktif</div>
                <h2 class="text-xl font-bold text-[#1E3A8A]">{{ $proyek->nama_proyek }}</h2>
                <p class="text-sm text-gray-500 mt-1 font-medium">{{ $proyek->nomor_kontrak }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Nilai Kontrak</p>
                <p class="text-lg font-bold text-[#1E3A8A] mt-1">Rp {{ number_format($proyek->nilai_kontrak, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- QUICK ACTION BUTTONS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <a href="{{ route('laporan-harian.create') }}" class="flex items-center justify-center gap-2 bg-[#1E3A8A] text-[#FFB800] font-bold text-sm rounded-sm px-6 py-4 hover:bg-[#152e70] transition-colors border border-transparent shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Buat Laporan Harian
            </a>
            <a href="{{ route('uang-muka.index') }}" class="flex items-center justify-center gap-2 bg-white border border-[#E5E7EB] text-[#1E3A8A] font-bold text-sm rounded-sm px-6 py-4 hover:bg-gray-50 transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Pengajuan Uang Muka
            </a>
            <a href="{{ route('kerja-tambah-kurang.index') }}" class="flex items-center justify-center gap-2 bg-white border border-[#E5E7EB] text-[#1E3A8A] font-bold text-sm rounded-sm px-6 py-4 hover:bg-gray-50 transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Usulan KTK
            </a>
        </div>

        <!-- STATS LAPORAN -->
        <div class="mb-2 text-sm font-bold text-[#1E3A8A] uppercase tracking-wider">Statistik Laporan Harian</div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['draft'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Draft</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['submitted'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Submitted</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['verified'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Verified</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['approved'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Approved</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['rejected'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Rejected</div>
            </div>
        </div>

        <!-- Recent Laporan Tabel -->
        <div class="bg-white rounded-sm shadow-sm border border-[#E5E7EB] overflow-hidden">
            <div class="bg-gray-50 border-b border-[#E5E7EB] px-6 py-4 flex justify-between items-center">
                <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">7 Laporan Harian Terakhir</h3>
                <a href="{{ route('laporan-harian.index') }}" class="text-xs font-bold text-[#1E3A8A] hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="text-[11px] text-gray-500 uppercase bg-white border-b border-[#E5E7EB]">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                            <th class="px-6 py-4 font-semibold">Cuaca</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB]">
                        @forelse($recentLaporan as $l)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $l->tanggal->isoFormat('dddd, D MMM Y') }}</td>
                            <td class="px-6 py-4">{{ $l->cuaca_label }}</td>
                            <td class="px-6 py-4">
                                @if($l->status == 'approved')
                                    <span class="inline-block px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-sm text-[10px] font-bold uppercase tracking-wider">Approved</span>
                                @elseif($l->status == 'verified')
                                    <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-sm text-[10px] font-bold uppercase tracking-wider">Verified</span>
                                @elseif($l->status == 'submitted')
                                    <span class="inline-block px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-sm text-[10px] font-bold uppercase tracking-wider">Submitted</span>
                                @elseif($l->status == 'rejected')
                                    <span class="inline-block px-3 py-1 bg-red-50 text-red-700 border border-red-200 rounded-sm text-[10px] font-bold uppercase tracking-wider">Rejected</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-gray-50 text-gray-700 border border-gray-200 rounded-sm text-[10px] font-bold uppercase tracking-wider">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('laporan-harian.show', $l) }}" class="text-xs font-bold text-[#1E3A8A] hover:underline">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">Belum ada laporan harian.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
    @else
    <div class="w-full px-4 md:px-8 py-8 font-sans">
        <!-- NO PROYEK -->
        <div class="bg-gray-50 border border-gray-200 rounded-sm p-8 text-center mt-8 shadow-sm">
            <div class="w-16 h-16 bg-gray-200 rounded-sm flex items-center justify-center mx-auto mb-4 text-gray-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Proyek Aktif</h3>
            <p class="text-gray-600 max-w-md mx-auto">Anda belum memiliki proyek aktif yang ditugaskan kepada Anda.</p>
        </div>
    </div>
    @endif
</x-app-layout>
