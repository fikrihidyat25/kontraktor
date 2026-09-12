<x-app-layout>
    <x-slot name="title">Berita Acara Konsultan</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Berita Acara Konsultan</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar Berita Acara dan Dokumentasi Konsultan Pengawas.</p>
            </div>
            
            @if(auth()->user()->isKonsultan())
            <button x-data="" x-on:click="$dispatch('open-modal', 'create-berita-acara')" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity shadow-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Upload Berita Acara
            </button>
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
                            <th class="px-6 py-3 font-semibold">Tgl</th>
                            <th class="px-6 py-3 font-semibold">Proyek</th>
                            <th class="px-6 py-3 font-semibold">Judul / Pembahasan</th>
                            <th class="px-6 py-3 font-semibold text-center">Berita Acara</th>
                            <th class="px-6 py-3 font-semibold text-center">Dokumentasi</th>
                            @if(auth()->user()->isKonsultan())
                            <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($beritaAcaras as $m)
                        <tr class="border-b border-[#E7E3DC] even:bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors">
                            <td class="px-6 py-4 text-[#0F172B] whitespace-nowrap font-medium">{{ $m->tanggal->isoFormat('D MMM Y') }}</td>
                            <td class="px-6 py-4 text-[#64748B] text-xs font-medium">{{ $m->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4">
                                <div class="text-[#0F172B] font-bold text-sm mb-1">{{ $m->judul }}</div>
                                <div class="text-[#64748B] text-xs max-w-xs">{{ $m->pembahasan }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ asset('storage/' . $m->file_berita_acara) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-[#15803D] hover:text-green-800 hover:underline">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    Unduh
                                </a>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(!empty($m->dokumentasi) && is_array($m->dokumentasi))
                                    @foreach($m->dokumentasi as $idx => $doc)
                                        <a href="{{ asset('storage/' . $doc) }}" target="_blank" class="inline-block text-xs font-bold text-[#1E3A8A] hover:underline mb-1">
                                            Lampiran {{ $idx+1 }}
                                        </a><br>
                                    @endforeach
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            @if(auth()->user()->isKonsultan())
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('berita-acara-konsultan.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Berita Acara ini?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isKonsultan() ? 6 : 5 }}" class="px-6 py-8 text-center text-[#64748B] italic">Belum ada Berita Acara Konsultan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(auth()->user()->isKonsultan())
        <!-- Modal Form Input -->
        <x-modal name="create-berita-acara" focusable>
            <form method="POST" action="{{ route('berita-acara-konsultan.store') }}" class="p-6" enctype="multipart/form-data">
                @csrf
                <h2 class="text-lg font-bold text-[#0F172B] mb-4">Upload Berita Acara & Dokumentasi</h2>
                
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Proyek</label>
                    <select name="proyek_id" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]">
                        <option value="">— Pilih Proyek —</option>
                        @foreach($proyeks as $proyek)
                            <option value="{{ $proyek->id }}">{{ $proyek->nama_proyek }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Judul / Kegiatan</label>
                    <input type="text" name="judul" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Tanggal</label>
                    <input type="date" name="tanggal" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Pembahasan / Catatan</label>
                    <textarea name="pembahasan" required rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Berita Acara (Wajib, PDF/DOC, Max 10MB)</label>
                    <input type="file" name="file_berita_acara" required accept=".pdf,.doc,.docx" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                </div>

                <div class="mb-4" x-data="{ dokCount: [1] }">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Dokumentasi (Opsional, Max 5MB/file)</label>
                    <template x-for="(item, index) in dokCount" :key="index">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="file" name="dokumentasi[]" accept=".pdf,.jpg,.jpeg,.png" class="flex-1 text-sm text-[#64748B] border border-[#E7E3DC] rounded p-1 file:mr-4 file:py-1.5 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                            <button type="button" @click="dokCount.splice(index, 1)" x-show="dokCount.length > 1" class="text-red-500 hover:text-red-700 p-1.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="dokCount.push(Date.now())" class="mt-1 text-sm text-[#1E3A8A] font-bold hover:underline flex items-center focus:outline-none">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Dokumentasi
                    </button>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B] mr-3">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#FFA000] text-[#0F172B] px-5 py-2 rounded text-sm font-bold hover:bg-opacity-90 shadow-sm">
                        Simpan
                    </button>
                </div>
            </form>
        </x-modal>
        @endif

    </div>
</x-app-layout>
