<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Desa Bengkala') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/tsparticles-slim@2.0.6/tsparticles.slim.bundle.min.js"></script>

        <style>
            body { 
                font-family: 'Plus Jakarta Sans', 'Figtree', sans-serif; 
                overflow-x: hidden; /* Mencegah scroll menyamping di HP */
            }
            [x-cloak] { display: none !important; }

            /* Agar semua gambar & video otomatis mengecil di HP */
            img, video, iframe {
                max-width: 100%;
                height: auto;
            }
        </style>
    </head>
    
    <body class="font-sans text-slate-900 antialiased bg-slate-50 flex flex-col min-h-screen">
        
        <x-navbar />

        <main class="flex-grow">
            {{ $slot }}
        </main>

        <footer class="bg-white border-t border-slate-200 py-8 mt-auto">
            <div class="max-w-7xl mx-auto px-4 text-center text-slate-500 text-sm">
                &copy; {{ date('Y') }} Desa Bengkala. <span class="hidden md:inline">SWARA TEAM UNDIKSHA.</span>
            </div>
        </footer>

        <x-ai-widget />
        <x-accessibility-widget />
    </body>
</html>