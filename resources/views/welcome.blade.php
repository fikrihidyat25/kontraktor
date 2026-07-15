<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DIPRODA (Digitalisasi Proyek Daerah) — Sistem Informasi Konstruksi</title>

    <!-- Poppins Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #0F172B;
            --accent: #FACC15;
        }
        body { font-family: 'Poppins', sans-serif; background: #FFFFFF; color: #0F172B; }
        


        /* Custom scrollbar for webkit */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="antialiased overflow-x-hidden w-full relative" x-data="{ mobileMenuOpen: false, scrolled: false, activeSection: 'beranda' }" @scroll.window="scrolled = (window.pageYOffset > 50)">

<!-- NAVBAR -->
<nav :class="scrolled ? 'bg-[#121A2F]/50 backdrop-blur-xl border-[#FACC15]/30 shadow-[0_8px_32px_rgba(0,0,0,0.1)] py-0' : 'bg-transparent border-transparent py-2'" 
     class="fixed top-0 w-full z-50 border-b transition-all duration-300 px-4 md:px-8 h-16 md:h-20 flex items-center justify-between">
    
    <!-- Kiri: Logo -->
    <div class="flex items-center">
        <a href="/" class="text-[#FACC15] text-xl font-bold tracking-tight">
            DIPRODA
        </a>
    </div>

    <!-- Tengah: Desktop Links (Flat) -->
    <div class="hidden lg:flex items-center gap-8 absolute left-1/2 -translate-x-1/2 transition-all duration-300">
        
        <a href="#" @click="activeSection = 'beranda'" 
           :class="activeSection === 'beranda' ? 'text-[#FACC15] font-bold' : 'text-white/80 hover:text-white'" 
           class="text-[13px] transition-colors duration-300 tracking-wide">Beranda</a>
           
        <a href="#pilar" @click="activeSection = 'pilar'" 
           :class="activeSection === 'pilar' ? 'text-[#FACC15] font-bold' : 'text-white/80 hover:text-white'" 
           class="text-[13px] transition-colors duration-300 tracking-wide">Tentang Sistem</a>
           
        <a href="#alur" @click="activeSection = 'alur'" 
           :class="activeSection === 'alur' ? 'text-[#FACC15] font-bold' : 'text-white/80 hover:text-white'" 
           class="text-[13px] transition-colors duration-300 tracking-wide">Alur Laporan</a>
           

    </div>

    <!-- Kanan: Login Button -->
    <div class="hidden lg:flex items-center gap-4">
        @if(Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="bg-[#FACC15] hover:bg-[#EAB308] text-[#0F172B] font-bold py-2 px-6 rounded-full transition shadow-[0_0_15px_rgba(250,204,21,0.2)]">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="bg-[#FACC15] hover:bg-[#EAB308] text-[#0F172B] font-bold py-2 px-6 rounded-full transition shadow-[0_0_15px_rgba(250,204,21,0.2)] flex items-center gap-2">
                    Masuk Portal
            </a>
            @endauth
        @endif
    </div>

    <!-- Mobile Menu Button -->
    <div class="lg:hidden flex items-center">
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-[#FACC15] focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display: none;"></path>
            </svg>
        </button>
    </div>
</nav>

<!-- Mobile Navigation Menu -->
<div x-show="mobileMenuOpen" x-transition class="lg:hidden bg-[#121A2F] border-b-2 border-[#FACC15] fixed w-full top-16 md:top-20 z-40">
    <div class="flex flex-col px-4 py-4 space-y-3 shadow-lg">
        <a href="#" class="text-[#FACC15] font-medium text-sm border-l-2 border-[#FACC15] pl-3 py-1">Beranda</a>
        <a href="#pilar" @click="mobileMenuOpen = false" class="text-white/70 hover:text-white text-sm font-medium transition pl-3 py-1">Tentang Sistem</a>
        <a href="#alur" @click="mobileMenuOpen = false" class="text-white/70 hover:text-white text-sm font-medium transition pl-3 py-1">Alur Laporan</a>

        
        <div class="pt-3 border-t border-white/10 mt-2">
            @if(Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-flex justify-center items-center gap-2 bg-[#FACC15] hover:bg-[#EAB308] text-[#0F172B] text-center text-sm font-bold py-2.5 px-6 rounded-full transition w-auto shadow-[0_0_15px_rgba(250,204,21,0.2)]">
                        Dashboard Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex justify-center items-center gap-2 bg-[#FACC15] hover:bg-[#EAB308] text-[#0F172B] text-center text-sm font-bold py-2.5 px-6 rounded-full transition w-auto shadow-[0_0_15px_rgba(250,204,21,0.2)]">
                        Masuk Sistem
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                @endauth
            @endif
        </div>
    </div>
</div>

<!-- HERO -->
<section class="relative min-h-[75vh] md:min-h-[85vh] flex flex-col justify-center border-b border-[#FACC15]/20 overflow-hidden"
         x-data="{
             images: [
                 '{{ asset('images/construction_sunset_1782405434589.png') }}',
                 '{{ asset('images/blueprint_desk_1782405450850.png') }}',
                 '{{ asset('images/tech_tablet_1782405462735.png') }}',
                 '{{ asset('images/modern_skyscraper_1782405475148.png') }}',
                 '{{ asset('images/construction_team_1782405486046.png') }}'
             ],
             activeImage: 0,
             init() {
                 setInterval(() => {
                     this.activeImage = (this.activeImage + 1) % this.images.length;
                 }, 5000);
             }
         }">
         
    <!-- Slider Backgrounds with Horizontal Slide effect -->
    <div class="absolute inset-0 z-0 flex transition-transform duration-1000 ease-in-out"
         :style="`transform: translateX(-${activeImage * 100}%);`">
        <template x-for="(image, index) in images" :key="index">
            <div class="min-w-full h-full bg-cover bg-center"
                 :style="`background-image: url('${image}');`">
            </div>
        </template>
    </div>

    <!-- Overlay Gradient to ensure text readability (Lightened) -->
    <div class="absolute inset-0 z-10" style="background-image: linear-gradient(to right, rgba(15, 23, 43, 0.8) 0%, rgba(15, 23, 43, 0.4) 40%, rgba(15, 23, 43, 0) 100%);"></div>

    <div class="relative z-20 max-w-2xl mt-10 md:mt-0 px-6 md:px-12 lg:px-24">
        <h1 data-aos="fade-up" data-aos-duration="1000" class="text-white text-4xl md:text-5xl lg:text-[54px] font-bold leading-[1.1] mb-6 tracking-tight">
            Digitalisasi Tata Kelola dan Monitoring Proyek<br class="hidden md:block">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#FACC15] to-[#FFCA28]">Daerah.</span>
        </h1>
        <p data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200" class="text-white/75 text-sm md:text-[15px] max-w-lg mb-8 leading-relaxed">
            Platform monitoring dan pelaporan proyek berbasis web yang menyatukan alur kerja Kontraktor, Konsultan Pengawas, dan PPK dalam satu sistem yang terstandarisasi sesuai regulasi Direktorat Jenderal Bina Marga.
        </p>
        <div data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400" class="flex flex-col sm:flex-row items-start sm:items-center gap-4 md:gap-5">
            @if(Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-[#FACC15] hover:bg-[#EAB308] text-[#0F172B] text-[13px] md:text-sm font-bold py-3 md:py-3.5 px-8 rounded-full transition w-full sm:w-auto text-center shadow-lg shadow-[#FACC15]/20">Akses Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="bg-[#FACC15] hover:bg-[#EAB308] text-[#0F172B] text-[13px] md:text-sm font-bold py-3 md:py-3.5 px-8 rounded-full transition w-full sm:w-auto text-center shadow-lg shadow-[#FACC15]/20">Masuk Portal</a>
                @endauth
            @endif
            <a href="#pilar" class="bg-transparent border border-white/40 hover:bg-white/10 text-white text-[13px] md:text-sm font-medium py-3 md:py-3.5 px-8 rounded-full transition w-full sm:w-auto text-center backdrop-blur-sm">Pelajari Spesifikasi</a>
        </div>
        
    </div>

    <!-- Slider Indicators -->
    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-30 flex items-center gap-2">
        <template x-for="(image, index) in images" :key="index">
            <button @click="activeImage = index" 
                    class="h-1.5 rounded-full transition-all duration-300"
                    :class="activeImage === index ? 'w-8 bg-[#FACC15]' : 'w-2 bg-white/30 hover:bg-white/50'"></button>
        </template>
    </div>
</section>

<!-- ALUR PENGGUNAAN -->
<section class="py-16 bg-[#F8FAFC]" id="alur">
    <div class="w-full px-6 md:px-12 lg:px-20 2xl:px-32 mx-auto">
        <h2 data-aos="fade-up" class="text-2xl font-bold text-[#0F172B] mb-8">Alur Penggunaan DIPRODA</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div data-aos="fade-up" data-aos-delay="100" class="bg-white border border-gray-200 p-6 flex flex-col items-center text-center shadow-sm">
                <div class="w-10 h-10 bg-gray-100 text-[#1E3A8A] font-bold flex items-center justify-center rounded mb-4 text-xl">1</div>
                <h3 class="text-[#1E3A8A] font-bold mb-4">Input Laporan</h3>
                <img src="{{ asset('images/inputan.jpeg') }}" class="w-full aspect-video mb-4 rounded-lg object-cover" alt="Input Laporan">
                <p class="text-gray-500 text-sm leading-relaxed">
                    Kontraktor mengunggah laporan kemajuan harian, mingguan, dan foto dokumentasi proyek beserta Rencana Anggara Biata (RAB) terbaru.
                </p>
            </div>
            <!-- Card 2 -->
            <div data-aos="fade-up" data-aos-delay="200" class="bg-white border border-gray-200 p-6 flex flex-col items-center text-center shadow-sm">
                <div class="w-10 h-10 bg-gray-100 text-[#1E3A8A] font-bold flex items-center justify-center rounded mb-4 text-xl">2</div>
                <h3 class="text-[#1E3A8A] font-bold mb-4">Verifikasi Konsultan</h3>
                <img src="{{ asset('images/verifikasi.jpg') }}" class="w-full aspect-video mb-4 rounded-lg object-cover" alt="Verifikasi">
                <p class="text-gray-500 text-sm leading-relaxed">
                    Konsultan Pengawas memeriksa dan memvalidasi kesesuainlaporan fisik dengan kondisi lapangan sebelum diteruskan ke Owner.
                </p>
            </div>
            <!-- Card 3 -->
            <div data-aos="fade-up" data-aos-delay="300" class="bg-white border border-gray-200 p-6 flex flex-col items-center text-center shadow-sm">
                <div class="w-10 h-10 bg-gray-100 text-[#1E3A8A] font-bold flex items-center justify-center rounded mb-4 text-xl">3</div>
                <h3 class="text-[#1E3A8A] font-bold mb-4">Pemantauan Owner</h3>
                <img src="{{ asset('images/pemantauan.jpeg') }}" class="w-full aspect-video mb-4 rounded-lg object-cover" alt="Pemantauan">
                <p class="text-gray-500 text-sm leading-relaxed">
                    Owner (Dinas/PPK) memantau dashboard progres, menyetujui termin pembayaran, dan menganalisis kurva-s proyek secara komprehensif.
                </p>
            </div>
        </div>
        <div class="mt-10 text-center">
            <button class="border border-gray-300 bg-white text-gray-800 font-bold py-3 px-6 hover:bg-gray-50 transition text-sm">
                UNDUH PANDUAN LENGKAP (PDF)
            </button>
        </div>
    </div>
</section>

<!-- FITUR UNGGULAN -->
<section class="py-20 bg-white" id="pilar">
    <div class="w-full px-6 md:px-12 lg:px-20 2xl:px-32 mx-auto text-center">
        <h2 data-aos="fade-up" class="text-4xl font-bold text-[#0F172B] mb-4">Fitur Unggulan Sistem</h2>
        <p data-aos="fade-up" data-aos-delay="100" class="text-gray-600 mb-10 max-w-2xl mx-auto">
            DIPRODA hadir dengan berbagai fitur unggulan untuk memastikan pengelolaan proyek yang transparan, akuntabel, dan efisien.
        </p>
        <div data-aos="zoom-in" data-aos-delay="200" class="w-12 h-1 bg-[#FACC15] mx-auto mb-12"></div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
            <!-- Feature 1 -->
            <div data-aos="fade-up" data-aos-delay="100" class="bg-white border border-gray-100 rounded-xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] flex gap-4">
                <div class="w-16 h-16 bg-[#F0F5FF] rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-8 h-8 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 13v-1m4 1v-3m4 3V8M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-[#0F172B] mb-2">Monitoring Real-Time</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">Pantau progres proyek secara langsung dan real-time dari mana saja untuk pengambilan keputusan yang cepat dan tepat.</p>
                    <div class="w-8 h-1 bg-[#2563EB] mt-4"></div>
                </div>
            </div>
            <!-- Feature 2 -->
            <div data-aos="fade-up" data-aos-delay="200" class="bg-white border border-gray-100 rounded-xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] flex gap-4">
                <div class="w-16 h-16 bg-[#F0F5FF] rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-8 h-8 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-[#0F172B] mb-2">Dokumentasi Digital</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">Kelola dan simpan seluruh dokumen proyek secara digital terstruktur, mudah diakses, dan aman.</p>
                    <div class="w-8 h-1 bg-[#2563EB] mt-4"></div>
                </div>
            </div>
            <!-- Feature 3 -->
            <div data-aos="fade-up" data-aos-delay="300" class="bg-white border border-gray-100 rounded-xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] flex gap-4">
                <div class="w-16 h-16 bg-[#F0F5FF] rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-8 h-8 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-[#0F172B] mb-2">Tracking Lokasi Proyek</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">Lacak lokasi proyek dengan akurasi tinggi untuk memastikan kegiatan proyek sesuai dengan perencanaan.</p>
                    <div class="w-8 h-1 bg-[#2563EB] mt-4"></div>
                </div>
            </div>
            <!-- Feature 4 -->
            <div data-aos="fade-up" data-aos-delay="400" class="bg-white border border-gray-100 rounded-xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] flex gap-4">
                <div class="w-16 h-16 bg-[#F0F5FF] rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-8 h-8 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-[#0F172B] mb-2">Dashboard Analitik</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">Dapatkan analisis data dan laporan kinerja proyek melalui dashboard interaktif yang informatif dan mudah dipahami.</p>
                    <div class="w-8 h-1 bg-[#2563EB] mt-4"></div>
                </div>
            </div>
            <!-- Feature 5 -->
            <div data-aos="fade-up" data-aos-delay="500" class="bg-white border border-gray-100 rounded-xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] flex gap-4">
                <div class="w-16 h-16 bg-[#F0F5FF] rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-8 h-8 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-[#0F172B] mb-2">Notifikasi Otomatis</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">Terima notifikasi otomatis untuk pengingat tenggat waktu, update proyek, dan informasi penting lainnya.</p>
                    <div class="w-8 h-1 bg-[#2563EB] mt-4"></div>
                </div>
            </div>
            <!-- Feature 6 -->
            <div data-aos="fade-up" data-aos-delay="600" class="bg-white border border-gray-100 rounded-xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] flex gap-4">
                <div class="w-16 h-16 bg-[#F0F5FF] rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-8 h-8 text-[#2563EB]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-[#0F172B] mb-2">Keamanan Data</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">Sistem keamanan berlapis untuk melindungi data proyek dan informasi sensitif dari akses tidak sah.</p>
                    <div class="w-8 h-1 bg-[#2563EB] mt-4"></div>
                </div>
            </div>
        </div>
        
        <div data-aos="zoom-in" data-aos-delay="300" class="mt-12 max-w-4xl mx-auto bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-lg flex items-center gap-4">
            <div class="w-10 h-10 bg-[#2563EB] rounded-full flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <p class="text-sm font-medium text-[#2563EB] text-left">
                DIPRODA berkomitmen menghadirkan sistem yang andal, aman, dan mudah digunakan untuk mendukung tata kelola proyek daerah yang lebih baik.
            </p>
        </div>
    </div>
</section>

<!-- ZIG ZAG FEATURES -->
<section class="py-20 bg-[#F8FAFC]">
    <div class="w-full px-6 md:px-12 lg:px-24 2xl:px-40 mx-auto space-y-32">
        
        <!-- Zig 1 -->
        <div class="flex flex-col md:flex-row items-center gap-12">
            <div data-aos="fade-right" class="w-full md:w-1/2">
                <img src="{{ asset('images/Lacak Semua Kemajuan Proye.jpg') }}" alt="Lacak Semua Kemajuan Proyek" class="rounded-xl shadow-lg w-full md:w-[90%] aspect-video object-cover mx-auto md:ml-0">
            </div>
            <div data-aos="fade-left" class="w-full md:w-1/2">
                <h3 class="text-2xl font-bold text-[#0F172B] mb-6">Lacak Semua <span class="text-[#1E40AF]">Kemajuan Proyek</span></h3>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <span class="w-1.5 h-1.5 rounded-full bg-black mt-2 mr-3 shrink-0"></span>
                        <span class="text-gray-700 text-sm">Visualisasikan jadwal dan status proyek di seluruh lokasi proyek Anda, secara real time</span>
                    </li>
                    <li class="flex items-start">
                        <span class="w-1.5 h-1.5 rounded-full bg-black mt-2 mr-3 shrink-0"></span>
                        <span class="text-gray-700 text-sm">Tetapkan tugas dan pantau progres pekerjaan</span>
                    </li>
                    <li class="flex items-start">
                        <span class="w-1.5 h-1.5 rounded-full bg-black mt-2 mr-3 shrink-0"></span>
                        <span class="text-gray-700 text-sm">Alokasikan sumber daya</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Zag 2 -->
        <div class="flex flex-col md:flex-row-reverse items-center gap-12">
            <div data-aos="fade-left" class="w-full md:w-1/2">
                <img src="{{ asset('images/Koordinasikan Lokasi Kerja.png') }}" alt="Koordinasikan Lokasi Kerja" class="rounded-xl shadow-lg w-full md:w-[90%] aspect-video object-cover mx-auto md:mr-0">
            </div>
            <div data-aos="fade-right" class="w-full md:w-1/2 md:pl-8">
                <h3 class="text-2xl font-bold text-[#0F172B] mb-6">Koordinasikan <span class="text-[#1E40AF]">Lokasi Kerja</span></h3>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <span class="w-1.5 h-1.5 rounded-full bg-black mt-2 mr-3 shrink-0"></span>
                        <span class="text-gray-700 text-sm">Bagikan dokumentasi dokumen, daftar kebutuhan</span>
                    </li>
                    <li class="flex items-start">
                        <span class="w-1.5 h-1.5 rounded-full bg-black mt-2 mr-3 shrink-0"></span>
                        <span class="text-gray-700 text-sm">Menerima Informasi Lokasi Kerja yang Ditentukan</span>
                    </li>
                    <li class="flex items-start">
                        <span class="w-1.5 h-1.5 rounded-full bg-black mt-2 mr-3 shrink-0"></span>
                        <span class="text-gray-700 text-sm">Pantau proyek lokasi kerja</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Zig 3 -->
        <div class="flex flex-col md:flex-row items-center gap-12">
            <div data-aos="fade-right" class="w-full md:w-1/2">
                <img src="{{ asset('images/Gemini_Generated_Image_9sovxa9sovxa9sov.png') }}" alt="Tampilan & Responsive" class="rounded-xl shadow-lg w-full md:w-[90%] aspect-video object-cover mx-auto md:ml-0">
            </div>
            <div data-aos="fade-left" class="w-full md:w-1/2">
                <h3 class="text-2xl font-bold text-[#0F172B] mb-6">Tampilan & <span class="text-[#1E40AF]">Responsive</span></h3>
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <span class="w-1.5 h-1.5 rounded-full bg-black mt-2 mr-3 shrink-0"></span>
                        <span class="text-gray-700 text-sm">UI atau tampilang terlihat nyaman saat digunakan di berbagai perangkat</span>
                    </li>
                    <li class="flex items-start">
                        <span class="w-1.5 h-1.5 rounded-full bg-black mt-2 mr-3 shrink-0"></span>
                        <span class="text-gray-700 text-sm">Tema tampilan cenderung ke bagaimana tema pada konstruksi</span>
                    </li>
                    <li class="flex items-start">
                        <span class="w-1.5 h-1.5 rounded-full bg-black mt-2 mr-3 shrink-0"></span>
                        <span class="text-gray-700 text-sm">Warna yang digunakan mencakup nuansa warna kebutuhan kontruksi</span>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</section>

<!-- FAQ -->
<section class="py-24 bg-[#F8FAFC]">
    <div class="max-w-6xl mx-auto px-6 lg:px-12">
        <div data-aos="fade-up" class="text-center mb-12">
            <h2 class="text-3xl font-bold text-black mb-4">FAQ</h2>
            <p class="text-xl text-gray-800">Jawaban atas pertanyaan yang paling sering diajukan</p>
        </div>

        <div class="space-y-4" x-data="{ activeAccordion: null }">
            <!-- Accordion 1 -->
            <div data-aos="fade-up" data-aos-delay="100" class="bg-[#052370] rounded-xl overflow-hidden">
                <button @click="activeAccordion = activeAccordion === 1 ? null : 1" class="w-full px-6 py-5 text-left flex justify-between items-center text-white focus:outline-none">
                    <span class="font-bold text-[15px]">Bagaimana proses orientasi untuk anggota baru ?</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" :class="activeAccordion === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="activeAccordion === 1" style="display: none;" class="px-6 pb-5 text-white/80 text-sm leading-relaxed">
                    Proses orientasi akan dipandu langsung oleh tim support kami yang akan menjelaskan langkah-langkah penggunaan sistem secara detail.
                </div>
            </div>

            <!-- Accordion 2 -->
            <div data-aos="fade-up" data-aos-delay="200" class="bg-[#052370] rounded-xl overflow-hidden">
                <button @click="activeAccordion = activeAccordion === 2 ? null : 2" class="w-full px-6 py-5 text-left flex justify-between items-center text-white focus:outline-none">
                    <span class="font-bold text-[15px]">Bagaimana proses tata kelola pemantaian dalam sistem ini ?</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" :class="activeAccordion === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="activeAccordion === 2" style="display: none;" class="px-6 pb-5 text-white/80 text-sm leading-relaxed">
                    Tata kelola pemantauan dilakukan secara real-time dengan integrasi berbagai input data dari pihak terkait, sehingga progres dan kendala terpantau akurat.
                </div>
            </div>

            <!-- Accordion 3 -->
            <div data-aos="fade-up" data-aos-delay="300" class="bg-[#052370] rounded-xl overflow-hidden">
                <button @click="activeAccordion = activeAccordion === 3 ? null : 3" class="w-full px-6 py-5 text-left flex justify-between items-center text-white focus:outline-none">
                    <span class="font-bold text-[15px]">Bagaimana setiap entitas dapat melihat pemantauan tata kelola ?</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" :class="activeAccordion === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="activeAccordion === 3" style="display: none;" class="px-6 pb-5 text-white/80 text-sm leading-relaxed">
                    Setiap entitas memiliki dashboard masing-masing sesuai hak akses dan rolenya untuk memantau progres proyek dengan transparan.
                </div>
            </div>

            <!-- Accordion 4 -->
            <div data-aos="fade-up" data-aos-delay="400" class="bg-[#052370] rounded-xl overflow-hidden">
                <button @click="activeAccordion = activeAccordion === 4 ? null : 4" class="w-full px-6 py-5 text-left flex justify-between items-center text-white focus:outline-none">
                    <span class="font-bold text-[15px]">Bagaimana proses orientasi untuk anggota baru ?</span>
                    <svg class="w-6 h-6 transform transition-transform duration-200" :class="activeAccordion === 4 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="activeAccordion === 4" style="display: none;" class="px-6 pb-5 text-white/80 text-sm leading-relaxed">
                    Sama seperti poin pertama, orientasi akan diberikan secara menyeluruh saat akun pertama kali dibuat oleh administrator sistem.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="bg-[#24428B] pt-12 pb-8 px-6 md:px-12">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-end gap-8">
        <div>
            <div class="text-white font-bold mb-4">DIPRODA</div>
            <p class="text-[11px] text-white/80 font-medium">© 2024 Diproda .</p>
          <p class="text-[11px] text-white/80 font-medium mt-1">ITP x UBH </p>
        </div>
        <!-- <div class="flex items-center gap-6 text-white text-xs font-semibold">
            <a href="#" class="text-[#FACC15] hover:underline">Beranda</a>
            <a href="#" class="hover:underline">Regulasi</a>
            <a href="#" class="hover:underline">Sistem</a>
            <a href="#" class="hover:underline">Bantuan</a>
        </div> -->
    </div>
</footer>

<!-- AOS Animation JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        once: true,
        offset: 50,
        duration: 800,
        easing: 'ease-out-cubic',
    });
</script>

</body>
</html>
