<x-app-layout>
    <x-slot name="title">Lengkapi Data Laporan Bulanan</x-slot>

    <div class="w-full font-sans">
        
        <div class="flex justify-between items-center mb-8 bg-white p-6 md:px-10 border-b border-gray-200">
            <div>
                <a href="{{ route('laporan-bulanan.index') }}" class="text-sm text-blue-600 hover:underline mb-2 inline-block">← Kembali ke Daftar</a>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Lengkapi Data Laporan Bulanan</h1>
                <p class="text-sm text-[#64748B] mt-1">Tambahkan lampiran atau dokumentasi ekstra untuk laporan bulan {{ $laporanBulanan->bulan_label }}</p>
            </div>
        </div>

        <div class="p-6 md:p-10 max-w-4xl">
            @if($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded mb-6 border border-red-200">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('laporan-bulanan.update', $laporanBulanan) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50 rounded border border-gray-200">
                    <div>
                        <span class="block text-xs font-bold text-gray-500 uppercase">Proyek</span>
                        <div class="font-medium text-gray-900 mt-1">{{ $laporanBulanan->proyek->nama_proyek }}</div>
                    </div>
                    <div>
                        <span class="block text-xs font-bold text-gray-500 uppercase">Kumulatif Realisasi</span>
                        <div class="font-medium text-green-600 mt-1">{{ $laporanBulanan->bobot_realisasi }}%</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-[#0F172B] mb-2">Lampiran Tambahan (Optional)</label>
                    <input type="file" name="lampiran_tambahan" class="w-full text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Format: PDF, Word, Excel, ZIP (Max 10MB)</p>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-[#0F172B] mb-2">Dokumentasi Ekstra (Optional)</label>
                    <input type="file" name="dokumentasi_tambahan[]" multiple class="w-full text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Bisa pilih lebih dari satu file (Max 10MB per file).</p>
                </div>

                <div class="pt-6 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition-colors">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
