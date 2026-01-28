@php
    // 1. Logika Link: Cek apakah user sedang di Halaman Utama?
    $isHome = request()->routeIs('landing');

    // 2. Logika Warna: Halaman mana saja yang navbarnya boleh transparan di awal?
    // Tambahkan 'shop' di sini agar dia ikut transparan
    $transparentPage = request()->routeIs('landing') || request()->routeIs('shop');

    // Setup Link Navigasi
    $homeLink   = $isHome ? '#beranda' : url('/#beranda');
    $profilLink = $isHome ? '#profil'  : url('/#profil');
    $umkmLink   = $isHome ? '#umkm'    : url('/#umkm');
@endphp

<nav class="fixed w-full z-50 transition-all duration-300" 
     x-data="{ 
        scrolled: false, 
        mobileOpen: false,
        isTransparent: {{ $transparentPage ? 'true' : 'false' }} 
     }" 
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     :class="(scrolled || !isTransparent) ? 'bg-white/95 backdrop-blur-md shadow-lg py-2' : 'bg-transparent py-4'">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            
            <div class="flex items-center gap-2">
                <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo2.png') }}" 
                         class="h-10 w-auto transition transform group-hover:scale-110"
                         :class="(scrolled || !isTransparent) ? '' : 'brightness-0 invert'">

                    <div class="flex items-center gap-3">
                        <span class="text-3xl font-black tracking-tighter leading-none transition duration-300"
                              :class="(scrolled || !isTransparent) ? 'text-[#0F244A]' : 'text-white'">
                            BISA
                        </span>
                        
                        <div class="h-8 w-0.5 rounded-full opacity-50 hidden sm:block" 
                             :class="(scrolled || !isTransparent) ? 'bg-slate-300' : 'bg-white/30'"></div>

                        <div class="hidden sm:flex flex-col justify-center">
                            <span class="text-[10px] font-bold uppercase tracking-wide leading-tight transition duration-300"
                                  :class="(scrolled || !isTransparent) ? 'text-slate-600' : 'text-blue-100'">
                                Bengkala Interactive
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-wide leading-tight transition duration-300"
                                  :class="(scrolled || !isTransparent) ? 'text-slate-500' : 'text-blue-200/80'">
                                Smart Assistant
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="hidden md:flex items-center space-x-8 text-sm">
                <a href="{{ $homeLink }}" class="hover:text-blue-500 transition font-normal" :class="(scrolled || !isTransparent) ? 'text-slate-600' : 'text-white/90'">Beranda</a>
                <a href="{{ $profilLink }}" class="hover:text-blue-500 transition font-normal" :class="(scrolled || !isTransparent) ? 'text-slate-600' : 'text-white/90'">Profil Desa</a>
                <a href="{{ $umkmLink }}" class="hover:text-blue-500 transition font-normal" :class="(scrolled || !isTransparent) ? 'text-slate-600' : 'text-white/90'">Produk UMKM</a>
                
                <a href="{{ route('shop') }}" class="hover:text-blue-500 transition font-normal" 
                   :class="request()->routeIs('shop') ? 'text-blue-600 font-bold' : ((scrolled || !isTransparent) ? 'text-slate-600' : 'text-white/90')">
                   Belanja
                </a>
                
                <span class="cursor-not-allowed opacity-60 hover:text-blue-500 transition font-normal" :class="(scrolled || !isTransparent) ? 'text-slate-400' : 'text-white/60'" title="Segera Hadir">Virtual Tour (Soon)</span>
                
                <div class="flex items-center gap-2 ml-4 border-l pl-6" :class="(scrolled || !isTransparent) ? 'border-slate-200' : 'border-white/20'">
                    @auth
                        <a href="{{ route('umkm.dashboard') }}" class="px-5 py-2.5 rounded-full font-normal shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 text-sm flex items-center gap-2"
                           :class="(scrolled || !isTransparent) ? 'bg-[#0F244A] text-white hover:bg-blue-800' : 'bg-white text-[#0F244A] hover:bg-gray-100'">
                            <span>Dashboard</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="px-5 py-2 rounded-full font-normal text-sm transition"
                           :class="(scrolled || !isTransparent) ? 'text-slate-600 hover:text-[#0F244A] hover:bg-slate-100' : 'text-white hover:text-blue-200 hover:bg-white/10'">
                            Masuk
                        </a>

                        <a href="{{ route('register') }}" 
                           class="px-6 py-2.5 rounded-full font-normal shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 text-sm"
                           :class="(scrolled || !isTransparent) ? 'bg-[#0F244A] text-white hover:bg-blue-800' : 'bg-white text-[#0F244A] hover:bg-gray-100'">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>

            <div class="md:hidden">
                <button @click="mobileOpen = !mobileOpen" :class="(scrolled || !isTransparent) ? 'text-slate-800' : 'text-white'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="md:hidden bg-white border-t border-slate-100 p-4 space-y-3 shadow-xl absolute w-full max-h-[85vh] overflow-y-auto">
        
        <a href="{{ $homeLink }}" @click="mobileOpen = false" class="block text-slate-600 font-normal px-2 py-2 hover:bg-slate-50 rounded">Beranda</a>
        <a href="{{ $profilLink }}" @click="mobileOpen = false" class="block text-slate-600 font-normal px-2 py-2 hover:bg-slate-50 rounded">Profil Desa</a>
        <a href="{{ $umkmLink }}" @click="mobileOpen = false" class="block text-slate-600 font-normal px-2 py-2 hover:bg-slate-50 rounded">Produk UMKM</a>
        
        <a href="{{ route('shop') }}" class="block font-normal px-2 py-2 rounded {{ request()->routeIs('shop') ? 'text-blue-600 bg-blue-50' : 'text-slate-700 hover:bg-slate-50' }}">
            Belanja
        </a>
        
        <div class="block text-slate-400 font-normal px-2 py-2 cursor-not-allowed">
            Virtual Tour (Soon)
        </div>
        
        <div class="border-t border-gray-100 pt-4 flex flex-col gap-3">
            @auth
                <a href="{{ route('umkm.dashboard') }}" class="block w-full text-center bg-[#0F244A] text-white py-3 rounded-xl font-normal">
                    Buka Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="block w-full text-center text-slate-600 border border-slate-200 py-3 rounded-xl font-normal hover:bg-slate-50">
                    Masuk Akun
                </a>
                <a href="{{ route('register') }}" class="block w-full text-center bg-[#0F244A] text-white py-3 rounded-xl font-normal shadow-lg">
                    Daftar Sekarang
                </a>
            @endauth
        </div>
    </div>
</nav>