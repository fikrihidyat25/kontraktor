<x-app-layout>
    <x-slot name="title">Serah Terima (PHO / FHO)</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Serah Terima (PHO/FHO)</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar pengajuan Provisional Hand Over (PHO) dan Final Hand Over (FHO).</p>
            </div>
            
            @if(auth()->user()->isKontraktor())
            <button x-data="" x-on:click="$dispatch('open-modal', 'create-serah-terima')" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity shadow-sm flex items-center">
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

        <!-- Area Tabel -->
        <div class="bg-white border border-[#E7E3DC] rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-[#64748B] uppercase bg-[#F8FAFC] border-b border-[#E7E3DC]">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Tgl Pengajuan</th>
                            <th class="px-6 py-3 font-semibold">Proyek</th>
                            <th class="px-6 py-3 font-semibold text-center">Jenis</th>
                            <th class="px-6 py-3 font-semibold text-center">Status</th>
                            
                            @if(auth()->user()->isPPTK() || auth()->user()->isPPK() || auth()->user()->isKonsultan() || auth()->user()->isKontraktor())
                                <th class="px-6 py-3 font-semibold text-center">Aksi / Detail</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serahTerimas as $st)
                        <tr class="border-b border-[#E7E3DC] even:bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors">
                            <td class="px-6 py-4 text-[#0F172B] whitespace-nowrap">{{ $st->tanggal_pengajuan->isoFormat('D MMM Y') }}</td>
                            <td class="px-6 py-4 text-[#64748B] text-xs font-medium">{{ $st->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($st->jenis == 'PHO')
                                    <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-[10px] font-bold uppercase tracking-wider">PHO</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-full text-[10px] font-bold uppercase tracking-wider">FHO</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col gap-1 items-center">
                                    @if($st->status_konsultan == 'pending')
                                        <span class="inline-block px-3 py-1 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-full text-[10px] font-bold uppercase tracking-wider">Menunggu Konsultan</span>
                                    @elseif($st->status_konsultan == 'ditolak')
                                        <span class="inline-block px-3 py-1 bg-red-50 text-red-700 border border-red-200 rounded-full text-[10px] font-bold uppercase tracking-wider">Ditolak Konsultan</span>
                                    @elseif($st->status_konsultan == 'disetujui' && $st->status_pptk == 'pending')
                                        <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-[10px] font-bold uppercase tracking-wider">Menunggu PPTK</span>
                                    @elseif($st->status_pptk == 'ditolak')
                                        <span class="inline-block px-3 py-1 bg-red-50 text-red-700 border border-red-200 rounded-full text-[10px] font-bold uppercase tracking-wider">Ditolak PPTK</span>
                                    @elseif($st->status_pptk == 'disetujui')
                                        <span class="inline-block px-3 py-1 bg-[#F0FDF4] text-[#15803D] border border-[#BBF7D0] rounded-full text-[10px] font-bold uppercase tracking-wider">Serah Terima Approved</span>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <div class="flex gap-2 justify-center" x-data="{ openDetail: false }">
                                    <button @click="openDetail = true" class="bg-gray-100 text-gray-700 border border-gray-300 px-3 py-1.5 rounded text-xs font-semibold hover:bg-gray-200 transition-colors">
                                        Detail
                                    </button>

                                    <template x-teleport="body">
                                    <!-- Modal Detail Semua Role -->
                                    <div x-show="openDetail" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                        <div @click.away="openDetail = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg text-left max-h-[90vh] overflow-y-auto relative z-10">
                                            <h3 class="text-lg font-bold text-[#0F172B] mb-4">Detail Pengajuan Serah Terima ({{ $st->jenis }})</h3>
                                            
                                            <div class="mb-4">
                                                <strong class="text-[#0F172B] block mb-2 border-b pb-1">Dokumen Pengajuan (Kontraktor):</strong>
                                                @if($st->surat_permohonan)
                                                    <a href="{{ asset('storage/' . $st->surat_permohonan) }}" target="_blank" class="text-sm font-semibold text-[#1E3A8A] hover:underline flex items-center gap-2 mb-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                        Surat Permohonan
                                                    </a>
                                                @endif
                                                @if(!empty($st->dokumen_lampiran) && is_array($st->dokumen_lampiran))
                                                    @foreach($st->dokumen_lampiran as $idx => $lamp)
                                                        <a href="{{ asset('storage/' . $lamp) }}" target="_blank" class="text-sm font-medium text-[#1E3A8A] hover:underline flex items-center gap-2 mb-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                            Lampiran {{ $idx + 1 }}
                                                        </a>
                                                    @endforeach
                                                @endif
                                                @if($st->lampiran_tambahan)
                                                    <a href="{{ asset('storage/' . $st->lampiran_tambahan) }}" target="_blank" class="text-sm font-medium text-[#1E3A8A] hover:underline flex items-center gap-2 mb-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                        Lampiran Tambahan
                                                    </a>
                                                @endif
                                                <div class="mt-2 text-sm text-[#64748B] italic">Keterangan: {{ $st->keterangan ?: '-' }}</div>
                                            </div>

                                            <div class="mb-4">
                                                <strong class="text-[#0F172B] block mb-2 border-b pb-1">Review Konsultan:</strong>
                                                @if($st->surat_persetujuan_konsultan)
                                                    <a href="{{ asset('storage/' . $st->surat_persetujuan_konsultan) }}" target="_blank" class="text-sm font-bold text-blue-700 hover:underline flex items-center gap-2 mb-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        Surat Persetujuan Konsultan
                                                    </a>
                                                @endif
                                                <div class="text-sm text-[#64748B]">Catatan: {{ $st->catatan_konsultan ?: '-' }}</div>
                                            </div>

                                            <div class="mb-4">
                                                <strong class="text-[#0F172B] block mb-2 border-b pb-1">Review PPTK:</strong>
                                                <div class="text-sm text-[#64748B]">Catatan: {{ $st->catatan_pptk ?: '-' }}</div>
                                            </div>

                                            <div class="mb-4">
                                                <strong class="text-[#0F172B] block mb-2 border-b pb-1">Persetujuan Akhir PPK:</strong>
                                                @if($st->surat_persetujuan_ppk)
                                                    <a href="{{ asset('storage/' . $st->surat_persetujuan_ppk) }}" target="_blank" class="text-sm font-bold text-green-700 hover:underline flex items-center gap-2 mb-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        Surat Persetujuan PHO/FHO (Final)
                                                    </a>
                                                @endif
                                                <div class="text-sm text-[#64748B]">Catatan: {{ $st->catatan_ppk ?: '-' }}</div>
                                            </div>

                                            <div class="flex justify-end gap-3 mt-6">
                                                <button type="button" @click="openDetail = false" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 font-medium rounded hover:bg-gray-200">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                    </template>
                                </div>

                                @if(auth()->user()->isKonsultan() && $st->status_konsultan == 'pending')
                                    <div class="flex gap-2 justify-center mt-2" x-data="{ openReviewKonsultan: false }">
                                        <button @click="openReviewKonsultan = true" class="bg-blue-600 text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity">
                                            Beri Review
                                        </button>

                                        <template x-teleport="body">
                                        <!-- Modal Review Konsultan -->
                                        <div x-show="openReviewKonsultan" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                            <div @click.away="openReviewKonsultan = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left relative z-10">
                                                <h3 class="text-lg font-bold text-[#0F172B] mb-4">Review Serah Terima ({{ $st->jenis }}) - Konsultan</h3>
                                                <form method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-4">
                                                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Upload Surat Persetujuan (Wajib jika setuju)</label>
                                                        <input type="file" name="surat_persetujuan_konsultan" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Catatan Konsultan</label>
                                                        <textarea name="catatan_konsultan" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm"></textarea>
                                                    </div>
                                                    <div class="flex justify-end gap-3">
                                                        <button type="button" @click="openReviewKonsultan = false" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B]">Batal</button>
                                                        <button type="submit" formaction="{{ route('serah-terima.rejectKonsultan', $st) }}" class="px-4 py-2 text-sm bg-red-50 text-red-600 border border-red-200 rounded font-semibold hover:bg-red-100">Tolak</button>
                                                        <button type="submit" formaction="{{ route('serah-terima.approveKonsultan', $st) }}" class="px-4 py-2 text-sm bg-blue-600 text-white rounded font-bold hover:bg-opacity-90">Setuju & Upload</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        </template>
                                    </div>
                                @endif

                                @if(auth()->user()->isPPTK() && $st->status_konsultan == 'disetujui' && $st->status_pptk == 'pending')
                                    <div class="flex gap-2 justify-center mt-2" x-data="{ openReviewPPTK: false }">
                                        <button @click="openReviewPPTK = true" class="bg-indigo-600 text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity">
                                            Validasi PPTK
                                        </button>

                                        <template x-teleport="body">
                                        <!-- Modal Review PPTK -->
                                        <div x-show="openReviewPPTK" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                            <div @click.away="openReviewPPTK = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left relative z-10">
                                                <h3 class="text-lg font-bold text-[#0F172B] mb-4">Validasi PPTK & Persetujuan Akhir ({{ $st->jenis }})</h3>
                                                <form method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-4">
                                                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Upload Surat Persetujuan PHO/FHO (Wajib)</label>
                                                        <input type="file" name="surat_persetujuan_ppk" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]" required>
                                                        <p class="text-[10px] text-gray-500 mt-1">Dokumen ini akan menjadi lampiran final untuk Kontraktor.</p>
                                                    </div>
                                                    <div class="mb-4">
                                                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Catatan PPTK (Opsional)</label>
                                                        <textarea name="catatan_pptk" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm"></textarea>
                                                    </div>
                                                    <div class="flex justify-end gap-3">
                                                        <button type="button" @click="openReviewPPTK = false" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B]">Batal</button>
                                                        <button type="submit" formaction="{{ route('serah-terima.reject', $st) }}" class="px-4 py-2 text-sm bg-red-50 text-red-600 border border-red-200 rounded font-semibold hover:bg-red-100">Tolak</button>
                                                        <button type="submit" formaction="{{ route('serah-terima.approve', $st) }}" class="px-4 py-2 text-sm bg-[#0F172B] text-white rounded font-bold hover:bg-opacity-90">Setuju & Upload</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        </template>
                                    </div>
                                @endif


                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-[#64748B] italic">Belum ada pengajuan serah terima.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(auth()->user()->isKontraktor())
        <!-- Modal Form Input Serah Terima -->
        <x-modal name="create-serah-terima" focusable>
            <form method="POST" action="{{ route('serah-terima.store') }}" class="p-6" enctype="multipart/form-data">
                @csrf
                <h2 class="text-lg font-bold text-[#0F172B] mb-4">Buat Pengajuan Serah Terima</h2>
                
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
                        <option value="PHO">Provisional Hand Over (PHO)</option>
                        <option value="FHO">Final Hand Over (FHO)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Keterangan (Opsional)</label>
                    <textarea name="keterangan" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]" placeholder="Keterangan tambahan..."></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Surat Permohonan Serah Terima (Wajib, Max 5MB)</label>
                    <input type="file" name="surat_permohonan" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onchange="if(this.files[0].size > 5242880){ alert('Ukuran file maksimal 5MB!'); this.value = ''; }" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                </div>

                <div class="mb-6" x-data="{ lampiranCount: [1] }">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Lampiran-lampirannya (Max 5MB/file)</label>
                    <template x-for="(item, index) in lampiranCount" :key="index">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="file" name="dokumen_lampiran[]" required accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png" onchange="if(this.files[0] && this.files[0].size > 5242880){ alert('Ukuran file maksimal 5MB!'); this.value = ''; }" class="flex-1 text-sm text-[#64748B] border border-[#E7E3DC] rounded p-1 file:mr-4 file:py-1.5 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
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

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Lampiran Tambahan Lainnya (Opsional)</label>
                    <input type="file" name="lampiran_tambahan" accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png" class="w-full text-sm text-[#64748B] border border-[#E7E3DC] rounded p-1 file:mr-4 file:py-1.5 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
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
