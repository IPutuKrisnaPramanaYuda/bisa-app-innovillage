<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'BISA AI') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            body { 
                font-family: 'Plus Jakarta Sans', 'Figtree', sans-serif; 
                overflow-x: hidden; /* Anti geser samping */
            }
            
            /* Gambar/Video otomatis mengecil di HP */
            img, video, iframe {
                max-width: 100%;
                height: auto;
            }

            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen">
            
            <nav class="bg-white border-b border-gray-100 sticky top-0 z-40 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="shrink-0 flex items-center">
                                <a href="{{ route('umkm.dashboard') }}" class="flex items-center gap-2 font-bold text-[#0F244A] hover:text-blue-600 transition group">
                                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                    <span class="text-sm md:text-base">Kembali</span>
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="px-3 py-1.5 md:px-4 md:py-2 text-xs md:text-sm font-medium text-gray-500 bg-gray-50 rounded-full border border-gray-200 flex items-center gap-2">
                                <span class="hidden sm:inline">Hai,</span>
                                <span class="font-bold text-[#0F244A] truncate max-w-[100px] sm:max-w-none">{{ Auth::user()->name }}</span>
                                <div class="w-6 h-6 rounded-full bg-[#0F244A] text-white flex items-center justify-center text-xs">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            @if (isset($header))
                <header class="bg-white shadow relative z-30">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main class="py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <x-umkm-ai-widget />
        <x-accessibility-widget />
    </body>
</html>