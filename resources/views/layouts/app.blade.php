<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — DIGITAPRODA</title>
    <meta name="description" content="Sistem Informasi dan Monitoring Proyek Konstruksi - DIGITAPRODA">

    <!-- Inter Font (Replacing Poppins with Inter for Formal-Modern UI if desired, but Landing uses Poppins. I will stick to Poppins to match landing page perfectly) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind + Alpine via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #1E3A8A;
            --accent: #FFB800;
            --bg: #FFFFFF;
            --border: #E5E7EB;
            --text-muted: #64748B;
        }
        * { font-family: 'Poppins', sans-serif; }
        body { background: var(--bg); color: #0F172B; }
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="antialiased flex flex-col min-h-screen bg-white" x-data="{ mobileMenuOpen: false, profileDropdown: false, pelaporanDropdown: false, masterDropdown: false }">

    <!-- TOP NAVBAR -->
    <nav class="bg-[#1E3A8A] text-white shadow-sm border-b border-[#1e3a8a] fixed w-full z-50 h-16 md:h-20 flex items-center justify-between px-4 md:px-8 transition-all duration-300">
        <!-- Kiri: Logo -->
        <div class="flex items-center gap-6">
            <a href="{{ route('dashboard') }}" class="text-[#FFB800] text-xl font-bold tracking-tight flex items-center gap-2 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                DIGITAPRODA
            </a>

            <!-- Desktop Links -->
            <div class="hidden lg:flex items-center gap-1">
                <a href="{{ route('dashboard') }}" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('*.dashboard') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                    Dashboard
                </a>

                @if(Auth::user()->isKontraktor() || Auth::user()->isKonsultan() || Auth::user()->isPPK() || Auth::user()->isPPTK())
                    @php
                        $menuKontrakCount = 0;
                        if(Auth::check() && Auth::user()->isKontraktor()) {
                            $menuKontrakCount = \App\Models\DokumenProyek::whereHas('proyek', function($q) {
                                $q->where('kontraktor_id', Auth::id());
                            })->where('status', 'menunggu_validasi')->count();
                        }
                    @endphp
                    <a href="{{ route('dokumen.index') }}" 
                       class="relative px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('dokumen.*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        Menu Kontrak
                        @if($menuKontrakCount > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">{{ $menuKontrakCount }}</span>
                        @endif
                    </a>

                    <!-- Uang Muka -->
                    @if(Auth::user()->isKontraktor() || Auth::user()->isPPTK() || Auth::user()->isPPK())
                    <a href="{{ route('uang-muka.index') }}" 
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('uang-muka.*') && !request()->routeIs('uang-muka-konsultan.*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        Uang Muka
                    </a>
                    @elseif(Auth::user()->isKonsultan())
                    <a href="{{ route('uang-muka-konsultan.index') }}" 
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('uang-muka-konsultan.*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        Uang Muka
                    </a>
                    @endif

                    <!-- Dropdown Pelaporan -->
                    @php
                        $pendingLh = 0;
                        $pendingLm = 0;
                        $pendingLb = 0;
                        $unreadLh = collect();
                        if(Auth::check()) {
                            $u = Auth::user();
                            if($u->isKonsultan()) {
                                $pendingLh = \App\Models\LaporanHarian::whereHas('proyek', function($q) use($u) { $q->where('konsultan_id', $u->id); })->where('status', 'submitted')->count();
                                $pendingLm = \App\Models\LaporanMingguan::whereHas('proyek', function($q) use($u) { $q->where('konsultan_id', $u->id); })->where('status', 'submitted')->count();
                                $pendingLb = \App\Models\LaporanBulanan::whereHas('proyek', function($q) use($u) { $q->where('konsultan_id', $u->id); })->where('status', 'submitted')->count();
                                
                                $unreadLh = \App\Models\LaporanHarian::whereHas('proyek', function($q) use($u) { $q->where('konsultan_id', $u->id); })
                                            ->where('is_read_konsultan', false)->latest('updated_at')->get();
                            } elseif($u->isPPTK()) {
                                $pendingLh = \App\Models\LaporanHarian::whereHas('proyek', function($q) use($u) { $q->where('pptk_id', $u->id); })->where('status', 'verified')->count();
                                $pendingLm = \App\Models\LaporanMingguan::whereHas('proyek', function($q) use($u) { $q->where('pptk_id', $u->id); })->where('status', 'verified')->count();
                                $pendingLb = \App\Models\LaporanBulanan::whereHas('proyek', function($q) use($u) { $q->where('pptk_id', $u->id); })->where('status', 'verified')->count();
                                
                                $unreadLh = \App\Models\LaporanHarian::whereHas('proyek', function($q) use($u) { $q->where('pptk_id', $u->id); })
                                            ->where('is_read_pptk', false)->latest('updated_at')->get();
                            } elseif($u->isPPK()) {
                                // PPK does not get notifications for Laporan Harian (only monitoring)
                                $pendingLh = 0;
                                $pendingLm = 0;
                                $pendingLb = 0;
                            } elseif($u->isKontraktor()) {
                                $unreadLh = \App\Models\LaporanHarian::where('kontraktor_id', $u->id)
                                            ->where('is_read_kontraktor', false)->latest('updated_at')->get();
                            }
                        }
                        $pendingLaporanCount = $pendingLh + $pendingLm + $pendingLb;
                    @endphp
                    @if(Auth::user()->isKontraktor() || Auth::user()->isPPTK() || Auth::user()->isKonsultan() || Auth::user()->isPPK())
                    <div class="relative" @click.away="pelaporanDropdown = false">
                        <button @click="pelaporanDropdown = !pelaporanDropdown" 
                                class="flex items-center gap-1 px-4 py-2 rounded-md text-sm font-medium transition-colors relative {{ request()->is('laporan-*') || request()->is('permintaan-*') || request()->is('shop-drawing*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                            Pelaporan Proyek
                            @if($pendingLaporanCount > 0)
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">{{ $pendingLaporanCount }}</span>
                            @endif
                            <svg class="w-4 h-4 transition-transform duration-200" :class="pelaporanDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="pelaporanDropdown" x-transition x-cloak class="absolute left-0 mt-2 w-56 bg-white border border-[#E5E7EB] rounded-sm shadow-lg py-1 z-50">
                            @if(Auth::user()->isKonsultan() || Auth::user()->isPPK() || Auth::user()->isPPTK())
                                <a href="{{ route('laporan-pengawas.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] border-b {{ request()->routeIs('laporan-pengawas.*') ? 'font-bold text-[#1E3A8A]' : '' }}">Laporan Pengawas</a>
                            @endif
                            
                            @if(Auth::user()->isKonsultan())
                                <div class="px-4 py-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-1">Verifikasi Pengawas</div>
                            @endif
                            <a href="{{ route('laporan-harian.index') }}" class="flex justify-between items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('laporan-harian.*') ? 'font-bold text-[#1E3A8A]' : '' }}">
                                <span>Laporan Harian</span>
                                @if($pendingLh > 0)
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full">{{ $pendingLh }}</span>
                                @endif
                            </a>
                            <a href="{{ route('laporan-mingguan.index') }}" class="flex justify-between items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('laporan-mingguan.*') ? 'font-bold text-[#1E3A8A]' : '' }}">
                                <span>Laporan Mingguan</span>
                                @if($pendingLm > 0)
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full">{{ $pendingLm }}</span>
                                @endif
                            </a>
                            <a href="{{ route('laporan-bulanan.index') }}" class="flex justify-between items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('laporan-bulanan.*') ? 'font-bold text-[#1E3A8A]' : '' }}">
                                <span>Laporan Bulanan</span>
                                @if($pendingLb > 0)
                                    <span class="inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full">{{ $pendingLb }}</span>
                                @endif
                            </a>
                            <a href="{{ route('shop-drawing.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('shop-drawing.*') ? 'font-bold text-[#1E3A8A]' : '' }}">Shop Drawing</a>
                            <a href="{{ route('laporan-lainnya.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('laporan-lainnya.*') ? 'font-bold text-[#1E3A8A]' : '' }}">Laporan Lainnya</a>
                        </div>
                    </div>
                    @endif

                    @if(Auth::user()->isKontraktor() || Auth::user()->isPPK() || Auth::user()->isPPTK() || Auth::user()->isKonsultan())
                    <!-- KTK -->
                    <a href="{{ route('kerja-tambah-kurang.index') }}" 
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('kerja-tambah-kurang.*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        PTK
                    </a>
                    @endif

                    <!-- Pembayaran -->
                    @if(Auth::user()->isKontraktor())
                        <a href="{{ route('permintaan-pembayaran.index') }}" 
                           class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('permintaan-pembayaran.*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                            Pembayaran
                        </a>
                    @elseif(Auth::user()->isKonsultan())
                        <a href="{{ route('pembayaran-konsultan.index') }}" 
                           class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('pembayaran-konsultan.*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                            Pembayaran
                        </a>
                    @elseif(Auth::user()->isPPK() || Auth::user()->isPPTK())
                        <div class="relative" @click.away="pembayaranDropdown = false" x-data="{ pembayaranDropdown: false }">
                            <button @click="pembayaranDropdown = !pembayaranDropdown" 
                                    class="flex items-center gap-1 px-4 py-2 rounded-md text-sm font-medium transition-colors relative {{ request()->is('permintaan-pembayaran*') || request()->is('pembayaran-konsultan*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                                Pembayaran
                                <svg class="w-4 h-4 transition-transform duration-200" :class="pembayaranDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="pembayaranDropdown" x-transition x-cloak class="absolute left-0 mt-2 w-52 bg-white border border-[#E5E7EB] rounded-sm shadow-lg py-1 z-50">
                                <a href="{{ route('permintaan-pembayaran.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('permintaan-pembayaran.*') ? 'font-bold text-[#1E3A8A]' : '' }}">Pembayaran Kontraktor</a>
                                <a href="{{ route('pembayaran-konsultan.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('pembayaran-konsultan.*') ? 'font-bold text-[#1E3A8A]' : '' }}">Pembayaran Konsultan</a>
                            </div>
                        </div>
                    @endif

                    <!-- Berita Acara -->
                    @if(Auth::user()->isKontraktor() || Auth::user()->isPPK() || Auth::user()->isPPTK() || Auth::user()->isKonsultan())
                    <a href="{{ route('berita-acara-konsultan.index') }}" 
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('berita-acara-konsultan.*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        Berita Acara
                    </a>
                    @endif

                    <!-- Serah Terima -->
                    @if(Auth::user()->isPPK() || Auth::user()->isPPTK() || Auth::user()->isKontraktor() || Auth::user()->isKonsultan())
                    <a href="{{ route('serah-terima.index') }}" 
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('serah-terima.*') && !request()->routeIs('serah-terima-konsultan.*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        Serah Terima
                    </a>
                    @endif



                @if(Auth::user()->isPPK())
                    <!-- Dropdown Master Data -->
                    <div class="relative" @click.away="masterDropdown = false">
                        <button @click="masterDropdown = !masterDropdown" 
                                class="flex items-center gap-1 px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('ppk.users.*') || request()->routeIs('ppk.proyeks.*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                            Master Data
                            <svg class="w-4 h-4 transition-transform duration-200" :class="masterDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="masterDropdown" x-transition x-cloak class="absolute left-0 mt-2 w-48 bg-white border border-[#E5E7EB] rounded-sm shadow-lg py-1 z-50">
                            <a href="{{ route('ppk.users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('ppk.users.*') ? 'font-bold text-[#1E3A8A]' : '' }}">Manajemen User</a>
                            <a href="{{ route('ppk.proyeks.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('ppk.proyeks.*') ? 'font-bold text-[#1E3A8A]' : '' }}">Master Proyek</a>
                        </div>
                    </div>
                @endif

                @if(Auth::user()->isKontraktor() || Auth::user()->isKonsultan())
                <a href="{{ route('tentang.index') }}" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('tentang.*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                    Tentang
                </a>
                @endif
                @endif
            </div>
        </div>

        <!-- Kanan: Profile & Mobile Toggle -->
        <div class="flex items-center gap-4">

            <!-- Bell Notification -->
            @php
                $unreadNotifications = collect();
                $notifCount = 0;
                if(Auth::check()) {
                    $unreadNotifications = Auth::user()->unreadNotifications;
                    $notifCount = $unreadNotifications->count();
                }
            @endphp
            @if(Auth::check())
            <div class="relative" x-data="{ notifDropdown: false }" @click.away="notifDropdown = false">
                <button @click="notifDropdown = !notifDropdown" class="relative p-2 text-gray-300 hover:text-white transition-colors focus:outline-none rounded-full hover:bg-white/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    @if($notifCount > 0)
                    <span id="bellBadge" class="absolute top-1 right-1 flex items-center justify-center min-w-[18px] h-[18px] text-[10px] font-bold text-white bg-red-500 rounded-full border-2 border-[#1E3A8A]">
                        {{ $notifCount }}
                    </span>
                    @endif
                </button>
                <div x-show="notifDropdown" x-transition x-cloak class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-xl overflow-hidden z-50">
                    <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h3 class="text-sm font-bold text-gray-800">Notifikasi</h3>
                        @if($notifCount > 0)
                        <span id="notifHeaderCount" class="text-xs text-white bg-red-500 px-2 py-0.5 rounded-full font-semibold">{{ $notifCount }} Baru</span>
                        @endif
                    </div>
                    
                    <div class="max-h-80 overflow-y-auto" id="notifList">
                        @forelse($unreadNotifications as $notif)
                        <a href="{{ route('notifikasi.read', $notif->id) }}" class="notif-item block px-4 py-3 border-b border-gray-50 hover:bg-blue-50 transition-colors relative">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900">
                                        <span class="font-bold">{{ $notif->data['title'] ?? 'Notifikasi' }}</span><br>
                                        {{ $notif->data['message'] ?? '' }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <p class="text-[11px] text-gray-400">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0 ml-1 shadow-[0_0_4px_rgba(59,130,246,0.5)]"></div>
                            </div>
                        </a>
                        @empty
                        <div class="px-4 py-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p class="text-sm">Tidak ada notifikasi baru.</p>
                        </div>
                        @endforelse
                        
                        @if($notifCount > 0)
                        <div class="p-2 border-t border-gray-100 bg-gray-50 text-center">
                            <form action="{{ route('notifikasi.read-all') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs font-semibold text-[#1E3A8A] hover:underline">Tandai semua dibaca</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            
            
              <!-- Desktop Profile Dropdown -->
            <div class="relative hidden lg:block" @click.away="profileDropdown = false">
                <button @click="profileDropdown = !profileDropdown" class="flex items-center gap-3 focus:outline-none bg-white/5 hover:bg-white/10 rounded-md py-1.5 px-3 transition-colors">
                    <div class="text-right hidden xl:block">
                        <div class="text-[13px] font-bold text-white leading-tight">{{ Auth::user()->name }}</div>
                        <div class="text-[11px] font-medium text-[#FFB800] uppercase tracking-wider">{{ Auth::user()->role_label }}</div>
                    </div>
                    <div class="h-8 w-8 rounded-full bg-[#FFB800] flex items-center justify-center text-[#1E3A8A] font-bold text-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <div x-show="profileDropdown" x-transition x-cloak class="absolute right-0 mt-2 w-48 bg-white border border-[#E5E7EB] rounded-sm shadow-lg py-1 z-50">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A]">Pengaturan Akun</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm font-bold text-red-600 hover:bg-red-50 transition-colors">
                            Keluar Sistem
                        </button>
                    </form>
                </div>
            </div>

            <!-- Mobile Menu Toggle -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-[#FFB800] focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display: none;"></path>
                </svg>
            </button>
        </div>
    </nav>

    <!-- MOBILE NAVIGATION MENU -->
    <div x-show="mobileMenuOpen" x-transition class="lg:hidden bg-[#1E3A8A]/95 backdrop-blur-md fixed w-full top-16 md:top-20 z-40 border-t border-white/10 shadow-xl overflow-y-auto max-h-[calc(100vh-64px)]">
        <div class="px-4 py-4 space-y-1">
            <div class="flex items-center gap-3 p-3 border-b border-white/10 mb-2">
                <div class="h-10 w-10 rounded-full bg-[#FFB800] flex items-center justify-center text-[#1E3A8A] font-bold text-lg">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <div class="text-sm font-bold text-white">{{ Auth::user()->name }}</div>
                    <div class="text-xs font-medium text-[#FFB800] uppercase">{{ Auth::user()->role_label }}</div>
                </div>
            </div>

            <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('*.dashboard') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Dashboard</a>

            @if(Auth::user()->isKontraktor() || Auth::user()->isKonsultan() || Auth::user()->isPPK() || Auth::user()->isPPTK())
                <a href="{{ route('dokumen.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('dokumen.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Menu Kontrak</a>
                
                @if(Auth::user()->isKontraktor() || Auth::user()->isPPTK() || Auth::user()->isPPK())
                <a href="{{ route('uang-muka.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('uang-muka.*') && !request()->routeIs('uang-muka-konsultan.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Uang Muka</a>
                @elseif(Auth::user()->isKonsultan())
                <a href="{{ route('uang-muka-konsultan.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('uang-muka-konsultan.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Uang Muka</a>
                @endif

                <div class="pt-2 mt-2 border-t border-white/10">
                    <div class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                        Pelaporan Proyek
                    </div>
                    @if(Auth::user()->isKonsultan() || Auth::user()->isPPK() || Auth::user()->isPPTK())
                    <a href="{{ route('laporan-pengawas.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('laporan-pengawas.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md border-b border-white/10 mb-1">Laporan Pengawas</a>
                    @endif
                    
                    @if(Auth::user()->isKonsultan())
                    <div class="px-3 py-1 text-[10px] font-bold text-gray-500 uppercase tracking-wider mt-1">Verifikasi Pengawas</div>
                    @endif
                    <a href="{{ route('laporan-harian.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('laporan-harian.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Laporan Harian</a>
                    <a href="{{ route('laporan-mingguan.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('laporan-mingguan.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Laporan Mingguan</a>
                    <a href="{{ route('laporan-bulanan.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('laporan-bulanan.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Laporan Bulanan</a>
                    <a href="{{ route('shop-drawing.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('shop-drawing.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Shop Drawing</a>
                    <a href="{{ route('laporan-lainnya.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('laporan-lainnya.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Laporan Lainnya</a>
                </div>

                <div class="pt-2 mt-2 border-t border-white/10">
                    <a href="{{ route('kerja-tambah-kurang.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('kerja-tambah-kurang.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">PTK</a>
                    
                    @if(Auth::user()->isKontraktor() || Auth::user()->isPPK() || Auth::user()->isPPTK())
                    <a href="{{ route('permintaan-pembayaran.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('permintaan-pembayaran.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Pembayaran{{ Auth::user()->isKontraktor() ? '' : ' Kontraktor' }}</a>
                    @endif
                    @if(Auth::user()->isKonsultan() || Auth::user()->isPPK() || Auth::user()->isPPTK())
                    <a href="{{ route('pembayaran-konsultan.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('pembayaran-konsultan.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Pembayaran{{ Auth::user()->isKonsultan() ? '' : ' Konsultan' }}</a>
                    @endif

                    @if(Auth::user()->isKontraktor() || Auth::user()->isKonsultan() || Auth::user()->isPPK() || Auth::user()->isPPTK())
                    <a href="{{ route('berita-acara-konsultan.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('berita-acara-konsultan.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Berita Acara</a>
                    @endif

                    @if(Auth::user()->isPPK() || Auth::user()->isPPTK() || Auth::user()->isKontraktor() || Auth::user()->isKonsultan())
                    <a href="{{ route('serah-terima.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('serah-terima.*') && !request()->routeIs('serah-terima-konsultan.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Serah Terima</a>
                    @endif
                </div>
            @endif





            @if(Auth::user()->isPPK())
                <div class="pt-2 mt-2 border-t border-white/10">
                    <div class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Master Data</div>
                    <a href="{{ route('ppk.users.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('ppk.users.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Manajemen User</a>
                    <a href="{{ route('ppk.proyeks.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('ppk.proyeks.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Master Proyek</a>
                </div>
            @endif

            @if(Auth::user()->isKontraktor() || Auth::user()->isKonsultan())
                <a href="{{ route('tentang.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('tentang.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Tentang</a>
            @endif

            <div class="pt-4 mt-2 border-t border-white/10">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-gray-300 rounded-md">Pengaturan Akun</a>
                <a href="{{ url('/') }}" class="block px-3 py-2 text-sm text-gray-300 rounded-md">Kembali ke Landing Page</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm font-bold text-red-400 hover:bg-red-500/10 rounded-md">Keluar Sistem</button>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 mt-16 md:mt-20 relative w-full overflow-hidden">
        {{ $slot }}
    </main>

    <!-- GLOBAL TOAST NOTIFICATION -->
    @if(session('success'))
    <div x-data="{ show: true }" 
         x-init="setTimeout(() => show = false, 4000)" 
         x-show="show" 
         x-transition.opacity.duration.500ms
         class="fixed top-24 right-8 z-[100] bg-green-500 text-white px-6 py-3 rounded shadow-lg flex items-center gap-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div x-data="{ show: true }" 
         x-init="setTimeout(() => show = false, 4000)" 
         x-show="show" 
         x-transition.opacity.duration.500ms
         class="fixed top-24 right-8 z-[100] bg-red-500 text-white px-6 py-3 rounded shadow-lg flex items-center gap-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

    <script>
    function markRead(id, event, element) {
        // Only prevent default if we are purely making an AJAX call and NOT following the link.
        // Wait, the user WANTS to follow the link to the detail page!
        // "jika sudah di lihat maka akan hilang langsung"
        // Let's fire the AJAX call in the background, and allow the browser to follow the link.
        
        // Hide the item locally for instant feedback
        element.style.display = 'none';

        // Update counts
        let badge = document.getElementById('bellBadge');
        let headerCount = document.getElementById('notifHeaderCount');
        
        if (badge) {
            let count = parseInt(badge.innerText) - 1;
            if (count <= 0) {
                badge.style.display = 'none';
                if(headerCount) headerCount.style.display = 'none';
                let emptyFallback = document.getElementById('emptyNotifFallback');
                if(emptyFallback) emptyFallback.classList.remove('hidden');
            } else {
                badge.innerText = count;
                if(headerCount) headerCount.innerText = count + ' Baru';
            }
        }

        // Send AJAX to mark as read
        fetch(`/laporan-harian/${id}/mark-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        }).catch(err => console.error(err));
    }
    </script>
</body>
</html>
