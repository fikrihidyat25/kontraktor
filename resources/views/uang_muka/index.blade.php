<x-app-layout>
    <x-slot name="title">Pengajuan Uang Muka</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Pengajuan Uang Muka</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar permohonan pencairan uang muka proyek.</p>
            </div>
            
            @if(auth()->user()->isKontraktor())
            <!-- Form Buat Pengajuan Baru (Hanya untuk Kontraktor) -->
            <button x-data="" x-on:click="$dispatch('open-modal', 'create-uang-muka')" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity shadow-sm flex items-center">
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

        <!-- Area Tabel (Single Interface) -->
        <div class="bg-white border border-[#E7E3DC] rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-[#64748B] uppercase bg-[#F8FAFC] border-b border-[#E7E3DC]">
                        <tr>
                            <th class="px-6 py-3 font-semibold">Tgl Pengajuan</th>
                            <th class="px-6 py-3 font-semibold">Proyek</th>
                            <th class="px-6 py-3 font-semibold">Kontraktor</th>
                            <th class="px-6 py-3 font-semibold text-right">Nilai Diajukan</th>
                            <th class="px-6 py-3 font-semibold text-center">Status</th>
                            
                            @if(auth()->user()->isPPK())
                                <th class="px-6 py-3 font-semibold text-center">Aksi (Owner)</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($uangMukas as $u)
                        <tr class="border-b border-[#E7E3DC] even:bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors">
                            <td class="px-6 py-4 text-[#0F172B] whitespace-nowrap">{{ $u->tanggal_pengajuan->isoFormat('D MMM Y') }}</td>
                            <td class="px-6 py-4 text-[#64748B] text-xs font-medium">{{ $u->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4 text-[#64748B] text-xs">{{ $u->kontraktor->name }}</td>
                            <td class="px-6 py-4 text-[#0F172B] font-mono font-bold text-right">Rp {{ number_format($u->nilai_pengajuan, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($u->status == 'disetujui')
                                    <span class="inline-block px-3 py-1 bg-[#F0FDF4] text-[#15803D] border border-[#BBF7D0] rounded-full text-[10px] font-bold uppercase tracking-wider">Disetujui</span>
                                @elseif($u->status == 'ditolak')
                                    <span class="inline-block px-3 py-1 bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] rounded-full text-[10px] font-bold uppercase tracking-wider">Ditolak</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-[#FFF8E1] text-[#F57F17] border border-[#FFE082] rounded-full text-[10px] font-bold uppercase tracking-wider">Menunggu</span>
                                @endif
                            </td>
                            
                            @if(auth()->user()->isPPK())
                            <td class="px-6 py-4 text-center">
                                @if($u->status == 'menunggu_persetujuan')
                                <div class="flex gap-2 justify-center" x-data="{ openReview: false }">
                                    <button @click="openReview = true" class="bg-[#0F172B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity">
                                        Review
                                    </button>

                                    <!-- Modal Review PPK -->
                                    <div x-show="openReview" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                        <div @click.away="openReview = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left">
                                            <h3 class="text-lg font-bold text-[#0F172B] mb-4">Review Pengajuan Uang Muka</h3>
                                            <p class="text-sm text-[#64748B] mb-4">Proyek: {{ $u->proyek->nama_proyek }}<br>Nilai: Rp {{ number_format($u->nilai_pengajuan, 0, ',', '.') }}</p>
                                            
                                            <form method="POST" id="form-review-{{ $u->id }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Catatan Persetujuan/Penolakan</label>
                                                    <textarea name="catatan_ppk" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm" placeholder="Opsional..."></textarea>
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="openReview = false" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B]">Batal</button>
                                                    <button type="submit" formaction="{{ route('uang-muka.reject', $u) }}" class="px-4 py-2 text-sm bg-red-50 text-red-600 border border-red-200 rounded font-semibold hover:bg-red-100">Tolak</button>
                                                    <button type="submit" formaction="{{ route('uang-muka.approve', $u) }}" class="px-4 py-2 text-sm bg-[#FFA000] text-[#0F172B] rounded font-bold hover:bg-opacity-90">Setujui</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @else
                                    <span class="text-xs text-[#64748B] italic">Telah direview</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-[#64748B] italic">Belum ada pengajuan uang muka.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(auth()->user()->isKontraktor())
        <!-- Modal Form Input Uang Muka (Alpine.js) -->
        <x-modal name="create-uang-muka" focusable>
            <form method="POST" action="{{ route('uang-muka.store') }}" class="p-6">
                @csrf
                <h2 class="text-lg font-bold text-[#0F172B] mb-4">Buat Pengajuan Uang Muka Baru</h2>
                
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Pilih Proyek</label>
                    <select name="proyek_id" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]">
                        <option value="">-- Pilih Proyek --</option>
                        @foreach($proyeks as $proyek)
                            <option value="{{ $proyek->id }}">{{ $proyek->nama_proyek }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Nilai Pengajuan (Rp)</label>
                    <input type="number" name="nilai_pengajuan" required min="1" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B] text-right" placeholder="Misal: 150000000">
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Surat Permohonan (Opsional / Dummy)</label>
                    <input type="file" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
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
