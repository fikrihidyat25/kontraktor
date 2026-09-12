<x-app-layout>
    <x-slot name="title">Serah Terima Pengawasan</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Serah Terima Konsultan</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar pengajuan Serah Terima Pekerjaan Pengawasan.</p>
            </div>
            
            @if(auth()->user()->isKonsultan())
            <button x-data="" x-on:click="$dispatch('open-modal', 'create-serah-terima-konsultan')" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity shadow-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Pengajuan Baru
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
                            <th class="px-6 py-3 font-semibold">Tgl Pengajuan</th>
                            <th class="px-6 py-3 font-semibold">Proyek</th>
                            <th class="px-6 py-3 font-semibold text-center">Jenis</th>
                            <th class="px-6 py-3 font-semibold text-center">Status</th>
                            
                            @if(auth()->user()->isPPTK())
                                <th class="px-6 py-3 font-semibold text-center">Aksi (PPTK)</th>
                            @else
                                <th class="px-6 py-3 font-semibold text-center">Detail</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serahTerimas as $st)
                        <tr class="border-b border-[#E7E3DC] even:bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors">
                            <td class="px-6 py-4 text-[#0F172B] whitespace-nowrap">{{ $st->tanggal_pengajuan->isoFormat('D MMM Y') }}</td>
                            <td class="px-6 py-4 text-[#64748B] text-xs font-medium">{{ $st->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-[10px] font-bold uppercase tracking-wider">{{ $st->jenis }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($st->status == 'disetujui')
                                    <span class="inline-block px-3 py-1 bg-[#F0FDF4] text-[#15803D] border border-[#BBF7D0] rounded-full text-[10px] font-bold uppercase tracking-wider">Disetujui</span>
                                @elseif($st->status == 'ditolak')
                                    <span class="inline-block px-3 py-1 bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] rounded-full text-[10px] font-bold uppercase tracking-wider">Ditolak</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-[#FFF8E1] text-[#F57F17] border border-[#FFE082] rounded-full text-[10px] font-bold uppercase tracking-wider">Diajukan</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @if(auth()->user()->isPPTK() && $st->status == 'diajukan')
                                <div class="flex gap-2 justify-center" x-data="{ openReview: false }">
                                    <button @click="openReview = true" class="bg-[#0F172B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity">
                                        Review
                                    </button>

                                    <!-- Modal Review PPTK -->
                                    <div x-show="openReview" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                        <div @click.away="openReview = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left">
                                            <h3 class="text-lg font-bold text-[#0F172B] mb-4">Review Serah Terima Konsultan</h3>
                                            
                                            <form method="POST" id="form-review-{{ $st->id }}">
                                                @csrf
                                                <div class="mb-4 text-sm text-[#64748B]">
                                                    <strong>Keterangan:</strong><br>
                                                    {{ $st->keterangan ?: '-' }}
                                                </div>

                                                <div class="mb-4">
                                                    <strong class="text-[#0F172B]">Dokumen Pengajuan:</strong><br>
                                                    @if($st->surat_permohonan)
                                                        <a href="{{ asset('storage/' . $st->surat_permohonan) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1">
                                                            - Surat Permohonan
                                                        </a>
                                                    @endif
                                                    @if(!empty($st->dokumen_lampiran) && is_array($st->dokumen_lampiran))
                                                        @foreach($st->dokumen_lampiran as $idx => $lamp)
                                                            <a href="{{ asset('storage/' . $lamp) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1">
                                                                - Lampiran {{ $idx + 1 }}
                                                            </a>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Catatan (Opsional)</label>
                                                    <textarea name="catatan_ppk" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm"></textarea>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="openReview = false" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B]">Batal</button>
                                                    <button type="submit" formaction="{{ route('serah-terima-konsultan.reject', $st) }}" class="px-4 py-2 text-sm bg-red-50 text-red-600 border border-red-200 rounded font-semibold hover:bg-red-100">Tolak</button>
                                                    <button type="submit" formaction="{{ route('serah-terima-konsultan.approve', $st) }}" class="px-4 py-2 text-sm bg-[#FFA000] text-[#0F172B] rounded font-bold hover:bg-opacity-90">Setujui</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @else
                                    <div x-data="{ openDetail: false }">
                                        <button @click="openDetail = true" class="text-xs font-bold text-[#1E3A8A] hover:underline">
                                            Lihat Detail
                                        </button>

                                        <!-- Modal Detail -->
                                        <div x-show="openDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                            <div @click.away="openDetail = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left">
                                                <h3 class="text-lg font-bold text-[#0F172B] mb-4">Detail Serah Terima Konsultan</h3>
                                                
                                                <div class="mb-4 text-sm text-[#64748B]">
                                                    <strong>Keterangan:</strong><br>
                                                    {{ $st->keterangan ?: '-' }}<br><br>
                                                    <strong>Catatan Reviewer:</strong><br>
                                                    {{ $st->catatan_ppk ?: '-' }}
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <strong class="text-sm text-[#0F172B] block mb-2">Dokumen Lampiran:</strong>
                                                    @if($st->surat_permohonan)
                                                        <a href="{{ asset('storage/' . $st->surat_permohonan) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1">
                                                            - Surat Permohonan
                                                        </a>
                                                    @endif
                                                    @if(!empty($st->dokumen_lampiran) && is_array($st->dokumen_lampiran))
                                                        @foreach($st->dokumen_lampiran as $idx => $lamp)
                                                            <a href="{{ asset('storage/' . $lamp) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1">
                                                                - Lampiran {{ $idx + 1 }}
                                                            </a>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="openDetail = false" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 rounded">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-[#64748B] italic">Belum ada pengajuan serah terima konsultan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(auth()->user()->isKonsultan())
        <x-modal name="create-serah-terima-konsultan" focusable>
            <form method="POST" action="{{ route('serah-terima-konsultan.store') }}" class="p-6" enctype="multipart/form-data">
                @csrf
                <h2 class="text-lg font-bold text-[#0F172B] mb-4">Buat Pengajuan Serah Terima Pengawasan</h2>
                
                @if(isset($proyeks) && $proyeks->isNotEmpty())
                    @php $proyek = $proyeks->first(); @endphp
                    <input type="hidden" name="proyek_id" value="{{ $proyek->id }}">
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Proyek</label>
                        <input type="text" value="{{ $proyek->nama_proyek }}" readonly class="w-full border-[#E7E3DC] rounded bg-gray-50 text-sm text-[#64748B] cursor-not-allowed">
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Jenis Serah Terima</label>
                    <select name="jenis" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]">
                        <option value="Pengawasan">Serah Terima Pengawasan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Keterangan (Opsional)</label>
                    <textarea name="keterangan" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]" placeholder="Keterangan tambahan..."></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Surat Permohonan Serah Terima (Wajib, Max 5MB)</label>
                    <input type="file" name="surat_permohonan" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onchange="if(this.files[0] && this.files[0].size > 5242880){ alert('Ukuran maksimal 5MB!'); this.value = ''; }" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                </div>

                <div class="mb-6" x-data="{ lampiranCount: [1] }">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Lampiran-lampirannya (Max 5MB/file)</label>
                    <template x-for="(item, index) in lampiranCount" :key="index">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="file" name="dokumen_lampiran[]" accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png" onchange="if(this.files[0] && this.files[0].size > 5242880){ alert('Ukuran maksimal 5MB!'); this.value = ''; }" class="flex-1 text-sm text-[#64748B] border border-[#E7E3DC] rounded p-1 file:mr-4 file:py-1.5 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                            <button type="button" @click="lampiranCount.splice(index, 1)" x-show="lampiranCount.length > 1" class="text-red-500 hover:text-red-700 p-1.5 focus:outline-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="lampiranCount.push(Date.now())" class="mt-1 text-sm text-[#1E3A8A] font-bold hover:underline flex items-center focus:outline-none">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Lampiran
                    </button>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B] mr-3">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#FFA000] text-[#0F172B] px-5 py-2 rounded text-sm font-bold hover:bg-opacity-90 shadow-sm">
                        Ajukan Sekarang
                    </button>
                </div>
            </form>
        </x-modal>
        @endif

    </div>
</x-app-layout>
