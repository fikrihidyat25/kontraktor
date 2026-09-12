<x-app-layout>
    <x-slot name="title">Laporan Lainnya</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Laporan Lainnya</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar laporan pendukung proyek seperti laporan uji mutu, beton, dll.</p>
            </div>
            
            @if(auth()->user()->isKontraktor())
            <button x-data="" x-on:click="$dispatch('open-modal', 'create-laporan')" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity shadow-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Unggah Laporan
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
        @if($errors->any())
            <div class="bg-[#FEF2F2] border-l-4 border-[#DC2626] text-[#DC2626] p-4 mb-6 rounded shadow-sm text-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-[#E7E3DC] rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-[#64748B] uppercase bg-[#F8FAFC] border-b border-[#E7E3DC]">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Tgl Unggah</th>
                            <th class="px-6 py-3 font-semibold">Judul Laporan</th>
                            <th class="px-6 py-3 font-semibold">Proyek</th>
                            <th class="px-6 py-3 font-semibold">Keterangan</th>
                            <th class="px-6 py-3 font-semibold text-center">Dokumen</th>
                            
                            <th class="px-6 py-3 font-semibold text-center">Status</th>
                            @if(auth()->user()->isPPTK())
                                <th class="px-6 py-3 font-semibold text-center">Aksi (PPTK)</th>
                            @elseif(auth()->user()->isKontraktor())
                                <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                            @else
                                <th class="px-6 py-3 font-semibold text-center">Detail</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporans as $lp)
                        <tr class="border-b border-[#E7E3DC] even:bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors">
                            <td class="px-6 py-4 text-[#0F172B] whitespace-nowrap">{{ $lp->created_at->isoFormat('D MMM Y, HH:mm') }}</td>
                            <td class="px-6 py-4 text-[#0F172B] font-bold">{{ $lp->judul_laporan }}</td>
                            <td class="px-6 py-4 text-[#64748B] text-xs font-medium">{{ $lp->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4 text-[#64748B] text-xs">{{ Str::limit($lp->keterangan, 50) }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <a href="{{ asset('storage/' . $lp->file_laporan) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline flex items-center bg-blue-50 px-3 py-1 rounded">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        File Utama
                                    </a>
                                    
                                    @if($lp->lampiran_tambahan)
                                        <div class="text-[10px] text-gray-500 font-semibold mt-1">Lampiran Tambahan:</div>
                                        @foreach($lp->lampiran_tambahan as $idx => $lampiran)
                                            <a href="{{ asset('storage/' . $lampiran) }}" target="_blank" class="text-[10px] text-gray-600 hover:text-blue-600 hover:underline">
                                                Lampiran {{ $idx + 1 }}
                                            </a>
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @if($lp->status == 'disetujui')
                                    <span class="inline-block px-3 py-1 bg-[#F0FDF4] text-[#15803D] border border-[#BBF7D0] rounded-full text-[10px] font-bold uppercase tracking-wider">Disetujui</span>
                                @elseif($lp->status == 'ditolak')
                                    <span class="inline-block px-3 py-1 bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] rounded-full text-[10px] font-bold uppercase tracking-wider">Ditolak</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-[#FFF8E1] text-[#F57F17] border border-[#FFE082] rounded-full text-[10px] font-bold uppercase tracking-wider">Menunggu</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @if(auth()->user()->isPPTK() && $lp->status == 'menunggu_validasi')
                                <div class="flex gap-2 justify-center" x-data="{ openReview: false }">
                                    <button @click="openReview = true" class="bg-[#0F172B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity">
                                        Review
                                    </button>

                                    <!-- Modal Review PPTK -->
                                    <div x-show="openReview" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                        <div @click.away="openReview = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left">
                                            <h3 class="text-lg font-bold text-[#0F172B] mb-4">Review Laporan Lainnya</h3>
                                            
                                            <div class="mb-4 text-sm text-[#64748B]">
                                                <strong>Judul:</strong> {{ $lp->judul_laporan }}<br>
                                                <strong>Keterangan:</strong><br>
                                                {{ $lp->keterangan ?: '-' }}
                                            </div>

                                            <form method="POST" id="form-review-{{ $lp->id }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Catatan PPTK</label>
                                                    <textarea name="catatan_pptk" required rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm" placeholder="Berikan catatan..."></textarea>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="openReview = false" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B]">Batal</button>
                                                    <button type="submit" formaction="{{ route('laporan-lainnya.reject', $lp) }}" class="px-4 py-2 text-sm bg-red-50 text-red-600 border border-red-200 rounded font-semibold hover:bg-red-100">Tolak</button>
                                                    <button type="submit" formaction="{{ route('laporan-lainnya.approve', $lp) }}" class="px-4 py-2 text-sm bg-[#FFA000] text-[#0F172B] rounded font-bold hover:bg-opacity-90">Setujui</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @else
                                    <div class="flex gap-2 justify-center" x-data="{ openDetail: false }">
                                        <button @click="openDetail = true" class="text-xs font-bold text-[#1E3A8A] hover:underline bg-gray-100 px-3 py-1.5 rounded">
                                            Detail
                                        </button>
                                        
                                        @if(auth()->user()->isKontraktor() && $lp->uploaded_by == auth()->id())
                                            <form method="POST" action="{{ route('laporan-lainnya.destroy', $lp) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded text-xs font-semibold hover:bg-red-100 transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Modal Detail -->
                                        <div x-show="openDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                            <div @click.away="openDetail = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left whitespace-normal">
                                                <h3 class="text-lg font-bold text-[#0F172B] mb-4">Detail Laporan Lainnya</h3>
                                                
                                                <div class="mb-4 text-sm text-[#64748B]">
                                                    <strong>Judul:</strong> {{ $lp->judul_laporan }}<br>
                                                    <strong>Keterangan:</strong><br>
                                                    {{ $lp->keterangan ?: '-' }}<br><br>
                                                    <strong>Catatan PPTK:</strong><br>
                                                    {{ $lp->catatan_pptk ?: '-' }}
                                                </div>
                                                
                                                <div class="flex justify-end gap-3 mt-4">
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
                            <td colspan="{{ (auth()->user()->isKontraktor() || auth()->user()->isPPTK() || auth()->user()->isPPK() || auth()->user()->isKonsultan()) ? '7' : '6' }}" class="px-6 py-8 text-center text-[#64748B] italic">Belum ada laporan lainnya.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(auth()->user()->isKontraktor())
        <x-modal name="create-laporan" focusable>
            <form method="POST" action="{{ route('laporan-lainnya.store') }}" class="p-6" enctype="multipart/form-data">
                @csrf
                <h2 class="text-lg font-bold text-[#0F172B] mb-4">Unggah Laporan Lainnya</h2>
                
                @if(isset($proyeks) && $proyeks->isNotEmpty())
                    @php $proyek = $proyeks->first(); @endphp
                    <input type="hidden" name="proyek_id" value="{{ $proyek->id }}">
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Proyek</label>
                        <input type="text" value="{{ $proyek->nama_proyek }}" readonly class="w-full border-[#E7E3DC] rounded bg-gray-50 text-sm text-[#64748B] cursor-not-allowed">
                    </div>
                @else
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Pilih Proyek</label>
                        <select name="proyek_id" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]">
                            <option value="">— Pilih Proyek —</option>
                            @foreach($proyeks as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_proyek }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Judul Laporan</label>
                    <input type="text" name="judul_laporan" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]" placeholder="Contoh: Laporan Uji Kuat Tekan Beton">
                </div>
                
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Tanggal Laporan</label>
                    <input type="date" name="tanggal_laporan" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Keterangan Singkat</label>
                    <textarea name="keterangan" rows="2" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">File Utama (Wajib)</label>
                    <input type="file" name="file_laporan" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.rar" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Lampiran Tambahan (Opsional, Multiple File)</label>
                    <input type="file" name="lampiran_tambahan[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.rar" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B] mr-3">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#FFA000] text-[#0F172B] px-5 py-2 rounded text-sm font-bold hover:bg-opacity-90 shadow-sm">
                        Unggah Laporan
                    </button>
                </div>
            </form>
        </x-modal>
        @endif

    </div>
</x-app-layout>
