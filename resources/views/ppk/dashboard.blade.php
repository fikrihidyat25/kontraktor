<x-app-layout>
    <x-slot name="title">Dashboard PPK</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-8 border-b border-[#E5E7EB] pb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Dashboard Monitoring Proyek</h1>
            </div>
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['menunggu_approval'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Menunggu Final Approval</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['sudah_diapprove'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Sudah Disetujui (Approved)</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['total_laporan'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Total Laporan Masuk</div>
            </div>
        </div>

        <!-- S-CURVE PER PROYEK -->
        @foreach($proyeks as $proyek)
        @php $mingguans = $sCurveData[$proyek->id]; @endphp
        <div class="bg-white rounded-sm shadow-sm border border-[#E5E7EB] mb-8 overflow-hidden">
            <!-- Header Card Proyek -->
            <div class="bg-[#1E3A8A] p-6 relative border-b border-[#1E3A8A]">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-white mb-2">{{ $proyek->nama_proyek }}</h2>
                        <div class="text-sm text-white/80 flex flex-wrap gap-4">
                            <span class="flex items-center"><svg class="w-4 h-4 mr-1 text-[#FFB800]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>{{ $proyek->lokasi }}</span>
                            <span class="flex items-center"><svg class="w-4 h-4 mr-1 text-[#FFB800]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>{{ $proyek->tanggal_mulai->format('d M Y') }} – {{ $proyek->tanggal_selesai->format('d M Y') }}</span>
                            <span class="flex items-center font-bold text-[#FFB800]">Rp {{ number_format($proyek->nilai_kontrak, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="inline-block px-4 py-1.5 {{ $proyek->status === 'aktif' ? 'bg-[#15803D]' : 'bg-gray-600' }} text-white text-[10px] font-bold uppercase tracking-wider rounded-sm shadow-sm">
                            {{ ucfirst($proyek->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="bg-gray-50 border border-dashed border-gray-300 rounded-sm p-12 text-center text-gray-500">
                    <p class="font-bold text-gray-700 mb-2">Monitoring Laporan Mingguan</p>
                    <p class="text-sm">Dokumen Laporan Mingguan dapat dilihat dan diunduh pada menu <a href="{{ route('laporan-mingguan.index') }}" class="text-[#1D4ED8] hover:underline">Laporan Mingguan</a>.</p>
                </div>
            </div>
        </div>
        @endforeach

        <!-- PENDING APPROVAL LAPORAN HARIAN -->
        @if($pendingApproval->count())
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-sm shadow-sm text-sm font-medium flex items-center text-amber-800">
            <svg class="w-5 h-5 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>Ada <strong class="font-bold text-amber-700 text-base mx-1">{{ $stats['menunggu_approval'] }}</strong> laporan harian menunggu Final Approval Anda.</div>
        </div>

        <div class="bg-white rounded-sm shadow-sm border border-[#E5E7EB] overflow-hidden">
            <div class="bg-gray-50 border-b border-[#E5E7EB] px-6 py-4 flex justify-between items-center">
                <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">Laporan Harian Menunggu Final Approval</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="text-[11px] text-gray-500 uppercase bg-white border-b border-[#E5E7EB]">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                            <th class="px-6 py-4 font-semibold">Proyek</th>
                            <th class="px-6 py-4 font-semibold">Kontraktor</th>
                            <th class="px-6 py-4 font-semibold">Diverifikasi Oleh</th>
                            <th class="px-6 py-4 font-semibold text-center w-48">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB]">
                        @foreach($pendingApproval as $l)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">{{ $l->tanggal->isoFormat('dddd, D MMM Y') }}</td>
                            <td class="px-6 py-4 text-gray-900 font-medium">{{ $l->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4">{{ $l->kontraktor->name }}</td>
                            <td class="px-6 py-4">{{ $l->verifiedBy->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('laporan-harian.show', $l) }}" class="inline-flex items-center justify-center bg-[#1E3A8A] text-[#FFB800] text-xs font-bold px-4 py-2 rounded-sm border border-[#1E3A8A] hover:bg-[#152e70] transition-colors whitespace-nowrap">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Review & Approve
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
