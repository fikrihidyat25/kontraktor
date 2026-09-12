<x-app-layout>
    <x-slot name="title">Pengajuan Uang Muka</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Pengajuan Uang Muka</h1>
                <p class="text-sm text-[#64748B] mt-1">Daftar permohonan pencairan uang muka proyek.</p>
            </div>
            
            @if(auth()->user()->isKontraktor() && isset($canSubmit) && $canSubmit)
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
                            
                            <th class="px-6 py-3 font-semibold text-center">Riwayat / Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($uangMukas as $u)
                        <tr class="border-b border-[#E7E3DC] even:bg-[#F8FAFC] hover:bg-[#F1F5F9] transition-colors">
                            <td class="px-6 py-4 text-[#0F172B] whitespace-nowrap">{{ $u->tanggal_pengajuan->isoFormat('D MMM Y') }}</td>
                            <td class="px-6 py-4 text-[#64748B] text-xs font-medium">
                                {{ $u->proyek->nama_proyek }}<br>
                                <span class="text-[10px] text-gray-500 font-normal">Kontrak: {{ $u->proyek->nomor_kontrak ?? '-' }}</span>
                            </td>
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
                            
                            <td class="px-6 py-4 text-center">
                                  @if(auth()->user()->isPPTK() && $u->status == 'menunggu_persetujuan')
                                  <div class="flex gap-2 justify-center" x-data="{ openReview: false }">
                                      <button @click="openReview = true" class="bg-[#0F172B] text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-opacity-90 transition-opacity">
                                          Review
                                      </button>
  
                                      <!-- Modal Review PPTK -->
                                      <div x-show="openReview" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                          <div @click.away="openReview = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left">
                                              <div class="flex justify-between items-center mb-4">
                                                  <h3 class="text-lg font-bold text-[#0F172B]">Review Uang Muka</h3>
                                                  <button type="button" @click="openReview = false" class="text-gray-400 hover:text-gray-600">
                                                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                  </button>
                                              </div>
                                              
                                              <div class="mb-4 text-sm text-[#64748B]">
                                                  <p class="mb-2"><strong class="text-[#0F172B]">Nilai Diajukan:</strong> Rp {{ number_format($u->nilai_pengajuan, 0, ',', '.') }}</p>
                                              </div>
  
                                              <div class="mb-4">
                                                  <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Dokumen Terlampir</label>
                                                  @if($u->surat_permohonan)
                                                      <a href="{{ Storage::url($u->surat_permohonan) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1 flex items-center">
                                                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                          Surat Permohonan
                                                      </a>
                                                  @endif
                                                  
                                                  @if(!empty($u->lampiran) && is_array($u->lampiran))
                                                      @foreach($u->lampiran as $index => $file)
                                                          <a href="{{ Storage::url($file) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1 flex items-center">
                                                              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                              Lampiran {{ $index + 1 }}
                                                          </a>
                                                      @endforeach
                                                  @elseif(is_string($u->lampiran))
                                                      <a href="{{ Storage::url($u->lampiran) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1 flex items-center">
                                                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                          Lampiran Tambahan
                                                      </a>
                                                  @endif
                                              </div>
                                              
                                              <form method="POST" id="form-review-{{ $u->id }}">
                                                  @csrf
                                                  <div class="mb-4">
                                                      <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Nomor Kontrak (Jika Belum Ada)</label>
                                                      <input type="text" name="nomor_kontrak" value="{{ $u->proyek->nomor_kontrak }}" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm" placeholder="Masukkan Nomor Kontrak...">
                                                  </div>
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
                                  <div class="flex gap-2 justify-center" x-data="{ openDetail: false }">
                                      <button @click="openDetail = true" class="bg-[#F1F5F9] text-[#64748B] hover:text-[#0F172B] px-3 py-1.5 rounded text-xs font-semibold border border-[#E7E3DC] transition-colors">
                                          Riwayat / Detail
                                      </button>
                                      
                                      <!-- Modal Detail -->
                                      <div x-show="openDetail" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
                                          <div @click.away="openDetail = false" class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md text-left">
                                              <div class="flex justify-between items-center mb-4">
                                                  <h3 class="text-lg font-bold text-[#0F172B]">Detail Pengajuan</h3>
                                                  <button type="button" @click="openDetail = false" class="text-gray-400 hover:text-gray-600">
                                                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                  </button>
                                              </div>
                                              
                                              <div class="mb-4 text-sm text-[#64748B]">
                                                  <p class="mb-2"><strong class="text-[#0F172B]">Nilai Diajukan:</strong> Rp {{ number_format($u->nilai_pengajuan, 0, ',', '.') }}</p>
                                                  <p class="mb-2"><strong class="text-[#0F172B]">Status:</strong> <span class="uppercase font-bold text-xs">{{ $u->status }}</span></p>
                                              </div>
  
                                              <div class="mb-4">
                                                  <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Dokumen Terlampir</label>
                                                  @if($u->surat_permohonan)
                                                      <a href="{{ Storage::url($u->surat_permohonan) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1 flex items-center">
                                                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                          Surat Permohonan
                                                      </a>
                                                  @endif
                                                  
                                                  @if(!empty($u->lampiran) && is_array($u->lampiran))
                                                      @foreach($u->lampiran as $index => $file)
                                                          <a href="{{ Storage::url($file) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1 flex items-center">
                                                              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                              Lampiran {{ $index + 1 }}
                                                          </a>
                                                      @endforeach
                                                  @elseif(is_string($u->lampiran))
                                                      <a href="{{ Storage::url($u->lampiran) }}" target="_blank" class="text-xs font-bold text-[#1E3A8A] hover:underline block mb-1 flex items-center">
                                                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                          Lampiran Tambahan
                                                      </a>
                                                  @endif
                                              </div>
  
                                              @if($u->catatan_ppk)
                                              <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded">
                                                  <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-1">Catatan Validasi</label>
                                                  <p class="text-sm text-[#0F172B] italic">"{{ $u->catatan_ppk }}"</p>
                                              </div>
                                              @endif
  
                                              <div class="flex justify-end mt-4">
                                                  <button type="button" @click="openDetail = false" class="px-4 py-2 text-sm bg-[#64748B] text-white rounded font-medium hover:bg-opacity-90 transition-colors">Tutup</button>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  @endif
                              </td>
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
            <form method="POST" action="{{ route('uang-muka.store') }}" class="p-6" enctype="multipart/form-data">
                @csrf
                <h2 class="text-lg font-bold text-[#0F172B] mb-4">Buat Pengajuan Uang Muka Baru</h2>
                
                @if(isset($proyeks) && $proyeks->isNotEmpty())
                    @php $proyek = $proyeks->first(); @endphp
                    <input type="hidden" name="proyek_id" value="{{ $proyek->id }}">
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Proyek & Nomor Kontrak</label>
                        <input type="text" value="{{ $proyek->nama_proyek }} (Kontrak: {{ $proyek->nomor_kontrak ?? '-' }})" readonly class="w-full border-[#E7E3DC] rounded bg-gray-50 text-sm text-[#64748B] cursor-not-allowed">
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Nilai Pengajuan (Rp)</label>
                    <input type="number" name="nilai_pengajuan" required min="1" class="w-full border-[#E7E3DC] rounded focus:ring-[#FFA000] focus:border-[#FFA000] text-sm text-[#0F172B] text-right" placeholder="0">
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Surat Permohonan (Wajib, Max 10MB)</label>
                    <input type="file" name="surat_permohonan" required accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png" onchange="if(this.files[0].size > 10485760){ alert('Ukuran file maksimal 10MB!'); this.value = ''; }" class="w-full text-sm text-[#64748B] file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                </div>

                <div class="mb-6" x-data="{ lampiranCount: [1] }">
                    <label class="block text-xs font-semibold text-[#64748B] uppercase tracking-wider mb-2">Lampiran Dokumen / Laporan / Foto (Opsional, Max 10MB/file)</label>
                    <template x-for="(item, index) in lampiranCount" :key="index">
                        <div class="flex items-center gap-2 mb-2">
                            <input type="file" name="lampiran[]" accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png" onchange="if(this.files[0] && this.files[0].size > 10485760){ alert('Ukuran file maksimal 10MB!'); this.value = ''; }" class="flex-1 text-sm text-[#64748B] border border-[#E7E3DC] rounded p-1 file:mr-4 file:py-1.5 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-[#F1F5F9] file:text-[#0F172B] hover:file:bg-[#E2E8F0]">
                            <button type="button" @click="lampiranCount.splice(index, 1)" x-show="lampiranCount.length > 1" class="text-red-500 hover:text-red-700 p-1.5 focus:outline-none" title="Hapus baris">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="lampiranCount.push(Date.now())" class="mt-1 text-sm text-[#1E3A8A] font-bold hover:underline flex items-center focus:outline-none">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah File Lainnya
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
