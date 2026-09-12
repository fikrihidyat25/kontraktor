<x-app-layout>
    <x-slot name="title">Pembayaran</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Pembayaran</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar pengajuan pembayaran progres.</p>
            </div>
            
            @if(auth()->user()->isKontraktor())
                @if($proyeks->isEmpty())
                    <button onclick="alert('Data proyek belum ada. Anda tidak bisa membuat pengajuan pembayaran.')" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity shadow-sm flex items-center opacity-70 cursor-not-allowed">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Buat Pengajuan Baru
                    </button>
                @else
                    <button x-data="" x-on:click="$dispatch('open-modal', 'create-pembayaran')" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity shadow-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Buat Pengajuan Baru
                    </button>
                @endif
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
                            <th class="px-6 py-3 font-semibold">Tgl / Pemb. Ke</th>
                            <th class="px-6 py-3 font-semibold">Proyek</th>
                            <th class="px-6 py-3 font-semibold text-right">Nilai Tagihan</th>
                            <th class="px-6 py-3 font-semibold text-center">Status</th>
                            
                            @if(auth()->user()->isPPTK() || auth()->user()->isKonsultan())
                                <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayarans as $pemb)
                        <tr class="border-b border-[#E7E3DC] even:bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors">
                            <td class="px-6 py-4 text-[#0F172B]">
                                <strong>Pembayaran ke-{{ $pemb->pembayaran_ke ?? 1 }}</strong><br>
                                <span class="text-xs text-[#64748B]">{{ $pemb->tanggal_pengajuan->isoFormat('D MMM Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-[#64748B] text-xs font-medium">{{ $pemb->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4 text-[#0F172B] font-mono font-bold text-right">Rp {{ number_format($pemb->nilai_tagihan, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($pemb->status == 'disetujui')
                                    <span class="inline-block px-3 py-1 bg-[#F0FDF4] text-[#15803D] border border-[#BBF7D0] rounded-full text-[10px] font-bold uppercase tracking-wider">Disetujui</span>
                                @elseif($pemb->status == 'ditolak')
                                    <span class="inline-block px-3 py-1 bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] rounded-full text-[10px] font-bold uppercase tracking-wider">Ditolak</span>
                                @elseif($pemb->status == 'diperiksa_konsultan')
                                    <span class="inline-block px-3 py-1 bg-[#EFF6FF] text-[#1D4ED8] border border-[#BFDBFE] rounded-full text-[10px] font-bold uppercase tracking-wider">Telah Diperiksa</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-[#FFF8E1] text-[#F57F17] border border-[#FFE082] rounded-full text-[10px] font-bold uppercase tracking-wider">Diajukan</span>
                                @endif
                            </td>
                            
                            @if(auth()->user()->isKonsultan())
                            <td class="px-6 py-4 text-center">
                                @if($pemb->status == 'diajukan')
                                <div class="flex gap-2 justify-center" x-data="{ openReview: false }">
                                    <button @click="openReview = true" class="bg-[#0F172B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity">
                                        Periksa Berkas
                                    </button>

                                    <!-- Modal Review Konsultan -->
                                    <div x-show="openReview" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                        <div @click.away="openReview = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left">
                                            <h3 class="text-lg font-bold text-[#0F172B] mb-4">Periksa Dokumen Pembayaran</h3>
                                            
                                            <form method="POST" id="form-review-{{ $pemb->id }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Checklist Verifikasi</label>
                                                    <div class="space-y-2">
                                                        <label class="flex items-center text-sm text-[#0F172B]">
                                                            <input type="checkbox" name="checklist[]" value="dokumen_lengkap" class="rounded border-gray-300 text-[#1E3A8A] focus:ring-[#1E3A8A] mr-2">
                                                            Dokumen lampiran lengkap dan valid
                                                        </label>
                                                        <label class="flex items-center text-sm text-[#0F172B]">
                                                            <input type="checkbox" name="checklist[]" value="progres_sesuai" class="rounded border-gray-300 text-[#1E3A8A] focus:ring-[#1E3A8A] mr-2">
                                                            Sesuai dengan progres fisik di lapangan
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Catatan Konsultan (Opsional)</label>
                                                    <textarea name="catatan" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm"></textarea>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="openReview = false" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B]">Batal</button>
                                                    <button type="submit" formaction="{{ route('permintaan-pembayaran.reject-konsultan', $pemb) }}" class="px-4 py-2 text-sm bg-red-50 text-red-600 border border-red-200 rounded font-semibold hover:bg-red-100">Tolak Berkas</button>
                                                    <button type="submit" formaction="{{ route('permintaan-pembayaran.verify', $pemb) }}" class="px-4 py-2 text-sm bg-[#FFA000] text-[#0F172B] rounded font-bold hover:bg-opacity-90">Verifikasi Berkas</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @else
                                    <span class="text-xs text-[#64748B] italic">Selesai</span>
                                @endif
                            </td>
                            @endif

                            @if(auth()->user()->isPPTK())
                            <td class="px-6 py-4 text-center">
                                @if($pemb->status == 'diperiksa_konsultan')
                                <div class="flex gap-2 justify-center" x-data="{ openReviewPPTK: false }">
                                    <button @click="openReviewPPTK = true" class="bg-[#0F172B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity">
                                        Approval
                                    </button>

                                    <!-- Modal Review PPTK -->
                                    <div x-show="openReviewPPTK" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                        <div @click.away="openReviewPPTK = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left relative z-10">
                                            <h3 class="text-lg font-bold text-[#0F172B] mb-4">Approval Pencairan oleh PPTK</h3>
                                            
                                            <form method="POST" id="form-review-pptk-{{ $pemb->id }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Catatan PPTK (Opsional)</label>
                                                    <textarea name="catatan" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm"></textarea>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="openReviewPPTK = false" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B]">Batal</button>
                                                    <button type="submit" formaction="{{ route('permintaan-pembayaran.reject-pptk', $pemb) }}" class="px-4 py-2 text-sm bg-red-50 text-red-600 border border-red-200 rounded font-semibold hover:bg-red-100">Tolak Pembayaran</button>
                                                    <button type="submit" formaction="{{ route('permintaan-pembayaran.approve', $pemb) }}" class="px-4 py-2 text-sm bg-[#FFA000] text-[#0F172B] rounded font-bold hover:bg-opacity-90">Setujui Pembayaran</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @else
                                    <span class="text-xs text-[#64748B] italic">@if($pemb->status == 'diajukan') Menunggu Konsultan @else Selesai @endif</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-[#64748B] italic">Belum ada tagihan pembayaran.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(auth()->user()->isKontraktor())
        <!-- Modal Form Input Pembayaran -->
        <x-modal name="create-pembayaran" focusable>
            <form method="POST" action="{{ route('permintaan-pembayaran.store') }}" class="p-6" enctype="multipart/form-data">
                @csrf
                <h2 class="text-lg font-bold text-[#0F172B] mb-4">Buat Pengajuan Pembayaran Baru</h2>
                
                @if(isset($proyeks) && $proyeks->isNotEmpty())
                    @php $proyek = $proyeks->first(); @endphp
                    <input type="hidden" name="proyek_id" value="{{ $proyek->id }}">
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Proyek</label>
                        <input type="text" value="{{ $proyek->nama_proyek }}" readonly class="w-full border-[#E7E3DC] rounded bg-gray-50 text-sm text-[#64748B] cursor-not-allowed">
                    </div>
                @endif

                <div class="mb-4 flex gap-4">
                    <div class="w-full">
                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Nilai Tagihan (Rp)</label>
                        <input type="number" name="nilai_tagihan" required min="0" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B] text-right" placeholder="0">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Surat Permohonan / Dokumen (Wajib, Max 5MB)</label>
                    <input type="file" name="dokumen_pendukung" required accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png" onchange="if(this.files[0].size > 5242880){ alert('Ukuran file maksimal 5MB!'); this.value = ''; }" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
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
                        Ajukan Pembayaran
                    </button>
                </div>
            </form>
        </x-modal>
        @endif

    </div>
</x-app-layout>
