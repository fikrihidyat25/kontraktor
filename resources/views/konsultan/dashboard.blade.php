<x-app-layout>
    <x-slot name="title">Dashboard Konsultan</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-8 border-b border-[#E5E7EB] pb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Dashboard Konsultan Pengawas</h1>
            </div>
        </div>

        <!-- STATS & PIE CHART -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Pie Chart -->
            <div class="bg-white border border-[#E5E7EB] rounded-sm shadow-sm p-6">
                <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider mb-4">Status Laporan Harian</h3>
                <div class="relative w-full h-48 flex justify-center">
                    <canvas id="konsultanPieChart"></canvas>
                </div>
            </div>
            
            <!-- Cards -->
            <div class="lg:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['menunggu_verifikasi'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Menunggu Verifikasi</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['sudah_diverifikasi'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Terverifikasi</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['sudah_diapprove'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Disetujui PPK</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['ditolak'] }}</div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Dikembalikan</div>
            </div>
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Dikembalikan</div>
            </div>
            </div>
            </div>
        </div>


        
        <!-- S-CURVE PER PROYEK -->
        @foreach($proyeks as $proyek)
        @php $mingguans = $sCurveData[$proyek->id] ?? collect(); @endphp
        <div class="bg-white rounded-sm shadow-sm border border-[#E5E7EB] mb-8 overflow-hidden">
            <!-- Header Card Proyek -->
            <div class="bg-[#1E3A8A] p-6 relative border-b border-[#1E3A8A]">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-white mb-2">{{ $proyek->nama_proyek }}</h2>
                        <div class="text-sm text-white/80 flex flex-wrap gap-4">
                            <span class="flex items-center"><svg class="w-4 h-4 mr-1 text-[#FFB800]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>{{ $proyek->lokasi }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6">
                @php
                    $latestMingguan = collect($mingguans)->last();
                    $progressRealisasi = $latestMingguan ? $latestMingguan['bobot_realisasi'] : 0;
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
                    <canvas id="sCurveChart_{{ $proyek->id }}"></canvas>
                </div>
            </div>
        </div>
        @endforeach

        <!-- PENDING LAPORAN ALERT -->
        @if($pendingLaporan->count())
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-sm shadow-sm text-sm font-medium flex items-center text-amber-800">
            <svg class="w-5 h-5 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>Ada <strong class="font-bold text-amber-700 text-base mx-1">{{ $stats['menunggu_verifikasi'] }}</strong> laporan menunggu proses audit & verifikasi Anda.</div>
        </div>
        @endif

        <!-- TABEL MENUNGGU VERIFIKASI -->
        <div class="bg-white rounded-sm shadow-sm border border-[#E5E7EB] overflow-hidden mb-4">
            <div class="bg-gray-50 border-b border-[#E5E7EB] px-6 py-4 flex justify-between items-center">
                <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">Laporan Menunggu Verifikasi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="text-[11px] text-gray-500 uppercase bg-white border-b border-[#E5E7EB]">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                            <th class="px-6 py-4 font-semibold">Proyek</th>
                            <th class="px-6 py-4 font-semibold">Kontraktor</th>
                            <th class="px-6 py-4 font-semibold">Cuaca</th>
                            <th class="px-6 py-4 font-semibold text-center w-48">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB]">
                        @forelse($pendingLaporan as $l)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">{{ $l->tanggal->isoFormat('dddd, D MMM Y') }}</td>
                            <td class="px-6 py-4 text-gray-900 font-medium">{{ $l->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4">{{ $l->kontraktor->name }}</td>
                            <td class="px-6 py-4">{{ $l->cuaca_label }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('laporan-harian.show', $l) }}" class="inline-flex items-center justify-center bg-[#1E3A8A] text-[#FFB800] text-xs font-bold px-4 py-2 rounded-sm border border-[#1E3A8A] hover:bg-[#152e70] transition-colors whitespace-nowrap">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Audit & Verifikasi
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center bg-white">
                                <div class="w-16 h-16 bg-gray-100 rounded-sm flex items-center justify-center mx-auto mb-4 text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div class="text-gray-500 italic text-base">Hore! Semua laporan sudah diverifikasi.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($pendingLaporan->count())
        <div class="text-right">
            <a href="{{ route('laporan-harian.index') }}" class="text-sm font-bold text-[#1E3A8A] hover:underline transition-colors">
                Lihat semua laporan &rarr;
            </a>
        </div>
        @endif
    </div>
    
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

            // PIE CHART
            const pieCtx = document.getElementById('konsultanPieChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Menunggu Verifikasi', 'Terverifikasi', 'Disetujui', 'Ditolak'],
                    datasets: [{
                        data: [
                            {{ $stats['menunggu_verifikasi'] ?? 0 }}, 
                            {{ $stats['sudah_diverifikasi'] ?? 0 }}, 
                            {{ $stats['sudah_diapprove'] ?? 0 }},
                            {{ $stats['ditolak'] ?? 0 }}
                        ],
                        backgroundColor: ['#F59E0B', '#3B82F6', '#10B981', '#EF4444'],
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

            // S-CURVES
            const sCurveData = @json($sCurveData);
            
            for (const projectId in sCurveData) {
                const data = sCurveData[projectId];
                let labels = data.map(item => 'Mg ' + item.minggu_ke);
                let rencana = data.map(item => item.bobot_rencana);
                let realisasi = data.map(item => item.bobot_realisasi);

                if (labels.length > 0 && labels[0] !== 'Mg 0') {
                    labels.unshift('Mg 0');
                    rencana.unshift(0);
                    realisasi.unshift(0);
                }

                const canvas = document.getElementById('sCurveChart_' + projectId);
                if (canvas) {
                    const sCurveCtx = canvas.getContext('2d');
                    
                    const gradient = sCurveCtx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(30, 58, 138, 0.4)');
                    gradient.addColorStop(1, 'rgba(30, 58, 138, 0.0)');

                    new Chart(sCurveCtx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Rencana Kumulatif (%)',
                                    data: rencana,
                                    borderColor: '#1E3A8A',
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
                                    data: realisasi,
                                    borderColor: '#FFB800',
                                    backgroundColor: 'transparent',
                                    borderWidth: 3,
                                    tension: 0.4,
                                    borderDash: [5, 5],
                                    pointBackgroundColor: '#ffffff',
                                    pointBorderColor: '#FFB800',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6
                                },
                                {
                                    label: 'Deviasi (%)',
                                    data: rencana.map((renc, i) => {
                                        return i < realisasi.length ? (realisasi[i] - renc).toFixed(2) : null;
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
                            interaction: { mode: 'index', intersect: false },
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
                                    position: 'top', align: 'end',
                                    labels: { usePointStyle: true, padding: 20, font: { size: 12, weight: '500' } }
                                },
                                tooltip: tooltipOptions
                            }
                        }
                    });
                }
            }
        });
    </script>
</x-app-layout>
