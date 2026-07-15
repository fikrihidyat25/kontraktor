<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — DIPRODA</title>
    <meta name="description" content="Sistem Informasi dan Monitoring Proyek Konstruksi - DIPRODA">

    <!-- Inter Font (Replacing Poppins with Inter for Formal-Modern UI if desired, but Landing uses Poppins. I will stick to Poppins to match landing page perfectly) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind + Alpine via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
                DIPRODA
            </a>

            <!-- Desktop Links -->
            <div class="hidden lg:flex items-center gap-1">
                <a href="{{ route('dashboard') }}" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('*.dashboard') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                    Dashboard
                </a>
                
                <a href="{{ route('uang-muka.index') }}" 
                   class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('uang-muka.*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                    Uang Muka
                </a>

                @if(Auth::user()->isKontraktor() || Auth::user()->isKonsultan() || Auth::user()->isPPK())
                    <a href="{{ route('kerja-tambah-kurang.index') }}" 
                       class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('kerja-tambah-kurang.*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                        Kerja Tambah Kurang
                    </a>

                    <!-- Dropdown Pelaporan -->
                    <div class="relative" @click.away="pelaporanDropdown = false">
                        <button @click="pelaporanDropdown = !pelaporanDropdown" 
                                class="flex items-center gap-1 px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->is('laporan-*') || request()->is('permintaan-*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                            Pelaporan Proyek
                            <svg class="w-4 h-4 transition-transform duration-200" :class="pelaporanDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="pelaporanDropdown" x-transition x-cloak class="absolute left-0 mt-2 w-56 bg-white border border-[#E5E7EB] rounded-sm shadow-lg py-1 z-50">
                            <a href="{{ route('laporan-harian.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('laporan-harian.*') ? 'font-bold text-[#1E3A8A]' : '' }}">Laporan Harian</a>
                            <a href="{{ route('laporan-mingguan.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('laporan-mingguan.*') ? 'font-bold text-[#1E3A8A]' : '' }}">Laporan Mingguan</a>
                            <a href="{{ route('laporan-bulanan.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('laporan-bulanan.*') ? 'font-bold text-[#1E3A8A]' : '' }}">Laporan Bulanan</a>
                            <a href="{{ route('permintaan-pembayaran.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('permintaan-pembayaran.*') ? 'font-bold text-[#1E3A8A]' : '' }}">Termin & Tagihan</a>
                        </div>
                    </div>
                @endif

                @if(Auth::user()->isSuperAdmin())
                    <!-- Dropdown Master Data -->
                    <div class="relative" @click.away="masterDropdown = false">
                        <button @click="masterDropdown = !masterDropdown" 
                                class="flex items-center gap-1 px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request()->is('admin/*') ? 'bg-white/10 text-[#FFB800]' : 'text-gray-300 hover:text-white hover:bg-white/5' }}">
                            Master Data
                            <svg class="w-4 h-4 transition-transform duration-200" :class="masterDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        <div x-show="masterDropdown" x-transition x-cloak class="absolute left-0 mt-2 w-48 bg-white border border-[#E5E7EB] rounded-sm shadow-lg py-1 z-50">
                            <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('admin.users.*') ? 'font-bold text-[#1E3A8A]' : '' }}">Manajemen User</a>
                            <a href="{{ route('admin.proyeks.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A] {{ request()->routeIs('admin.proyeks.*') ? 'font-bold text-[#1E3A8A]' : '' }}">Master Proyek</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Kanan: Profile & Mobile Toggle -->
        <div class="flex items-center gap-4">

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
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#1E3A8A]">Profil Saya</a>
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
            <a href="{{ route('uang-muka.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('uang-muka.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Pengajuan Uang Muka</a>
            
            @if(Auth::user()->isKontraktor() || Auth::user()->isKonsultan() || Auth::user()->isPPK())
                <a href="{{ route('kerja-tambah-kurang.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('kerja-tambah-kurang.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Kerja Tambah Kurang</a>
                
                <div class="pt-2 mt-2 border-t border-white/10">
                    <div class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Pelaporan Proyek</div>
                    <a href="{{ route('laporan-harian.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('laporan-harian.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Laporan Harian</a>
                    <a href="{{ route('laporan-mingguan.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('laporan-mingguan.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Laporan Mingguan</a>
                    <a href="{{ route('laporan-bulanan.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('laporan-bulanan.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Laporan Bulanan</a>
                    <a href="{{ route('permintaan-pembayaran.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('permintaan-pembayaran.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Termin & Tagihan</a>
                </div>
            @endif

            @if(Auth::user()->isSuperAdmin())
                <div class="pt-2 mt-2 border-t border-white/10">
                    <div class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Master Data</div>
                    <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('admin.users.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Manajemen User</a>
                    <a href="{{ route('admin.proyeks.index') }}" class="block px-3 py-2 text-sm {{ request()->routeIs('admin.proyeks.*') ? 'bg-white/10 text-[#FFB800] font-bold' : 'text-gray-300' }} rounded-md">Master Proyek</a>
                </div>
            @endif

            <div class="pt-4 mt-2 border-t border-white/10">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-gray-300 rounded-md">Profil Saya</a>
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

</body>
</html>
