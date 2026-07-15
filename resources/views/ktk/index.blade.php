<x-app-layout>
    <x-slot name="title">Kerja Tambah Kurang (KTK)</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Kerja Tambah Kurang (KTK)</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar usulan pekerjaan tambah / kurang (Change Order).</p>
            </div>
            
            @if(auth()->user()->isKontraktor())
            <button x-data="" x-on:click="$dispatch('open-modal', 'create-ktk')" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity shadow-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Usulan KTK
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
                            <th class="px-6 py-3 font-semibold">No Surat / Tgl</th>
                            <th class="px-6 py-3 font-semibold">Proyek</th>
                            <th class="px-6 py-3 font-semibold">Jenis KTK</th>
                            <th class="px-6 py-3 font-semibold text-right">Nilai Estimasi</th>
                            <th class="px-6 py-3 font-semibold text-center">Status</th>
                            
                            @if(auth()->user()->isPPK() || auth()->user()->isKonsultan())
                                <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ktks as $ktk)
                        <tr class="border-b border-[#E7E3DC] even:bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors">
                            <td class="px-6 py-4 text-[#0F172B] whitespace-nowrap">
                                <strong>{{ $ktk->nomor_surat_pengajuan }}</strong><br>
                                <span class="text-xs text-[#64748B]">{{ $ktk->tanggal_pengajuan->isoFormat('D MMM Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-[#64748B] text-xs font-medium">{{ $ktk->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4">
                                @if($ktk->jenis_ktk == 'tambah')
                                    <span class="text-[#15803D] font-bold">Pekerjaan Tambah</span>
                                @else
                                    <span class="text-[#DC2626] font-bold">Pekerjaan Kurang</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-[#0F172B] font-mono font-bold text-right">Rp {{ number_format($ktk->nilai_estimasi, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($ktk->status == 'disetujui_ppk')
                                    <span class="inline-block px-3 py-1 bg-[#F0FDF4] text-[#15803D] border border-[#BBF7D0] rounded-full text-[10px] font-bold uppercase tracking-wider">Disetujui PPK</span>
                                @elseif($ktk->status == 'ditolak')
                                    <span class="inline-block px-3 py-1 bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] rounded-full text-[10px] font-bold uppercase tracking-wider">Ditolak</span>
                                @elseif($ktk->status == 'diverifikasi_konsultan')
                                    <span class="inline-block px-3 py-1 bg-[#EFF6FF] text-[#1D4ED8] border border-[#BFDBFE] rounded-full text-[10px] font-bold uppercase tracking-wider">Diverifikasi Konsultan</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-[#FFF8E1] text-[#F57F17] border border-[#FFE082] rounded-full text-[10px] font-bold uppercase tracking-wider">Diajukan</span>
                                @endif
                            </td>
                            
                            @if(auth()->user()->isKonsultan())
                            <td class="px-6 py-4 text-center">
                                @if($ktk->status == 'diajukan')
                                <div class="flex gap-2 justify-center" x-data="{ openReview: false }">
                                    <button @click="openReview = true" class="bg-[#0F172B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity">
                                        Review Kelayakan
                                    </button>

                                    <!-- Modal Review Konsultan -->
                                    <div x-show="openReview" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                        <div @click.away="openReview = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left">
                                            <h3 class="text-lg font-bold text-[#0F172B] mb-4">Verifikasi KTK oleh Konsultan</h3>
                                            <div class="mb-4 text-sm text-[#64748B]">
                                                <strong>Deskripsi:</strong><br>
                                                {{ $ktk->deskripsi_pekerjaan }}
                                            </div>
                                            
                                            <form method="POST" id="form-review-{{ $ktk->id }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Catatan Konsultan (Opsional)</label>
                                                    <textarea name="catatan_konsultan" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm"></textarea>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="openReview = false" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B]">Batal</button>
                                                    <button type="submit" formaction="{{ route('kerja-tambah-kurang.reject-konsultan', $ktk) }}" class="px-4 py-2 text-sm bg-red-50 text-red-600 border border-red-200 rounded font-semibold hover:bg-red-100">Tolak</button>
                                                    <button type="submit" formaction="{{ route('kerja-tambah-kurang.verify', $ktk) }}" class="px-4 py-2 text-sm bg-[#FFA000] text-[#0F172B] rounded font-bold hover:bg-opacity-90">Verifikasi & Teruskan ke PPK</button>
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

                            @if(auth()->user()->isPPK())
                            <td class="px-6 py-4 text-center">
                                @if($ktk->status == 'diverifikasi_konsultan')
                                <div class="flex gap-2 justify-center" x-data="{ openReviewPPK: false }">
                                    <button @click="openReviewPPK = true" class="bg-[#0F172B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity">
                                        Approval
                                    </button>

                                    <!-- Modal Review PPK -->
                                    <div x-show="openReviewPPK" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                        <div @click.away="openReviewPPK = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left">
                                            <h3 class="text-lg font-bold text-[#0F172B] mb-4">Approval KTK oleh PPK</h3>
                                            <div class="mb-4 text-sm text-[#64748B]">
                                                <strong>Catatan Konsultan:</strong><br>
                                                {{ $ktk->catatan_konsultan ?: '-' }}
                                            </div>
                                            
                                            <form method="POST" id="form-review-ppk-{{ $ktk->id }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Catatan PPK (Opsional)</label>
                                                    <textarea name="catatan_ppk" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm"></textarea>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="openReviewPPK = false" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B]">Batal</button>
                                                    <button type="submit" formaction="{{ route('kerja-tambah-kurang.reject-ppk', $ktk) }}" class="px-4 py-2 text-sm bg-red-50 text-red-600 border border-red-200 rounded font-semibold hover:bg-red-100">Tolak</button>
                                                    <button type="submit" formaction="{{ route('kerja-tambah-kurang.approve', $ktk) }}" class="px-4 py-2 text-sm bg-[#FFA000] text-[#0F172B] rounded font-bold hover:bg-opacity-90">Setujui KTK</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @else
                                    <span class="text-xs text-[#64748B] italic">@if($ktk->status == 'diajukan') Menunggu Konsultan @else Selesai @endif</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-[#64748B] italic">Belum ada data KTK.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(auth()->user()->isKontraktor())
        <!-- Modal Form Input KTK -->
        <x-modal name="create-ktk" focusable>
            <form method="POST" action="{{ route('kerja-tambah-kurang.store') }}" class="p-6">
                @csrf
                <h2 class="text-lg font-bold text-[#0F172B] mb-4">Buat Usulan KTK Baru</h2>
                
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Pilih Proyek</label>
                    <select name="proyek_id" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]">
                        <option value="">-- Pilih Proyek --</option>
                        @foreach($proyeks as $proyek)
                            <option value="{{ $proyek->id }}">{{ $proyek->nama_proyek }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4 flex gap-4">
                    <div class="w-1/2">
                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Nomor Surat Pengajuan</label>
                        <input type="text" name="nomor_surat_pengajuan" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]">
                    </div>
                    <div class="w-1/2">
                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Jenis KTK</label>
                        <select name="jenis_ktk" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]">
                            <option value="tambah">Pekerjaan Tambah</option>
                            <option value="kurang">Pekerjaan Kurang</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Deskripsi Pekerjaan</label>
                    <textarea name="deskripsi_pekerjaan" required rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]" placeholder="Detail perubahan pekerjaan..."></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Nilai Estimasi (Rp)</label>
                    <input type="number" name="nilai_estimasi" required min="0" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B] text-right" placeholder="Misal: 50000000">
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
