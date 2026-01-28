<x-auth-layout>
    <div class="h-screen flex w-full font-sans bg-[#0F172A] overflow-hidden">
        
        <div class="hidden lg:flex w-1/2 flex-col justify-center items-center text-white p-12 relative z-0">
            <div id="tsparticles" class="absolute top-0 left-0 w-full h-full z-0 opacity-40"></div>
            
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none z-0">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
                </svg>
            </div>

            <div class="relative z-10 text-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Growth AI" class="w-32 h-auto mx-auto mb-6 drop-shadow-2xl hover:scale-105 transition-transform duration-300">
                
                <h1 class="text-4xl font-extrabold mb-4 tracking-tight">BISA</h1>
                <p class="text-blue-200 text-lg max-w-md mx-auto font-light leading-relaxed">
                   Bengkala Interactive Smart Assistant.
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-50 relative z-10 lg:rounded-l-[0px] shadow-2xl h-full overflow-y-auto">
            
            <div class="w-full max-w-md p-8 bg-white rounded-3xl shadow-sm border border-gray-100 my-auto">
                
                <div class="text-center mb-8 lg:hidden">
                    <img src="{{ asset('images/logo2.png') }}" alt="Logo" class="w-20 mx-auto mb-4">
                    <h2 class="text-2xl font-bold text-[#0F172A]">BISA</h2>
                    <p class="text-gray-500 text-sm">Masuk ke akun Anda</p>
                </div>

                <div class="mb-8 hidden lg:block">
                    <h2 class="text-2xl font-bold text-[#0F172A]">Selamat Datang Kembali!</h2>
                    <p class="text-gray-500 text-sm mt-1">Masukkan detail akun Anda untuk masuk.</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                        <input id="email" class="w-full px-5 py-3 rounded-xl border border-gray-300 focus:border-[#0F172A] focus:ring-2 focus:ring-[#0F172A]/20 transition-all outline-none" 
                               type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div x-data="{ show: false }">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input id="password" 
                                   :type="show ? 'text' : 'password'" 
                                   class="w-full px-5 py-3 pr-12 rounded-xl border border-gray-300 focus:border-[#0F172A] focus:ring-2 focus:ring-[#0F172A]/20 transition-all outline-none" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password" 
                                   placeholder="••••••••" />
                            
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none cursor-pointer">
                                <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-1.563 3.029m5.858.908l3.59 3.59" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#0F172A] shadow-sm focus:ring-[#0F172A] cursor-pointer" name="remember">
                            <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-800 transition-colors">Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-blue-600 hover:text-blue-800 font-semibold transition-colors" href="{{ route('password.request') }}">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="w-full bg-[#0F172A] text-white py-3.5 rounded-xl font-bold hover:bg-blue-900 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 active:scale-[0.98]">
                        Masuk Sekarang
                    </button>
                </form>

                <div class="my-6 flex items-center before:mt-0.5 before:flex-1 before:border-t before:border-gray-200 after:mt-0.5 after:flex-1 after:border-t after:border-gray-200">
                    <p class="mx-4 mb-0 text-center font-semibold text-gray-400 text-xs tracking-wider uppercase">Atau masuk dengan</p>
                </div>

                <a href="{{ route('google.login') }}" class="w-full flex items-center justify-center gap-3 bg-white border border-gray-200 text-gray-700 py-3.5 rounded-xl font-bold hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-[0.98]">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21.35,11.1H12.18V13.83H18.69C18.36,17.64 15.19,19.27 12.19,19.27C8.36,19.27 5,16.25 5,12C5,7.9 8.2,4.73 12.2,4.73C15.29,4.73 17.1,6.7 17.1,6.7L19,4.72C19,4.72 16.56,2 12.1,2C6.42,2 2.03,6.8 2.03,12C2.03,17.05 6.16,22 12.25,22C17.6,22 21.5,18.33 21.5,12.91C21.5,11.76 21.35,11.1 21.35,11.1V11.1Z" fill="#4285F4"/><path d="M11.68,6.57C10.68,6.57 9.68,6.29 8.8,5.73L7.66,8.87C9.85,10.13 12.52,10.13 14.71,8.87L13.58,5.73C12.69,6.29 11.68,6.57 11.68,6.57Z" fill="#34A853"/><path d="M22,12C22,11.45 21.95,10.91 21.86,10.39H11.68V13.73H18.67C18.53,14.56 18.08,15.31 17.39,15.85L18.68,18.77C21.32,16.84 22,14.11 22,12Z" fill="#FBBC05"/><path d="M11.68,17.43C12.68,17.43 13.69,17.71 14.57,18.27L15.71,15.13C13.52,13.87 10.85,13.87 8.66,15.13L9.79,18.27C10.68,17.71 11.68,17.43 11.68,17.43Z" fill="#EA4335"/></svg>
                    Google
                </a>

                <p class="mt-8 text-center text-sm text-gray-500 font-medium">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="text-[#0F172A] font-bold hover:underline">Daftar disini</a>
                </p>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if(window.tsParticles) {
                tsParticles.load('tsparticles', {
                    particles: {
                        number: { value: 60, density: { enable: true, value_area: 800 } },
                        color: { value: "#ffffff" },
                        shape: { type: "circle" },
                        opacity: { value: 0.3, random: true },
                        size: { value: 2, random: true },
                        move: { enable: true, speed: 0.8 }
                    },
                    retina_detect: true
                });
            }
        });
    </script>
</x-auth-layout>