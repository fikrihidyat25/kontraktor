<x-app-layout>
    <x-slot name="title">Dokumen & Kontrak Proyek</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Dokumen Kontrak & SPMK</h1>
                <p class="text-sm text-[#64748B] mt-1">Kumpulan dokumen penting terkait pelaksanaan proyek (Kontrak, SPMK, dll).</p>
            </div>
            
            @if(auth()->user()->isPPTK())
            <button x-data="" x-on:click="$dispatch('open-modal', 'create-dokumen')" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity shadow-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Unggah Dokumen
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
                            <th class="px-6 py-3 font-semibold">Tgl Unggah</th>
                            <th class="px-6 py-3 font-semibold">Proyek</th>
                            <th class="px-6 py-3 font-semibold text-center">Tipe Dokumen</th>
                            <th class="px-6 py-3 font-semibold">Nama Dokumen</th>
                            <th class="px-6 py-3 font-semibold text-center">Status</th>
                            <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dokumens as $d)
                        <tr class="border-b border-[#E7E3DC] even:bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors">
                            <td class="px-6 py-4 text-[#0F172B] whitespace-nowrap">{{ $d->created_at->isoFormat('D MMM Y HH:mm') }}</td>
                            <td class="px-6 py-4 text-[#64748B] text-xs font-medium">{{ $d->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($d->tipe_dokumen == 'SPMK')
                                    <span class="inline-block px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-full text-[10px] font-bold uppercase tracking-wider">SPMK</span>
                                @elseif($d->tipe_dokumen == 'Kontrak')
                                    <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-[10px] font-bold uppercase tracking-wider">KONTRAK</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-gray-50 text-gray-700 border border-gray-200 rounded-full text-[10px] font-bold uppercase tracking-wider">{{ $d->tipe_dokumen }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-[#0F172B] font-semibold">{{ $d->nama_dokumen }}</div>
                                <div class="flex flex-col gap-1 mt-1">
                                    <a href="{{ route('dokumen.file', $d->id) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat File Utama
                                    </a>
                                    @if($d->lampiran_tambahan)
                                    <a href="{{ route('dokumen.lampiran', $d->id) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-[#FFA000] hover:text-amber-600 hover:underline">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat Lampiran Opsional
                                    </a>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($d->status == 'disetujui')
                                    <span class="inline-block px-3 py-1 bg-[#F0FDF4] text-[#15803D] border border-[#BBF7D0] rounded-full text-[10px] font-bold uppercase tracking-wider">Disetujui</span>
                                @elseif($d->status == 'ditolak')
                                    <span class="inline-block px-3 py-1 bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] rounded-full text-[10px] font-bold uppercase tracking-wider">Ditolak</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-[#FFF8E1] text-[#F57F17] border border-[#FFE082] rounded-full text-[10px] font-bold uppercase tracking-wider">Menunggu Validasi</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(auth()->user()->isKontraktor() && $d->status == 'menunggu_validasi')
                                <div class="flex gap-2 justify-center" x-data="{ openReview: false }">
                                    <a href="{{ route('dokumen.show', $d->id) }}" target="_blank" class="bg-[#0F172B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity shadow-sm">
                                        Pratinjau
                                    </a>
                                    <button @click="openReview = true" class="bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity shadow-sm">
                                        Validasi
                                    </button>

                                    <!-- Modal Validasi -->
                                    <div x-show="openReview" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                        <div @click.away="openReview = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left">
                                            <h3 class="text-lg font-bold text-[#0F172B] mb-4">Validasi Dokumen Kontrak</h3>
                                            <div class="mb-4 text-sm text-[#64748B]">
                                                <strong>Nama Dokumen:</strong><br>
                                                {{ $d->nama_dokumen }}
                                            </div>
                                            
                                            <form method="POST" action="{{ route('dokumen.approve', $d) }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Catatan Validasi (Opsional)</label>
                                                    <textarea name="catatan_validasi" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm"></textarea>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="openReview = false" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B]">Batal</button>
                                                    <button type="submit" formaction="{{ route('dokumen.reject', $d) }}" class="px-4 py-2 text-sm bg-red-50 text-red-600 border border-red-200 rounded font-semibold hover:bg-red-100 shadow-sm">Tolak</button>
                                                    <button type="submit" class="px-4 py-2 text-sm bg-[#FFA000] text-[#0F172B] rounded font-bold hover:bg-opacity-90 shadow-sm">Setujui Dokumen</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="flex gap-2 justify-center" x-data="{ openDetail: false }">
                                    <a href="{{ route('dokumen.show', $d->id) }}" target="_blank" class="bg-[#0F172B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity shadow-sm">
                                        Pratinjau
                                    </a>
                                    <button @click="openDetail = true" class="bg-[#64748B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity shadow-sm">
                                        Detail
                                    </button>

                                    <!-- Modal Detail -->
                                    <div x-show="openDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                        <div @click.away="openDetail = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left">
                                            <h3 class="text-lg font-bold text-[#0F172B] mb-4">Detail Dokumen</h3>
                                            <div class="mb-4 text-sm text-[#64748B]">
                                                <strong>Nama Dokumen:</strong><br>
                                                {{ $d->nama_dokumen }}
                                            </div>
                                            @if($d->catatan_validasi)
                                            <div class="mb-4 text-sm text-[#64748B]">
                                                <strong>Catatan Validasi:</strong><br>
                                                {{ $d->catatan_validasi }}
                                            </div>
                                            @endif
                                            
                                            <div class="flex justify-end mt-6 gap-3">
                                                @if(auth()->id() == $d->uploaded_by)
                                                <form action="{{ route('dokumen.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini?');" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-4 py-2 text-sm bg-red-600 text-white rounded font-medium hover:bg-opacity-90">Hapus File</button>
                                                </form>
                                                @endif
                                                <button type="button" @click="openDetail = false" class="px-4 py-2 text-sm bg-[#64748B] text-white rounded font-medium hover:bg-opacity-90">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-[#64748B] italic">Belum ada dokumen yang diunggah.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(auth()->user()->isPPTK())
        <!-- Modal Form Input Dokumen -->
        <x-modal name="create-dokumen" focusable>
            <form method="POST" action="{{ route('dokumen.store') }}" class="p-6" enctype="multipart/form-data">
                @csrf
                <h2 class="text-lg font-bold text-[#0F172B] mb-4">Unggah Dokumen Kontrak / SPMK</h2>
                
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Pilih Proyek</label>
                    <select name="proyek_id" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]">
                        <option value="">— Pilih Proyek —</option>
                        @foreach($proyeks as $proyek)
                            <option value="{{ $proyek->id }}">{{ $proyek->nama_proyek }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Tipe Dokumen</label>
                    <select name="tipe_dokumen" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]">
                        <option value="SPMK">Surat Perintah Mulai Kerja (SPMK Kontraktor)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Nama Dokumen</label>
                    <input type="text" name="nama_dokumen" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]" placeholder="Contoh: SPMK Tahap 1">
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">File Dokumen (Wajib, Max 10MB)</label>
                    <input type="file" name="file_dokumen" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.rar" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                </div>
                <div class="mb-6">
                    <label class="block text-[#0F172B] text-sm font-bold mb-2">Lampiran Tambahan (Opsional)</label>
                    <input type="file" name="lampiran_tambahan" accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B] mr-3">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#FFA000] text-[#0F172B] px-5 py-2 rounded text-sm font-bold hover:bg-opacity-90 shadow-sm">
                        Unggah Dokumen
                    </button>
                </div>
            </form>
        </x-modal>
        @endif

    </div>
</x-app-layout>
