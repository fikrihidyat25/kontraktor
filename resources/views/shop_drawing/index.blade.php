<x-app-layout>
    <x-slot name="title">Shop Drawing</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Shop Drawing</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar pengajuan Shop Drawing proyek.</p>
            </div>
            
            @if(auth()->user()->isKontraktor())
            <button x-data="" x-on:click="$dispatch('open-modal', 'create-shop-drawing')" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity shadow-sm flex items-center">
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
        @if($errors->any())
            <div class="bg-[#FEF2F2] border-l-4 border-[#DC2626] text-[#DC2626] p-4 mb-6 rounded shadow-sm text-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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
                            <th class="px-6 py-3 font-semibold">Judul Gambar</th>
                            <th class="px-6 py-3 font-semibold text-center">Status</th>
                            
                            @if((auth()->user()->isPPTK() || auth()->user()->isKonsultan()))
                                <th class="px-6 py-3 font-semibold text-center">Aksi (Validasi)</th>
                            @else
                                <th class="px-6 py-3 font-semibold text-center">Detail</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shopDrawings as $sd)
                        <tr class="border-b border-[#E7E3DC] even:bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors">
                            <td class="px-6 py-4 text-[#0F172B] whitespace-nowrap">{{ $sd->created_at->isoFormat('D MMM Y') }}</td>
                            <td class="px-6 py-4 text-[#64748B] text-xs font-medium">{{ $sd->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4 text-[#0F172B] text-xs">
                                <strong>{{ $sd->judul }}</strong><br>
                                <span class="text-gray-500">{{ Str::limit($sd->keterangan, 50) }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($sd->status == 'disetujui')
                                    <span class="inline-block px-3 py-1 bg-[#F0FDF4] text-[#15803D] border border-[#BBF7D0] rounded-full text-[10px] font-bold uppercase tracking-wider">Disetujui</span>
                                @elseif($sd->status == 'ditolak')
                                    <span class="inline-block px-3 py-1 bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] rounded-full text-[10px] font-bold uppercase tracking-wider">Ditolak</span>
                                @elseif($sd->status == 'diverifikasi_konsultan')
                                    <span class="inline-block px-3 py-1 bg-[#E0F2FE] text-[#0369A1] border border-[#BAE6FD] rounded-full text-[10px] font-bold uppercase tracking-wider">Verifikasi Konsultan</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-[#FFF8E1] text-[#F57F17] border border-[#FFE082] rounded-full text-[10px] font-bold uppercase tracking-wider">Diajukan</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @if((auth()->user()->isKonsultan() && $sd->status == 'diajukan') || (auth()->user()->isPPTK() && $sd->status == 'diverifikasi_konsultan'))
                                <div class="flex gap-2 justify-center" x-data="{ openReview: false }">
                                    <button @click="openReview = true" class="bg-[#0F172B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity">
                                        Review
                                    </button>

                                    <!-- Modal Review Validasi -->
                                    <div x-show="openReview" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                        <div @click.away="openReview = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left">
                                            <h3 class="text-lg font-bold text-[#0F172B] mb-4">Review Shop Drawing</h3>
                                            
                                            <div class="mb-4 text-sm text-[#64748B]">
                                                <strong>Judul:</strong> {{ $sd->judul }}<br>
                                                <strong>Keterangan:</strong><br>
                                                {{ $sd->keterangan ?: '-' }}
                                            </div>

                                            @if($sd->catatan_konsultan)
                                            <div class="mb-4 text-sm text-[#64748B]">
                                                <strong>Catatan Konsultan:</strong><br>
                                                {{ $sd->catatan_konsultan }}
                                            </div>
                                            @endif

                                            <div class="mb-4">
                                                <a href="{{ asset('storage/' . $sd->file_gambar) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1 flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                    Lihat Gambar / Dokumen
                                                </a>
                                            </div>

                                            <form method="POST" id="form-review-{{ $sd->id }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Catatan Evaluasi</label>
                                                    <textarea name="catatan_pptk" required rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm" placeholder="Berikan catatan..."></textarea>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="openReview = false" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B]">Batal</button>
                                                    <button type="submit" formaction="{{ route('shop-drawing.reject', $sd) }}" class="px-4 py-2 text-sm bg-red-50 text-red-600 border border-red-200 rounded font-semibold hover:bg-red-100">Tolak</button>
                                                    <button type="submit" formaction="{{ route('shop-drawing.approve', $sd) }}" class="px-4 py-2 text-sm bg-[#FFA000] text-[#0F172B] rounded font-bold hover:bg-opacity-90">Setujui</button>
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

                                        <div x-show="openDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                            <div @click.away="openDetail = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left">
                                                <h3 class="text-lg font-bold text-[#0F172B] mb-4">Detail Shop Drawing</h3>
                                                
                                                <div class="mb-4 text-sm text-[#64748B]">
                                                    <strong>Judul:</strong> {{ $sd->judul }}<br>
                                                    <strong>Keterangan:</strong><br>
                                                    {{ $sd->keterangan ?: '-' }}<br><br>
                                                    <strong>Catatan Evaluasi:</strong><br>
                                                    {{ $sd->catatan_pptk ?: '-' }}
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <a href="{{ asset('storage/' . $sd->file_gambar) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1 flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                        Lihat Gambar / Dokumen
                                                    </a>
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
                            <td colspan="5" class="px-6 py-8 text-center text-[#64748B] italic">Belum ada pengajuan Shop Drawing.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(auth()->user()->isKontraktor())
        <!-- Modal Form Input Shop Drawing -->
        <x-modal name="create-shop-drawing" focusable>
            <form method="POST" action="{{ route('shop-drawing.store') }}" class="p-6" enctype="multipart/form-data">
                @csrf
                <h2 class="text-lg font-bold text-[#0F172B] mb-4">Buat Pengajuan Shop Drawing</h2>
                
                @if(isset($proyeks) && $proyeks->isNotEmpty())
                    @php $proyek = $proyeks->first(); @endphp
                    <input type="hidden" name="proyek_id" value="{{ $proyek->id }}">
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Proyek</label>
                        <input type="text" value="{{ $proyek->nama_proyek }}" readonly class="w-full border-[#E7E3DC] rounded bg-gray-50 text-sm text-[#64748B] cursor-not-allowed">
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Judul Gambar</label>
                    <input type="text" name="judul" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]" placeholder="Cth: Denah Pondasi, Instalasi Listrik Lantai 1...">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Keterangan (Opsional)</label>
                    <textarea name="keterangan" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]" placeholder="Keterangan tambahan..."></textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">File Gambar (Wajib, PDF/JPG/PNG, Max 15MB)</label>
                    <input type="file" name="file_gambar" required accept=".pdf,.jpg,.jpeg,.png" onchange="if(this.files[0] && this.files[0].size > 15728640){ alert('Ukuran file maksimal 15MB!'); this.value = ''; }" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B] mr-3">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#FFA000] text-[#0F172B] px-5 py-2 rounded text-sm font-bold hover:bg-opacity-90 shadow-sm">
                        Ajukan Shop Drawing
                    </button>
                </div>
            </form>
        </x-modal>
        @endif

    </div>
</x-app-layout>
