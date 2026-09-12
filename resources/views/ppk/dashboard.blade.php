<x-app-layout>
    <x-slot name="title">Dashboard PPK</x-slot>

    <div class="w-full px-4 md:px-8 py-8 font-sans">
        <!-- HEADER -->
        <div class="flex justify-between items-center mb-8 border-b border-[#E5E7EB] pb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0F172B]">Dashboard Monitoring Proyek</h1>
            </div>
        </div>

        <!-- MASTER DATA STATS -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-[#1E3A8A] rounded-lg p-5 text-white shadow-md">
                <div class="text-sm font-medium text-white/80 uppercase tracking-wider mb-1">Total Proyek</div>
                <div class="text-3xl font-black">{{ $stats['total_proyek'] }}</div>
            </div>
            <div class="bg-white border border-[#E5E7EB] rounded-lg p-5 shadow-sm">
                <div class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Proyek Aktif</div>
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['proyek_aktif'] }}</div>
            </div>
            <div class="bg-white border border-[#E5E7EB] rounded-lg p-5 shadow-sm">
                <div class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Total User</div>
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['total_user'] }}</div>
            </div>
            <div class="bg-white border border-[#E5E7EB] rounded-lg p-5 shadow-sm">
                <div class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Kontraktor</div>
                <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['total_kontraktor'] }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Pie Chart -->
            <div class="bg-white border border-[#E5E7EB] rounded-sm shadow-sm p-6">
                <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider mb-4">Status Laporan Harian</h3>
                <div class="relative w-full h-48 flex justify-center">
                    <canvas id="ppkPieChart"></canvas>
                </div>
            </div>
            
            <!-- Cards -->
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                    <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['menunggu_approval'] }}</div>
                    <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Menunggu Final Approval PPTK</div>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                    <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['sudah_diapprove'] }}</div>
                    <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Sudah Disetujui (Approved)</div>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5 text-center transition-colors">
                    <div class="text-3xl font-black text-[#1E3A8A]">{{ $stats['menunggu_approval'] + $stats['sudah_diapprove'] }}</div>
                    <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mt-2">Total Laporan Masuk</div>
                </div>
            </div>
        </div>



        <!-- S-CURVE PER PROYEK -->
        @foreach($proyeks as $proyek)
        @php 
            $mingguans = $sCurveData[$proyek->id]; 
            
            $tMulai = \Carbon\Carbon::parse($proyek->tanggal_mulai)->startOfDay();
            $tSelesai = \Carbon\Carbon::parse($proyek->tanggal_selesai)->startOfDay();
            $totalHari = intval($tMulai->diffInDays($tSelesai)) ?: 1;
            $hariBerjalan = intval($tMulai->diffInDays(now()->startOfDay(), false));
            if ($hariBerjalan < 0) $hariBerjalan = 0;
            if ($hariBerjalan > $totalHari) $hariBerjalan = $totalHari;
        @endphp
        <div class="bg-white rounded-sm shadow-sm border border-[#E5E7EB] mb-8 overflow-hidden">
            <!-- Header Card Proyek -->
            <div class="bg-[#1E3A8A] p-6 relative border-b border-[#1E3A8A]">
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-white mb-2">{{ $proyek->nama_proyek }}</h2>
                        <div class="text-sm text-white/80 flex flex-wrap gap-4 mb-3">
                            <span class="flex items-center"><svg class="w-4 h-4 mr-1 text-[#FFB800]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>{{ $proyek->lokasi }}</span>
                            <span class="flex items-center"><svg class="w-4 h-4 mr-1 text-[#FFB800]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>{{ $proyek->tanggal_mulai->format('d M Y') }} – {{ $proyek->tanggal_selesai->format('d M Y') }}</span>
                            <span class="flex items-center font-bold text-[#FFB800]">Rp {{ number_format($proyek->nilai_kontrak, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="inline-flex items-center bg-white/20 px-3 py-1 rounded-full text-xs font-semibold text-white shadow-sm">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $hariBerjalan }} hari berlalu dari total {{ $totalHari }} hari
                            </span>
                        </div>
                    </div>
                    <div>
                        @php
                            $bgBadge = 'bg-gray-600';
                            $textBadge = ucfirst($proyek->status);
                            if($proyek->status === 'aktif') { 
                                if($hariBerjalan >= $totalHari) {
                                    $bgBadge = 'bg-blue-600'; 
                                    $textBadge = 'Selesai'; 
                                } else {
                                    $bgBadge = 'bg-[#15803D]'; 
                                    $textBadge = 'Aktif'; 
                                }
                            }
                            elseif($proyek->status === 'selesai') { $bgBadge = 'bg-blue-600'; $textBadge = 'Selesai'; }
                            elseif($proyek->status === 'menunggu_verifikasi') { $bgBadge = 'bg-yellow-500'; $textBadge = 'Menunggu Verifikasi'; }
                        @endphp
                        <span class="inline-block px-4 py-1.5 {{ $bgBadge }} text-white text-[10px] font-bold uppercase tracking-wider rounded-sm shadow-sm mb-2">
                            {{ $textBadge }}
                        </span>
                        
                        <!-- Actions -->
                        <div class="mt-2 flex flex-col md:flex-row gap-2 items-center justify-end">
                            <a href="{{ route('ppk.proyek.edit-tim', $proyek) }}" class="bg-white text-[#1E3A8A] text-xs font-bold px-3 py-1.5 rounded hover:bg-gray-100 transition-colors inline-flex items-center whitespace-nowrap shadow-sm">
                                @if(!$proyek->kontraktor_id || !$proyek->konsultan_id) Pilih Tim Proyek @else Edit Tim @endif
                            </a>
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

        <!-- LAPORAN HARIAN TERBARU -->
        @if($laporanTerbaru->count())
        <div class="bg-white rounded-sm shadow-sm border border-[#E5E7EB] overflow-hidden mb-8">
            <div class="bg-gray-50 border-b border-[#E5E7EB] px-6 py-4 flex justify-between items-center">
                <h3 class="text-sm font-bold text-[#0F172B] uppercase tracking-wider">Laporan Harian Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="text-[11px] text-gray-500 uppercase bg-white border-b border-[#E5E7EB]">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Tanggal</th>
                            <th class="px-6 py-4 font-semibold">Proyek</th>
                            <th class="px-6 py-4 font-semibold">Kontraktor</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold text-center w-48">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E5E7EB]">
                        @foreach($laporanTerbaru as $l)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 whitespace-nowrap">{{ $l->tanggal->isoFormat('dddd, D MMM Y') }}</td>
                            <td class="px-6 py-4 text-gray-900 font-medium">{{ $l->proyek->nama_proyek }}</td>
                            <td class="px-6 py-4">{{ $l->kontraktor->name }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusMap = [
                                        'draft'    => ['label' => 'Draft',     'class' => 'bg-gray-100 text-gray-700 border-gray-300'],
                                        'submitted'=> ['label' => 'Submitted', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
                                        'verified' => ['label' => 'Verified',  'class' => 'bg-amber-50 text-amber-700 border-amber-300'],
                                        'approved' => ['label' => 'Approved',  'class' => 'bg-green-50 text-green-700 border-green-200'],
                                        'rejected' => ['label' => 'Rejected',  'class' => 'bg-red-50 text-red-700 border-red-200'],
                                    ];
                                    $s = $statusMap[$l->status] ?? ['label' => ucfirst($l->status), 'class' => 'bg-gray-100 text-gray-700'];
                                @endphp
                                <span class="inline-block px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded border {{ $s['class'] }}">{{ $s['label'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('laporan-harian.show', $l) }}" class="inline-flex items-center justify-center bg-[#1E3A8A] text-[#FFB800] text-xs font-bold px-4 py-2 rounded-sm border border-[#1E3A8A] hover:bg-[#152e70] transition-colors whitespace-nowrap">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
            const pieCtx = document.getElementById('ppkPieChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Menunggu Approval', 'Approved', 'Total Lainnya'],
                    datasets: [{
                        data: [
                            {{ $stats['menunggu_approval'] ?? 0 }}, 
                            {{ $stats['sudah_diapprove'] ?? 0 }}, 
                            {{ max(0, ($stats['total_laporan'] ?? 0) - ($stats['menunggu_approval'] ?? 0) - ($stats['sudah_diapprove'] ?? 0)) }}
                        ],
                        backgroundColor: ['#F59E0B', '#10B981', '#E5E7EB'],
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
