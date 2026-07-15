<x-app-layout>
    <x-slot name="title">Dashboard Super Admin</x-slot>

    <div class="w-full font-sans">
        <!-- THE "WHITE PAGE" CONTAINER -->
        <div class="bg-white p-6 md:p-10 min-h-[calc(100vh-64px)] md:min-h-[calc(100vh-80px)]">
            
            <!-- HEADER -->
            <div class="flex justify-between items-center mb-8 border-b border-gray-200 pb-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Dashboard Super Admin</h1>
                    <p class="text-sm text-gray-500 mt-1">Ringkasan sistem dan manajemen pengguna.</p>
                </div>
            </div>

            <!-- STATS ROW -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-10">
                @php
                    $statItems = [
                        ['label'=>'Total Proyek', 'val'=>$stats['total_proyek'], 'color'=>'text-[#1E3A8A]', 'bg'=>'bg-gray-50', 'border'=>'border-gray-200'],
                        ['label'=>'Proyek Aktif', 'val'=>$stats['proyek_aktif'], 'color'=>'text-[#1E3A8A]', 'bg'=>'bg-gray-50', 'border'=>'border-gray-200'],
                        ['label'=>'Total User', 'val'=>$stats['total_user'], 'color'=>'text-[#1E3A8A]', 'bg'=>'bg-gray-50', 'border'=>'border-gray-200'],
                        ['label'=>'Kontraktor', 'val'=>$stats['total_kontraktor'], 'color'=>'text-[#1E3A8A]', 'bg'=>'bg-gray-50', 'border'=>'border-gray-200'],
                        ['label'=>'Konsultan', 'val'=>$stats['total_konsultan'], 'color'=>'text-[#1E3A8A]', 'bg'=>'bg-gray-50', 'border'=>'border-gray-200'],
                        ['label'=>'PPK', 'val'=>$stats['total_ppk'], 'color'=>'text-[#1E3A8A]', 'bg'=>'bg-gray-50', 'border'=>'border-gray-200'],
                    ];
                @endphp
                @foreach($statItems as $s)
                <div class="{{ $s['bg'] }} {{ $s['border'] }} border rounded-lg p-5 text-center transition-colors">
                    <div class="text-3xl font-black {{ $s['color'] }}">{{ $s['val'] }}</div>
                    <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">{{ $s['label'] }}</div>
                </div>
                @endforeach
            </div>

            <div class="flex flex-col gap-10">
                <!-- DAFTAR PROYEK -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">Daftar Proyek</h3>
                        <a href="{{ route('admin.proyeks.create') }}" class="bg-[#1E3A8A] text-[#FFB800] text-xs font-bold px-4 py-2 rounded border border-[#1E3A8A] hover:bg-[#152e70] transition-colors">
                            + Tambah Proyek
                        </a>
                    </div>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm text-left text-gray-700">
                            <thead class="text-[11px] text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Nama Proyek</th>
                                    <th class="px-6 py-4 font-semibold">Status</th>
                                    <th class="px-6 py-4 font-semibold">Kontraktor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($proyeks->take(8) as $p)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $p->nama_proyek }}</div>
                                        <div class="text-xs text-gray-500">{{ $p->lokasi }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-block px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded border {{ $p->status === 'aktif' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-100 text-gray-700 border-gray-300' }}">
                                            {{ ucfirst($p->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-900">{{ $p->kontraktor->name ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-500 italic">Belum ada proyek.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if($proyeks->count() > 8)
                        <div class="bg-gray-50 border-t border-gray-200 px-6 py-3 text-right rounded-b-lg">
                            <a href="{{ route('admin.proyeks.index') }}" class="text-sm font-bold text-[#1E3A8A] hover:underline transition-colors">Kelola Proyek &rarr;</a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- DAFTAR USER -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">Pengguna Sistem</h3>
                        <a href="{{ route('admin.users.create') }}" class="bg-[#1E3A8A] text-[#FFB800] text-xs font-bold px-4 py-2 rounded border border-[#1E3A8A] hover:bg-[#152e70] transition-colors">
                            + Tambah User
                        </a>
                    </div>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm text-left text-gray-700">
                            <thead class="text-[11px] text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Nama</th>
                                    <th class="px-6 py-4 font-semibold">Email</th>
                                    <th class="px-6 py-4 font-semibold">Peran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($users->take(8) as $u)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $u->name }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $u->email }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-block px-3 py-1 bg-gray-100 text-[#1E3A8A] border border-gray-300 rounded text-[10px] font-bold uppercase tracking-wider">
                                            {{ $u->role_label }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-500 italic">Belum ada user.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if($users->count() > 8)
                        <div class="bg-gray-50 border-t border-gray-200 px-6 py-3 text-right rounded-b-lg">
                            <a href="{{ route('admin.users.index') }}" class="text-sm font-bold text-[#1E3A8A] hover:underline transition-colors">Kelola User &rarr;</a>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
