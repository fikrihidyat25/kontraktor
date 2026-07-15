<x-app-layout>
    <x-slot name="title">Daftar Laporan Bulanan</x-slot>

    <div class="w-full font-sans">
        
        <div class="flex justify-between items-center mb-8 bg-white p-6 md:px-10 border-b border-gray-200">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Laporan Kemajuan Bulanan</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar rekapan progres fisik proyek secara bulanan.</p>
            </div>
            
            @if(auth()->user()->isKontraktor())
            <a href="{{ route('laporan-bulanan.create') }}" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity flex items-center">
                + Input Laporan Baru
            </a>
            @endif
        </div>

        <div class="p-6 md:p-10 min-h-[calc(100vh-200px)]">
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

            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-[#64748B] uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Bulan</th>
                                <th class="px-6 py-4 font-semibold">Proyek</th>
                                @if(!auth()->user()->isKontraktor())
                                    <th class="px-6 py-4 font-semibold">Kontraktor</th>
                                @endif
                                <th class="px-6 py-4 font-semibold text-right">Rencana</th>
                                <th class="px-6 py-4 font-semibold text-right">Realisasi</th>
                                <th class="px-6 py-4 font-semibold text-right">Deviasi</th>
                                <th class="px-6 py-4 font-semibold text-center">Status</th>
                                <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($laporans as $l)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-[#0F172B] whitespace-nowrap font-medium">{{ $l->bulan_label }} {{ $l->tahun }}</td>
                                <td class="px-6 py-4 text-[#64748B] text-xs max-w-[200px] truncate" title="{{ $l->proyek->nama_proyek ?? '-' }}">{{ $l->proyek->nama_proyek ?? '—' }}</td>
                                
                                @if(!auth()->user()->isKontraktor())
                                    <td class="px-6 py-4 text-[#64748B] text-xs">{{ $l->kontraktor->name ?? '—' }}</td>
                                @endif

                                <td class="px-6 py-4 text-right font-medium text-gray-700">{{ $l->bobot_rencana }}%</td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900">{{ $l->bobot_realisasi }}%</td>
                                <td class="px-6 py-4 text-right font-bold {{ $l->deviasi >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $l->deviasi >= 0 ? '+' : '' }}{{ $l->deviasi }}%</td>
                                
                                <td class="px-6 py-4 text-center">
                                    @if($l->status == 'approved')
                                        <span class="inline-block px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded text-[10px] font-bold uppercase tracking-wider">Approved</span>
                                    @elseif($l->status == 'verified')
                                        <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded text-[10px] font-bold uppercase tracking-wider">Verified</span>
                                    @elseif($l->status == 'submitted')
                                        <span class="inline-block px-3 py-1 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded text-[10px] font-bold uppercase tracking-wider">Submitted</span>
                                    @elseif($l->status == 'rejected')
                                        <span class="inline-block px-3 py-1 bg-red-50 text-red-700 border border-red-200 rounded text-[10px] font-bold uppercase tracking-wider">Rejected</span>
                                    @else
                                        <span class="inline-block px-3 py-1 bg-gray-100 text-gray-600 border border-gray-200 rounded text-[10px] font-bold uppercase tracking-wider">Draft</span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('laporan-bulanan.show', $l) }}" class="text-xs text-blue-600 hover:underline font-bold">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-[#64748B] italic">
                                    Belum ada laporan bulanan. 
                                    @if(auth()->user()->isKontraktor())
                                        <a href="{{ route('laporan-bulanan.create') }}" class="text-[#FFA000] hover:underline">Buat laporan sekarang →</a>
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
    </div>
</x-app-layout>
