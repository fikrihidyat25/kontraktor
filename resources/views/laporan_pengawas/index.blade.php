<x-app-layout>
    <x-slot name="title">Laporan Pengawas</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Laporan Pengawas</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar laporan mingguan, bulanan, dan akhir pengawasan dari konsultan.</p>
            </div>
            
            @if(auth()->user()->isKonsultan())
            <button x-data="" x-on:click="$dispatch('open-modal', 'create-laporan-pengawas')" class="bg-[#FFA000] text-[#0F172B] px-5 py-2.5 rounded-md text-sm font-bold hover:bg-opacity-90 transition-opacity shadow-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Laporan Baru
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
                            <th class="px-6 py-3 font-semibold">Tgl Upload</th>
                            <th class="px-6 py-3 font-semibold">Proyek</th>
                            <th class="px-6 py-3 font-semibold text-center">Jenis</th>
                            <th class="px-6 py-3 font-semibold">Dokumen</th>
                            <th class="px-6 py-3 font-semibold text-center">Status</th>
                            
                            @if(auth()->user()->isPPTK() || auth()->user()->isPPK())
                                <th class="px-6 py-3 font-semibold text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporans as $lp)
                        <tr class="border-b border-[#E7E3DC] even:bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors">
                            <td class="px-6 py-4 text-[#0F172B] whitespace-nowrap">{{ $lp->created_at->isoFormat('D MMM Y, HH:mm') }}</td>
                            <td class="px-6 py-4 text-[#64748B] text-xs font-medium">{{ $lp->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-[10px] font-bold uppercase tracking-wider">{{ $lp->jenis }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ asset('storage/' . $lp->file_laporan) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline flex items-center">
                                    Lihat File
                                </a>
                                @if($lp->catatan)
                                    <div class="text-[11px] text-gray-500 mt-1">{{ Str::limit($lp->catatan, 30) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($lp->status == 'disetujui')
                                    <span class="inline-block px-3 py-1 bg-[#F0FDF4] text-[#15803D] border border-[#BBF7D0] rounded-full text-[10px] font-bold uppercase tracking-wider">Disetujui</span>
                                @elseif($lp->status == 'ditolak')
                                    <span class="inline-block px-3 py-1 bg-[#FEF2F2] text-[#DC2626] border border-[#FECACA] rounded-full text-[10px] font-bold uppercase tracking-wider">Ditolak</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-[#FFF8E1] text-[#F57F17] border border-[#FFE082] rounded-full text-[10px] font-bold uppercase tracking-wider">Diajukan</span>
                                @endif
                            </td>
                            
                            @if(auth()->user()->isPPTK() || auth()->user()->isPPK())
                            <td class="px-6 py-4 text-center">
                                @if(auth()->user()->isPPTK() && $lp->status == 'diajukan')
                                <div class="flex gap-2 justify-center">
                                    <form method="POST" action="{{ route('laporan-pengawas.approve', $lp) }}">
                                        @csrf
                                        <button type="submit" class="bg-[#15803D] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 shadow-sm">Setujui</button>
                                    </form>
                                    <form method="POST" action="{{ route('laporan-pengawas.reject', $lp) }}">
                                        @csrf
                                        <button type="submit" class="bg-red-600 text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 shadow-sm">Tolak</button>
                                    </form>
                                </div>
                                @else
                                    @if($lp->status == 'diajukan')
                                        <span class="text-xs text-[#64748B] italic">Menunggu Validasi PPTK</span>
                                    @else
                                        <span class="text-xs text-[#64748B] italic">Direview oleh {{ $lp->verifier->name ?? '-' }}</span>
                                    @endif
                                @endif
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-[#64748B] italic">Belum ada laporan pengawas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(auth()->user()->isKonsultan())
        <x-modal name="create-laporan-pengawas" focusable>
            <form method="POST" action="{{ route('laporan-pengawas.store') }}" class="p-6" enctype="multipart/form-data">
                @csrf
                <h2 class="text-lg font-bold text-[#0F172B] mb-4">Buat Laporan Pengawas</h2>
                
                @if(isset($proyeks) && $proyeks->isNotEmpty())
                    @php $proyek = $proyeks->first(); @endphp
                    <input type="hidden" name="proyek_id" value="{{ $proyek->id }}">
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Proyek</label>
                        <input type="text" value="{{ $proyek->nama_proyek }}" readonly class="w-full border-[#E7E3DC] rounded bg-gray-50 text-sm text-[#64748B] cursor-not-allowed">
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Jenis Laporan</label>
                    <select name="jenis" required class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]">
                        <option value="mingguan">Mingguan</option>
                        <option value="bulanan">Bulanan</option>
                        <option value="akhir">Akhir (Final)</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">File Laporan (PDF/ZIP/RAR)</label>
                    <input type="file" name="file_laporan" required accept=".pdf,.zip,.rar" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Catatan (Opsional)</label>
                    <textarea name="catatan" rows="3" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B]"></textarea>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 text-sm text-[#64748B] font-medium hover:text-[#0F172B] mr-3">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#FFA000] text-[#0F172B] px-5 py-2 rounded text-sm font-bold hover:bg-opacity-90 shadow-sm">
                        Ajukan Laporan
                    </button>
                </div>
            </form>
        </x-modal>
        @endif

    </div>
</x-app-layout>
