<x-app-layout>
    <x-slot name="title">Tentang Proyek</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Profil Proyek</h1>
            <p class="text-gray-600 mt-1">Informasi detail mengenai proyek-proyek yang Anda tangani.</p>
        </div>

        @if($proyeks->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum Ada Proyek</h3>
                <p class="mt-1 text-sm text-gray-500">Anda belum ditugaskan pada proyek apa pun saat ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6">
                @foreach($proyeks as $proyek)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-[#1E3A8A] px-6 py-4">
                            <h2 class="text-lg font-bold text-white">{{ $proyek->nama_proyek }}</h2>
                            <div class="flex items-center gap-4 mt-2 text-sm text-blue-100">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    {{ $proyek->lokasi ?? 'Lokasi Belum Diisi' }}
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ \Carbon\Carbon::parse($proyek->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($proyek->tanggal_selesai)->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <!-- SKPD & Nilai Kontrak -->
                                <div class="col-span-1 md:col-span-2 flex flex-col md:flex-row justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                                    <div>
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">SKPD Pemilik Pekerjaan</p>
                                        <p class="text-base font-semibold text-gray-900">{{ $proyek->skpd ?? 'Belum Diisi' }}</p>
                                    </div>
                                    <div class="mt-4 md:mt-0 md:text-right">
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nilai Kontrak</p>
                                        <p class="text-lg font-bold text-[#FFB800]">Rp {{ number_format($proyek->nilai_kontrak, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <!-- PPK -->
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                                            P
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-500 uppercase">Pejabat Pembuat Komitmen</p>
                                            <p class="font-medium text-gray-900">{{ $proyek->ppk->name ?? 'Belum Ditentukan' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kontraktor -->
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center font-bold">
                                            K
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-500 uppercase">Kontraktor Pelaksana</p>
                                            <p class="font-medium text-gray-900">{{ $proyek->kontraktor->name ?? 'Belum Ditentukan' }}</p>
                                            @if($proyek->kontraktor && $proyek->kontraktor->penanggung_jawab)
                                                <p class="text-sm text-gray-500">PJ: {{ $proyek->kontraktor->penanggung_jawab }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Konsultan -->
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold">
                                            S
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-500 uppercase">Konsultan Pengawas</p>
                                            <p class="font-medium text-gray-900">{{ $proyek->konsultan->name ?? 'Belum Ditentukan' }}</p>
                                            @if($proyek->konsultan && $proyek->konsultan->penanggung_jawab)
                                                <p class="text-sm text-gray-500">PJ: {{ $proyek->konsultan->penanggung_jawab }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
