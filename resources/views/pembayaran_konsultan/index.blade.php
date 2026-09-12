<x-app-layout>
    <x-slot name="title">Pembayaran Konsultan</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Pembayaran Konsultan</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar pengajuan termin pembayaran konsultan.</p>
            </div>
            
            @if(auth()->user()->isKonsultan())
            <button x-data="" x-on:click="$dispatch('open-modal', 'create-pembayaran-konsultan')" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity shadow-sm flex items-center">
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
                            <th class="px-6 py-3 font-semibold">Termin</th>
                            <th class="px-6 py-3 font-semibold text-right">Nilai</th>
                            <th class="px-6 py-3 font-semibold text-center">Status</th>
                            
                            @if(auth()->user()->isPPTK())
                                <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                            @else
                                <th class="px-6 py-3 font-semibold text-center">Detail</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayarans as $p)
                        <tr class="border-b border-[#E7E3DC] even:bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors">
                            <td class="px-6 py-4 text-[#0F172B] whitespace-nowrap">{{ $p->created_at->isoFormat('D MMM Y') }}</td>
                            <td class="px-6 py-4 text-[#64748B] text-xs font-medium">{{ $p->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4 text-[#0F172B] font-medium">{{ $p->termin }}</td>
                            <td class="px-6 py-4 text-[#0F172B] font-bold text-right">Rp {{ number_format($p->nilai_pembayaran, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($p->status == 'disetujui')
                                    <span class="inline-block px-3 py-1 bg-[#F0FDF4] text-[#15803D] border border-[#BBF7D0] rounded-full text-[10px] font-bold uppercase tracking-wider">Disetujui</span>
                                @elseif($p->status == 'ditolak')
                                    <span class="inline-block px-3 py-1 bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] rounded-full text-[10px] font-bold uppercase tracking-wider">Ditolak</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-[#FFF8E1] text-[#F57F17] border border-[#FFE082] rounded-full text-[10px] font-bold uppercase tracking-wider">Diajukan</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @if(auth()->user()->isPPTK() && $p->status == 'diajukan')
                                <div class="flex gap-2 justify-center" x-data="{ openReview: false }">
                                    <button @click="openReview = true" class="bg-[#0F172B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity">
                                        Review
                                    </button>

                                    <template x-teleport="body">
                                    <!-- Modal Review -->
                                    <div x-show="openReview" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                        <div @click.away="openReview = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left relative z-10">
                                            <h3 class="text-lg font-bold text-[#0F172B] mb-4">Review Pembayaran Konsultan (PPTK)</h3>
                                            
                                            <form method="POST" id="form-review-{{ $p->id }}">
                                                @csrf
                                                <div class="mb-4 text-sm text-[#64748B]">
                                                    <strong>Termin:</strong> {{ $p->termin }}<br>
                                                    <strong>Nilai:</strong> Rp {{ number_format($p->nilai_pembayaran, 0, ',', '.') }}
                                                </div>

                                                <div class="mb-4">
                                                    <strong class="text-[#0F172B] text-sm">Dokumen Pengajuan:</strong><br>
                                                    @if($p->surat_permohonan)
                                                        <a href="{{ asset('storage/' . $p->surat_permohonan) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1">
                                                            - Surat Permohonan
                                                        </a>
                                                    @endif
                                                    @if($p->laporan_kemajuan)
                                                        <a href="{{ asset('storage/' . $p->laporan_kemajuan) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1">
                                                            - Laporan Kemajuan
                                                        </a>
                                                    @endif
                                                    @if(!empty($p->lampiran_lainnya) && is_array($p->lampiran_lainnya))
                                                        @foreach($p->lampiran_lainnya as $idx => $lamp)
                                                            <a href="{{ asset('storage/' . $lamp) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1">
                                                                - Lampiran Lainnya {{ $idx + 1 }}
                                                            </a>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Catatan PPTK (Opsional)</label>
                                                    <textarea name="catatan_pptk" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm"></textarea>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="openReview = false" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B]">Batal</button>
                                                    <button type="submit" formaction="{{ route('pembayaran-konsultan.reject-pptk', $p) }}" class="px-4 py-2 text-sm bg-red-50 text-red-600 border border-red-200 rounded font-semibold hover:bg-red-100">Tolak</button>
                                                    <button type="submit" formaction="{{ route('pembayaran-konsultan.approve', $p) }}" class="px-4 py-2 text-sm bg-[#FFA000] text-[#0F172B] rounded font-bold hover:bg-opacity-90">Setujui</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    </template>
                                </div>
                                @else
                                    <div x-data="{ openDetail: false }">
                                        <button @click="openDetail = true" class="text-xs font-bold text-[#1E3A8A] hover:underline">
                                            Lihat Detail
                                        </button>

                                        <template x-teleport="body">
                                        <!-- Modal Detail -->
                                        <div x-show="openDetail" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                            <div @click.away="openDetail = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left relative z-10">
                                                <h3 class="text-lg font-bold text-[#0F172B] mb-4">Detail Pembayaran Konsultan</h3>
                                                
                                                <div class="mb-4 text-sm text-[#64748B]">
                                                    <strong>Termin:</strong> {{ $p->termin }}<br>
                                                    <strong>Nilai:</strong> Rp {{ number_format($p->nilai_pembayaran, 0, ',', '.') }}<br><br>
                                                    <strong>Catatan PPTK:</strong><br>
                                                    {{ $p->catatan_pptk ?: '-' }}<br><br>
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <strong class="text-[#0F172B] text-sm">Dokumen Pengajuan:</strong><br>
                                                    @if($p->surat_permohonan)
                                                        <a href="{{ asset('storage/' . $p->surat_permohonan) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1">
                                                            - Surat Permohonan
                                                        </a>
                                                    @endif
                                                    @if($p->laporan_kemajuan)
                                                        <a href="{{ asset('storage/' . $p->laporan_kemajuan) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1">
                                                            - Laporan Kemajuan
                                                        </a>
                                                    @endif
                                                    @if(!empty($p->lampiran_lainnya) && is_array($p->lampiran_lainnya))
                                                        @foreach($p->lampiran_lainnya as $idx => $lamp)
                                                            <a href="{{ asset('storage/' . $lamp) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1">
                                                                - Lampiran Lainnya {{ $idx + 1 }}
                                                            </a>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <div class="flex justify-end gap-3 mt-4">
                                                    <button type="button" @click="openDetail = false" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 rounded">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                        </template>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-[#64748B] italic">Belum ada pengajuan pembayaran konsultan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(auth()->user()->isKonsultan())
        <x-modal name="create-pembayaran-konsultan" focusable>
            <form method="POST" action="{{ route('pembayaran-konsultan.store') }}" class="p-6" enctype="multipart/form-data">
                @csrf
                <h2 class="text-lg font-bold text-[#0F172B] mb-4">Buat Pengajuan Pembayaran Konsultan</h2>
                
                @if(isset($proyeks) && $proyeks->isNotEmpty())
                    @php $proyek = $proyeks->first(); @endphp
                    <input type="hidden" name="proyek_id" value="{{ $proyek->id }}">
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Proyek</label>
                        <input type="text" value="{{ $proyek->nama_proyek }}" readonly class="w-full border-[#E7E3DC] rounded bg-gray-50 text-sm text-[#64748B] cursor-not-allowed">
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Termin</label>
                        <input type="text" name="termin" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]" placeholder="Contoh: Termin 1 (25%)">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Nilai Pembayaran (Rp)</label>
                        <input type="number" name="nilai_pembayaran" required min="0" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]" placeholder="Contoh: 50000000">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Surat Permohonan (Wajib, Max 5MB)</label>
                    <input type="file" name="surat_permohonan" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onchange="if(this.files[0] && this.files[0].size > 5242880){ alert('Ukuran maksimal 5MB!'); this.value = ''; }" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Laporan Kemajuan (Wajib, Max 10MB)</label>
                    <input type="file" name="laporan_kemajuan" required accept=".pdf,.doc,.docx,.zip,.rar" onchange="if(this.files[0] && this.files[0].size > 10485760){ alert('Ukuran maksimal 10MB!'); this.value = ''; }" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                </div>

                <div class="mb-6" x-data="{ lampiranCount: [1] }">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Lampiran Lainnya (Opsional, Max 5MB/file)</label>
                    <template x-for="(item, index) in lampiranCount" :key="index">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="file" name="lampiran_lainnya[]" accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png" onchange="if(this.files[0] && this.files[0].size > 5242880){ alert('Ukuran maksimal 5MB!'); this.value = ''; }" class="flex-1 text-sm text-[#64748B] border border-[#E7E3DC] rounded p-1 file:mr-4 file:py-1.5 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
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
