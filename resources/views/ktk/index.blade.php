<x-app-layout>
    <x-slot name="title">Pekerjaan Tambah Kurang (PTK)</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Pekerjaan Tambah Kurang (PTK)</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar usulan pekerjaan tambah / kurang (Change Order).</p>
            </div>
            
            @if(auth()->user()->isKontraktor() || auth()->user()->isPPTK())
            <button x-data="" x-on:click="$dispatch('open-modal', 'create-ktk')" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity shadow-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Usulan PTK
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
                            <th class="px-6 py-3 font-semibold">Jenis PTK</th>
                            <th class="px-6 py-3 font-semibold text-right">Nilai Estimasi</th>
                            <th class="px-6 py-3 font-semibold text-center">Status</th>
                            <th class="px-6 py-3 font-semibold text-center">Aksi</th>
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
                                <br><span class="text-[10px] text-[#64748B]">Usulan: {{ strtoupper($ktk->usulan_dari) }}</span>
                            </td>
                            <td class="px-6 py-4 text-[#0F172B] font-mono font-bold text-right">Rp {{ number_format($ktk->nilai_estimasi, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($ktk->status == 'disetujui')
                                    <span class="inline-block px-3 py-1 bg-[#F0FDF4] text-[#15803D] border border-[#BBF7D0] rounded-full text-[10px] font-bold uppercase tracking-wider">Disetujui</span>
                                @elseif($ktk->status == 'revisi')
                                    <span class="inline-block px-3 py-1 bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] rounded-full text-[10px] font-bold uppercase tracking-wider">Revisi</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-[#FFF8E1] text-[#F57F17] border border-[#FFE082] rounded-full text-[10px] font-bold uppercase tracking-wider">Menunggu Validasi</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @php
                                    $user = auth()->user();
                                    $canApprove = false;
                                    if ($ktk->status == 'menunggu_validasi') {
                                        if ($user->isKontraktor() && $ktk->usulan_dari == 'pptk') $canApprove = true;
                                        if ($user->isPPTK() && $ktk->usulan_dari == 'kontraktor') $canApprove = true;
                                    }
                                @endphp

                                @if($canApprove)
                                <div class="flex gap-2 justify-center" x-data="{ openReview: false }">
                                    <button type="button" @click="openReview = true" class="bg-[#0F172B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity">
                                        Validasi
                                    </button>

                                    <template x-teleport="body">
                                        <div x-show="openReview" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                            <div class="absolute inset-0" @click="openReview = false"></div>
                                            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left relative z-[101]">
                                                <h3 class="text-lg font-bold text-[#0F172B] mb-4">Validasi PTK</h3>
                                                <div class="mb-4 text-sm text-[#64748B]">
                                                    <strong>Deskripsi Pekerjaan:</strong><br>
                                                    {{ $ktk->deskripsi_pekerjaan }}
                                                </div>
                                                
                                                <form method="POST" action="{{ route('kerja-tambah-kurang.approve', $ktk) }}">
                                                    @csrf
                                                    <div class="mb-4">
                                                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Catatan Evaluasi (Opsional)</label>
                                                        <textarea name="catatan_evaluasi" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm"></textarea>
                                                    </div>
                                                    <div class="flex justify-end gap-3">
                                                        <button type="button" @click="openReview = false" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B]">Batal</button>
                                                        <button type="submit" formaction="{{ route('kerja-tambah-kurang.reject', $ktk) }}" class="px-4 py-2 text-sm bg-red-50 text-red-600 border border-red-200 rounded font-semibold hover:bg-red-100">Revisi</button>
                                                        <button type="submit" class="px-4 py-2 text-sm bg-[#FFA000] text-[#0F172B] rounded font-bold hover:bg-opacity-90">Validasi PTK</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                @else
                                <div class="flex gap-2 justify-center" x-data="{ openDetail: false }">
                                    <button type="button" @click="openDetail = true" class="bg-[#64748B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity">
                                        Lihat Detail
                                    </button>

                                    <template x-teleport="body">
                                        <div x-show="openDetail" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                            <div class="absolute inset-0" @click="openDetail = false"></div>
                                            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left relative z-[101]">
                                                <h3 class="text-lg font-bold text-[#0F172B] mb-4">Detail Usulan PTK</h3>
                                                <div class="mb-4 text-sm text-[#64748B]">
                                                    <strong>Deskripsi Pekerjaan:</strong><br>
                                                    {{ $ktk->deskripsi_pekerjaan }}
                                                </div>
                                                @if($ktk->catatan_evaluasi)
                                                <div class="mb-4 text-sm text-[#64748B]">
                                                    <strong>Catatan Evaluasi:</strong><br>
                                                    {{ $ktk->catatan_evaluasi }}
                                                </div>
                                                @endif
                                                <div class="flex justify-end mt-6">
                                                    <button type="button" @click="openDetail = false" class="px-4 py-2 text-sm bg-[#64748B] text-white rounded font-medium hover:bg-opacity-90">Tutup</button>
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
                            <td colspan="6" class="px-6 py-8 text-center text-[#64748B] italic">Belum ada data PTK.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        @if(auth()->user()->isKontraktor() || auth()->user()->isPPTK())
        <!-- Modal Form Input PTK -->
        <x-modal name="create-ktk" focusable>
            <form method="POST" action="{{ route('kerja-tambah-kurang.store') }}" class="p-6">
                @csrf
                <h2 class="text-lg font-bold text-[#0F172B] mb-4">Buat Usulan PTK Baru</h2>
                
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
                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Jenis PTK</label>
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
