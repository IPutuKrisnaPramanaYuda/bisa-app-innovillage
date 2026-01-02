<header class="bg-white px-8 py-4 flex justify-between items-center shadow-sm sticky top-0 z-40">
    
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-[#0F244A] focus:outline-none transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
        </button>

        <h2 class="text-2xl font-bold text-gray-800">{{ $title ?? 'Dashboard' }}</h2>
    </div>

    <div class="flex items-center gap-4">
        
        <button class="p-2 text-gray-400 hover:text-gray-600 bg-gray-50 rounded-full transition relative">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
        </button>

        <div class="h-8 w-px bg-gray-200 mx-1"></div>

        <div class="relative" x-data="{ dropdownOpen: false }">
            
            <button @click="dropdownOpen = !dropdownOpen" @click.outside="dropdownOpen = false" class="flex items-center gap-3 focus:outline-none group">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-gray-700 group-hover:text-[#0F244A] transition">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate max-w-[120px]">{{ Auth::user()->umkm->name ?? 'Pemilik Toko' }}</p>
                </div>
                <div class="w-11 h-11 rounded-full bg-[#0F244A] text-white flex items-center justify-center font-bold text-sm border-2 border-white shadow-sm group-hover:shadow-md transition">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
            </button>

            <div x-show="dropdownOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden transform origin-top-right z-50"
                 style="display: none;">
                
                <div class="block sm:hidden px-4 py-3 border-b bg-gray-50">
                    <p class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                </div>

                <div class="py-2">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition flex items-center gap-2">
                        <span>🤖</span> Kembali ke Chat AI
                    </a>

                    <a href="#" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-100 transition flex items-center gap-2">
                        <span>⚙️</span> Setelan Akun
                    </a>

                    <div class="border-t border-gray-100 my-1"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition flex items-center gap-2 font-medium">
                            <span>🚪</span> Keluar
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</header>