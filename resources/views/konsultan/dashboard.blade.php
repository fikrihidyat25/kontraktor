<x-app-layout>
    <x-slot name="title">Dashboard Konsultan</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-8 border-b border-[#E5E7EB] pb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Dashboard Konsultan Pengawas</h1>
            </div>
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['menunggu_verifikasi'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Menunggu Verifikasi</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['sudah_diverifikasi'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Terverifikasi</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['sudah_diapprove'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Disetujui PPK</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['ditolak'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Dikembalikan</div>
            </div>
        </div>

        <!-- PENDING LAPORAN ALERT -->
        @if($pendingLaporan->count())
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-sm shadow-sm text-sm font-medium flex items-center text-amber-800">
            <svg class="w-5 h-5 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>Ada <strong class="font-bold text-amber-700 text-base mx-1">{{ $stats['menunggu_verifikasi'] }}</strong> laporan menunggu proses audit & verifikasi Anda.</div>
        </div>
        @endif

        <!-- TABEL MENUNGGU VERIFIKASI -->
        <div class="bg-white rounded-sm shadow-sm border border-[#E5E7EB] overflow-hidden mb-4">
            <div class="bg-gray-50 border-b border-[#E5E7EB] px-6 py-4 flex justify-between items-center">
                <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">Laporan Menunggu Verifikasi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="text-[11px] text-gray-500 uppercase bg-white border-b border-[#E5E7EB]">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                            <th class="px-6 py-4 font-semibold">Proyek</th>
                            <th class="px-6 py-4 font-semibold">Kontraktor</th>
                            <th class="px-6 py-4 font-semibold">Cuaca</th>
                            <th class="px-6 py-4 font-semibold text-center w-48">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB]">
                        @forelse($pendingLaporan as $l)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">{{ $l->tanggal->isoFormat('dddd, D MMM Y') }}</td>
                            <td class="px-6 py-4 text-gray-900 font-medium">{{ $l->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4">{{ $l->kontraktor->name }}</td>
                            <td class="px-6 py-4">{{ $l->cuaca_label }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('laporan-harian.show', $l) }}" class="inline-flex items-center justify-center bg-[#1E3A8A] text-[#FFB800] text-xs font-bold px-4 py-2 rounded-sm border border-[#1E3A8A] hover:bg-[#152e70] transition-colors whitespace-nowrap">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Audit & Verifikasi
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center bg-white">
                                <div class="w-16 h-16 bg-gray-100 rounded-sm flex items-center justify-center mx-auto mb-4 text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div class="text-gray-500 italic text-base">Hore! Semua laporan sudah diverifikasi.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($pendingLaporan->count())
        <div class="text-right">
            <a href="{{ route('laporan-harian.index') }}" class="text-sm font-bold text-[#1E3A8A] hover:underline transition-colors">
                Lihat semua laporan &rarr;
            </a>
        </div>
        @endif
    </div>
</x-app-layout>
