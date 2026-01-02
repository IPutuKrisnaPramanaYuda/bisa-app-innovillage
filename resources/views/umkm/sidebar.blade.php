<aside 
    x-show="sidebarOpen" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="w-64 bg-[#0F244A] text-white flex flex-col h-screen flex-shrink-0 z-50 absolute md:relative shadow-xl"
>
    <div class="p-6 flex items-center justify-between border-b border-white/10">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto object-contain">
            <div class="leading-tight">
                <h1 class="text-xl font-bold tracking-wide">Growth AI</h1>
                <p class="text-[10px] text-blue-200 opacity-80">Smart ERP</p>
            </div>
        </div>

        <button @click="sidebarOpen = false" class="md:hidden text-gray-300 hover:text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <a href="{{ route('umkm.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 {{ request()->routeIs('umkm.dashboard') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <a href="{{ route('umkm.products') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 {{ request()->routeIs('umkm.products*') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="font-medium">Manajemen Produk</span>
        </a>

        <a href="{{ route('umkm.sales') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 {{ request()->routeIs('umkm.sales') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="font-medium">Penjualan</span>
        </a>

        <a href="{{ route('umkm.inventory') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 {{ request()->routeIs('umkm.inventory*') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            <span class="font-medium">Inventori & Stok</span>
        </a>

        <a href="{{ route('umkm.reports') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition duration-200 {{ request()->routeIs('umkm.reports') ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span class="font-medium">Evaluasi & Laporan</span>
        </a>

        <div class="p-4 mt-auto">
            <a href="{{ route('dashboard') }}" class="block w-full py-2 bg-blue-600 hover:bg-blue-500 text-center rounded-lg text-sm font-semibold transition">
                Kembali ke Chat AI
            </a>
        </div>
    </nav>
</aside>