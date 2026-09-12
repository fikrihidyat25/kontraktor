<x-app-layout>
    <x-slot name="title">Dashboard Kontraktor</x-slot>

    @if($proyek)
    <div class="w-full px-4 md:px-8 py-8 font-sans">
        
        @if(session('success'))
        <div class="mb-6 bg-green-50 text-green-800 border border-green-200 rounded-sm p-4 text-sm font-medium">
            {{ session('success') }}
        </div>
        @endif

        <!-- PROYEK AKTIF INFO -->
        <div class="bg-[#F9FAFB] border border-[#E5E7EB] rounded-sm p-6 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-sm">
            <div>
                <div class="inline-flex items-center px-2 py-0.5 rounded-sm text-[10px] font-bold bg-[#FFB800] text-[#1E3A8A] uppercase tracking-wider mb-2">Proyek Aktif</div>
                <h2 class="text-xl font-bold text-[#1E3A8A]">{{ $proyek->nama_proyek }}</h2>
                <p class="text-sm text-gray-500 mt-1 font-medium">{{ $proyek->nomor_kontrak }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-bold">Nilai Kontrak</p>
                <p class="text-lg font-bold text-[#1E3A8A] mt-1">Rp {{ number_format($proyek->nilai_kontrak, 0, ',', '.') }}</p>
            </div>
        </div>


        <!-- STATS LAPORAN -->
        <div class="mb-2 text-sm font-bold text-[#1E3A8A] uppercase tracking-wider">Statistik Laporan Harian</div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['draft'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Draft</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['submitted'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Submitted</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['verified'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Verified</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['approved'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Approved</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['rejected'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Rejected</div>
            </div>
        </div>



        <!-- CHARTS SECTION -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <!-- PIE CHART -->
            <div class="bg-white border border-[#E5E7EB] rounded-sm shadow-sm p-6">
                <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider mb-4">Distribusi Status Laporan</h3>
                <div class="relative w-full h-64 flex justify-center">
                    <canvas id="statusPieChart"></canvas>
                </div>
            </div>
            
            <!-- S-CURVE CHART -->
            <div class="bg-white border border-[#E5E7EB] rounded-sm shadow-sm p-6 md:col-span-2">
                @php
                    $progressRealisasi = !empty($sCurveData['realisasi']) ? collect($sCurveData['realisasi'])->last() : 0;
                    $realisasiBiaya = ($progressRealisasi / 100) * $proyek->nilai_kontrak;
                    
                    $tanggalMulai = \Carbon\Carbon::parse($proyek->tanggal_mulai);
                    $tanggalSelesai = \Carbon\Carbon::parse($proyek->tanggal_selesai);
                    $totalHari = $tanggalMulai->diffInDays($tanggalSelesai) ?: 1;
                    $hariBerjalan = $tanggalMulai->diffInDays(now(), false);
                    
                    if ($hariBerjalan < 0) $hariBerjalan = 0;
                    if ($hariBerjalan > $totalHari) $hariBerjalan = $totalHari;
                    
                    $progressWaktu = ($hariBerjalan / $totalHari) * 100;
                @endphp

                <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider mb-4">Kurva S Kemajuan Proyek</h3>
                <div class="relative w-full h-64">
                    <canvas id="sCurveChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Laporan Tabel -->
        <div class="bg-white rounded-sm shadow-sm border border-[#E5E7EB] overflow-hidden">
            <div class="bg-gray-50 border-b border-[#E5E7EB] px-6 py-4 flex justify-between items-center">
                <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">Laporan Harian Terbaru (Proyek Aktif)</h3>
                <a href="{{ route('laporan-harian.index') }}" class="text-xs font-bold text-[#1E3A8A] hover:underline">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="text-[11px] text-gray-500 uppercase bg-white border-b border-[#E5E7EB]">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                            <th class="px-6 py-4 font-semibold">Cuaca</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB]">
                        @forelse($recentLaporan as $l)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $l->tanggal->isoFormat('dddd, D MMM Y') }}</td>
                            <td class="px-6 py-4">{{ $l->cuaca_label }}</td>
                            <td class="px-6 py-4">
                                @if($l->status == 'approved')
                                    <span class="inline-block px-3 py-1 bg-green-50 text-green-700 border border-green-200 rounded-sm text-[10px] font-bold uppercase tracking-wider">Approved</span>
                                @elseif($l->status == 'verified')
                                    <span class="inline-block px-3 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-sm text-[10px] font-bold uppercase tracking-wider">Verified</span>
                                @elseif($l->status == 'submitted')
                                    <span class="inline-block px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-sm text-[10px] font-bold uppercase tracking-wider">Submitted</span>
                                @elseif($l->status == 'rejected')
                                    <span class="inline-block px-3 py-1 bg-red-50 text-red-700 border border-red-200 rounded-sm text-[10px] font-bold uppercase tracking-wider">Rejected</span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-gray-50 text-gray-700 border border-gray-200 rounded-sm text-[10px] font-bold uppercase tracking-wider">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('laporan-harian.show', $l) }}" class="text-xs font-bold text-[#1E3A8A] hover:underline">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">Belum ada laporan harian.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
    @else
    <div class="w-full px-4 md:px-8 py-8 font-sans">
        <!-- NO PROYEK -->
        <div class="bg-gray-50 border border-gray-200 rounded-sm p-8 text-center mt-8 shadow-sm">
            <div class="w-16 h-16 bg-gray-200 rounded-sm flex items-center justify-center mx-auto mb-4 text-gray-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Proyek Aktif</h3>
            <p class="text-gray-600 max-w-md mx-auto">Anda belum memiliki proyek aktif yang ditugaskan kepada Anda.</p>
        </div>
    </div>
    @endif

    @if($proyek)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";
            Chart.defaults.color = '#6B7280';
            
            const tooltipOptions = {
                backgroundColor: 'rgba(15, 23, 43, 0.95)',
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 },
                padding: 12,
                cornerRadius: 8,
                usePointStyle: true,
                boxPadding: 6
            };

            // Data for Pie Chart
            const statusPieCtx = document.getElementById('statusPieChart').getContext('2d');
            new Chart(statusPieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Draft', 'Submitted', 'Verified', 'Approved', 'Rejected'],
                    datasets: [{
                        data: [
                            {{ $stats['draft'] ?? 0 }},
                            {{ $stats['submitted'] ?? 0 }},
                            {{ $stats['verified'] ?? 0 }},
                            {{ $stats['approved'] ?? 0 }},
                            {{ $stats['rejected'] ?? 0 }}
                        ],
                        backgroundColor: [
                            '#9CA3AF', // Gray
                            '#F59E0B', // Amber
                            '#3B82F6', // Blue
                            '#10B981', // Emerald
                            '#EF4444'  // Red
                        ],
                        borderWidth: 0,
                        borderRadius: 4,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { 
                            position: 'bottom',
                            labels: { usePointStyle: true, padding: 20, font: { size: 12, weight: '500' } }
                        },
                        tooltip: tooltipOptions
                    }
                }
            });

            // Data for S-Curve
            const sCurveCtx = document.getElementById('sCurveChart').getContext('2d');
            
            // Create Gradient
            const gradient = sCurveCtx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(30, 58, 138, 0.4)');
            gradient.addColorStop(1, 'rgba(30, 58, 138, 0.0)');

            let labelsData = {!! json_encode($sCurveData['labels'] ?? []) !!};
            let rencanaData = {!! json_encode($sCurveData['rencana'] ?? []) !!};
            let realisasiData = {!! json_encode($sCurveData['realisasi'] ?? []) !!};

            if (labelsData.length > 0 && labelsData[0] !== 'Mg 0') {
                labelsData.unshift('Mg 0');
                rencanaData.unshift(0);
                realisasiData.unshift(0);
            }

            new Chart(sCurveCtx, {
                type: 'line',
                data: {
                    labels: labelsData,
                    datasets: [
                        {
                            label: 'Rencana Kumulatif (%)',
                            data: rencanaData,
                            borderColor: '#1E3A8A', // primary
                            backgroundColor: gradient,
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#1E3A8A',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Realisasi Kumulatif (%)',
                            data: realisasiData,
                            borderColor: '#FFB800', // accent
                            backgroundColor: 'transparent',
                            borderWidth: 3,
                            tension: 0.4,
                            borderDash: [5, 5], // dashed line
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#FFB800',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Deviasi (%)',
                            data: rencanaData.map((rencana, i) => {
                                return i < realisasiData.length ? (realisasiData[i] - rencana).toFixed(2) : null;
                            }),
                            borderColor: '#EF4444',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            tension: 0.4,
                            borderDash: [5, 5],
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#EF4444',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: { color: '#F3F4F6', drawBorder: false },
                            ticks: { callback: (value) => value + '%' }
                        },
                        x: {
                            grid: { display: false, drawBorder: false }
                        }
                    },
                    plugins: {
                        legend: { 
                            position: 'top',
                            align: 'end',
                            labels: { usePointStyle: true, padding: 20, font: { size: 12, weight: '500' } }
                        },
                        tooltip: tooltipOptions
                    }
                }
            });
        });
    </script>
    @endif
</x-app-layout>
