<x-app-layout>
    <x-slot name="title">Daftar Laporan Mingguan</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Laporan Kemajuan Mingguan</h1>
                <p class="text-sm text-[#64748B] mt-1">Laporan Kemajuan Proyek Mingguan.</p>
            </div>
            
            @if(auth()->user()->isKontraktor())
            <a href="{{ route('laporan-mingguan.create') }}" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity shadow-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Input Laporan Mingguan
            </a>
            @endif
        </div>

        @if(session('success'))
            <div class="bg-[#F0FDF4] border-l-4 border-[#15803D] text-[#15803D] p-4 mb-6 rounded shadow-sm text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-[#FEF2F2] border-l-4 border-[#DC2626] text-[#DC2626] p-4 mb-6 rounded shadow-sm text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white border border-[#E7E3DC] rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-[#64748B] uppercase bg-[#F8FAFC] border-b border-[#E7E3DC]">
                        <tr>
                            <th class="px-6 py-3 font-semibold text-center">Mg. Ke-</th>
                            <th class="px-6 py-3 font-semibold">Periode</th>
                            <th class="px-6 py-3 font-semibold">Dokumen</th>
                            <th class="px-6 py-3 font-semibold text-center">Status</th>
                            <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporans as $l)
                        <tr class="border-b border-[#E7E3DC] even:bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors">
                            <td class="px-6 py-4 text-center font-bold text-lg text-[#0F172B]">{{ $l->minggu_ke }}</td>
                            <td class="px-6 py-4 text-[#0F172B] whitespace-nowrap">
                                {{ $l->tanggal_mulai->format('d/m/Y') }} — {{ $l->tanggal_selesai->format('d/m/Y') }}
                                <div class="text-[10px] text-[#64748B] mt-0.5">{{ $l->proyek->nama_proyek }}</div>
                            </td>
                            
                            <td class="px-6 py-4">
                                @if($l->file_laporan)
                                    <a href="{{ Storage::url($l->file_laporan) }}" target="_blank" class="text-[#1D4ED8] hover:underline inline-flex items-center gap-1 font-semibold text-xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Unduh
                                    </a>
                                @else
                                    <span class="text-xs text-gray-500 italic">Tidak ada dokumen</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($l->status == 'approved')
                                    <span class="inline-block px-3 py-1 bg-[#F0FDF4] text-[#15803D] border border-[#BBF7D0] rounded-full text-[10px] font-bold uppercase tracking-wider">Approved</span>
                                @elseif($l->status == 'verified')
                                    <span class="inline-block px-3 py-1 bg-[#EFF6FF] text-[#1D4ED8] border border-[#BFDBFE] rounded-full text-[10px] font-bold uppercase tracking-wider">Verified</span>
                                @elseif($l->status == 'submitted')
                                    <span class="inline-block px-3 py-1 bg-[#FFF8E1] text-[#F57F17] border border-[#FFE082] rounded-full text-[10px] font-bold uppercase tracking-wider">Submitted</span>
                                @elseif($l->status == 'rejected')
                                    <span class="inline-block px-3 py-1 bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] rounded-full text-[10px] font-bold uppercase tracking-wider">Rejected</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-[#F1F5F9] text-[#475569] border border-[#CBD5E1] rounded-full text-[10px] font-bold uppercase tracking-wider">Draft</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <div class="flex gap-2 justify-center items-center">
                                    <a href="{{ route('laporan-mingguan.show', $l) }}" class="text-xs text-[#0F172B] hover:text-[#FFA000] font-bold bg-[#F1F5F9] px-3 py-1.5 rounded transition">
                                        Lihat Detail
                                    </a>

                                    @if(auth()->user()->isKonsultan() && $l->status === 'submitted')
                                        <span class="w-2.5 h-2.5 rounded-full bg-[#F57F17] animate-pulse absolute -ml-1 -mt-1" title="Butuh Verifikasi"></span>
                                    @endif

                                    @if(auth()->user()->isPPK() && $l->status === 'verified')
                                        <span class="w-2.5 h-2.5 rounded-full bg-[#F57F17] animate-pulse absolute -ml-1 -mt-1" title="Butuh Approval"></span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-[#64748B] italic">
                                Belum ada laporan mingguan. 
                                @if(auth()->user()->isKontraktor())
                                    <a href="{{ route('laporan-mingguan.create') }}" class="text-[#FFA000] hover:underline">Buat laporan pertama →</a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4">{{ $laporans->links() }}</div>

    </div>
</x-app-layout>
