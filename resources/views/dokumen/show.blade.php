<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pratinjau Dokumen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6 border-b pb-4">
                        <div>
                            <h3 class="text-2xl font-bold text-[#0F172B]">{{ $dokumen->nama_dokumen }}</h3>
                            <p class="text-sm text-gray-500 mt-1">Tipe: {{ $dokumen->tipe_dokumen }} | Diunggah: {{ $dokumen->created_at->isoFormat('D MMM Y HH:mm') }}</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('dokumen.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded hover:bg-gray-200 transition">
                                Kembali
                            </a>
                            <a href="{{ route('dokumen.file', $dokumen->id) }}" download class="px-4 py-2 bg-[#FFA000] text-[#0F172B] font-bold rounded hover:bg-opacity-90 shadow-sm transition flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Download
                            </a>
                        </div>
                    </div>

                    <div class="w-full bg-gray-50 rounded border border-gray-200 flex items-center justify-center overflow-hidden relative" style="height: 75vh;" id="viewer-container">
                        @if($isPdf)
                            <div id="loading-spinner" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 z-10">
                                <svg class="animate-spin h-10 w-10 text-[#0F172B] mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-gray-600 font-medium">Memuat Pratinjau PDF...</p>
                                <p class="text-xs text-gray-400 mt-2">Ini akan mencegah IDM mengambil alih file.</p>
                            </div>
                            <!-- Embed will be injected here by JS -->
                            
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const pdfUrl = "{{ route('dokumen.file', $dokumen->id) }}";
                                    
                                    fetch(pdfUrl)
                                        .then(response => {
                                            if (!response.ok) throw new Error('Network response was not ok');
                                            return response.blob();
                                        })
                                        .then(blob => {
                                            const blobUrl = URL.createObjectURL(blob);
                                            const container = document.getElementById('viewer-container');
                                            const spinner = document.getElementById('loading-spinner');
                                            
                                            const embed = document.createElement('embed');
                                            embed.src = blobUrl;
                                            embed.type = 'application/pdf';
                                            embed.style.width = '100%';
                                            embed.style.height = '100%';
                                            
                                            container.appendChild(embed);
                                            spinner.style.display = 'none';
                                        })
                                        .catch(error => {
                                            console.error('Error fetching PDF:', error);
                                            const spinner = document.getElementById('loading-spinner');
                                            spinner.innerHTML = `
                                                <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <h3 class="text-lg font-medium text-gray-900 mb-2">Gagal Menampilkan Pratinjau</h3>
                                                <p class="text-gray-500 text-center">Gagal memuat file PDF.<br>Silakan klik tombol Download di atas.</p>
                                            `;
                                        });
                                });
                            </script>
                        @elseif($isImage)
                            <img src="{{ route('dokumen.file', $dokumen->id) }}" alt="{{ $dokumen->nama_dokumen }}" class="max-w-full max-h-full object-contain">
                        @else
                            <div class="text-center p-8">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Pratinjau Tidak Tersedia</h3>
                                <p class="text-gray-500">Format file ini tidak dapat ditampilkan langsung di browser.<br>Silakan klik tombol Download untuk melihat isinya.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>