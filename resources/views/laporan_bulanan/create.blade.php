<x-app-layout>
    <x-slot name="title">Input Laporan Bulanan</x-slot>

    <div class="w-full font-sans bg-white p-6 md:p-10 min-h-[calc(100vh-64px)] md:min-h-[calc(100vh-80px)]">
        
        <div class="mb-8 border-b border-gray-200 pb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Input Laporan Bulanan</h1>
            <p class="text-sm text-[#64748B] mt-1">Isi realisasi dan deviasi fisik proyek bulan ini.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-600 text-red-700 p-4 mb-6 rounded shadow-sm text-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('laporan-bulanan.store') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl bg-gray-50 p-6 md:p-8 rounded-lg border border-gray-200">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Proyek -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Proyek <span class="text-red-500">*</span></label>
                    <select name="proyek_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1E3A8A] focus:ring focus:ring-[#1E3A8A] focus:ring-opacity-50">
                        <option value="">-- Pilih Proyek --</option>
                        @foreach($proyeks as $p)
                            <option value="{{ $p->id }}" {{ old('proyek_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_proyek }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Bulan & Tahun -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Bulan <span class="text-red-500">*</span></label>
                    <select name="bulan" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1E3A8A] focus:ring focus:ring-[#1E3A8A] focus:ring-opacity-50">
                        <option value="">-- Bulan --</option>
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ (old('bulan') ?? date('n')) == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tahun <span class="text-red-500">*</span></label>
                    <input type="number" name="tahun" value="{{ old('tahun') ?? date('Y') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1E3A8A] focus:ring focus:ring-[#1E3A8A] focus:ring-opacity-50">
                </div>

                <!-- Bobot Rencana & Realisasi -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Bobot Rencana (Kompulatif) % <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="bobot_rencana" value="{{ old('bobot_rencana') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1E3A8A] focus:ring focus:ring-[#1E3A8A] focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Bobot Realisasi (Kompulatif) % <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="bobot_realisasi" value="{{ old('bobot_realisasi') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1E3A8A] focus:ring focus:ring-[#1E3A8A] focus:ring-opacity-50">
                </div>
            </div>

            <!-- Ringkasan Kemajuan -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Ringkasan Kemajuan Fisik</label>
                <textarea name="ringkasan_kemajuan" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1E3A8A] focus:ring focus:ring-[#1E3A8A] focus:ring-opacity-50">{{ old('ringkasan_kemajuan') }}</textarea>
            </div>

            <!-- Kendala -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Kendala & Masalah (Jika Ada)</label>
                <textarea name="kendala" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#1E3A8A] focus:ring focus:ring-[#1E3A8A] focus:ring-opacity-50">{{ old('kendala') }}</textarea>
            </div>

            <!-- File Upload -->
            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-700 mb-2">Upload Dokumen Laporan (PDF, Max 10MB)</label>
                <input type="file" name="file_laporan" accept=".pdf" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#1E3A8A] file:text-[#FFB800] hover:file:bg-[#152e70] transition">
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-[#1E3A8A] text-[#FFB800] px-6 py-3 rounded text-sm font-bold shadow-md hover:bg-[#152e70] transition">
                    Simpan Laporan (Draft)
                </button>
                <a href="{{ route('laporan-bulanan.index') }}" class="text-gray-500 hover:text-gray-800 text-sm font-medium">Batal</a>
            </div>
        </form>

    </div>
</x-app-layout>
