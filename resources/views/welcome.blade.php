<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <title>Desa Bengkala - Wisata & UMKM Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; scroll-behavior: smooth; }
        .chat-popup { display: none; }
        /* Animasi Loading Chat */
        .typing-indicator span {
            display: inline-block; width: 6px; height: 6px; background-color: #fff; border-radius: 50%;
            animation: typing 1.4s infinite ease-in-out both;
        }
        .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
        .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }
        @keyframes typing { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="fixed w-full bg-white/95 backdrop-blur z-50 shadow-sm transition-all duration-300">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-2xl font-bold text-indigo-700 flex items-center gap-2">
                BENGKALA<span class="text-orange-500">.ID</span>
            </a>

            <div class="hidden md:flex space-x-8 font-medium text-gray-600">
                <a href="#profil" class="hover:text-indigo-600 transition">Profil Desa</a>
                <a href="#wisata" class="hover:text-indigo-600 transition">Wisata</a>
                <a href="{{ route('marketplace.index') }}" class="hover:text-indigo-600 transition">Belanja</a>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('cart.index') }}" class="relative text-gray-600 hover:text-orange-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs w-5 h-5 flex items-center justify-center rounded-full">
                            {{ \App\Models\Cart::where('user_id', auth()->id())->sum('quantity') }}
                        </span>
                    </a>

                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 bg-indigo-50 px-4 py-2 rounded-full hover:bg-indigo-100 transition">
                        <div class="w-8 h-8 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="font-semibold text-indigo-700 text-sm hidden sm:inline">Profil</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 font-semibold hover:text-indigo-600 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-5 py-2 rounded-full font-bold hover:bg-indigo-700 shadow-md transition">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <section id="profil" class="pt-32 pb-20 bg-gradient-to-br from-indigo-900 via-blue-900 to-indigo-800 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="container mx-auto px-6 text-center relative z-10">
            <h1 class="text-5xl md:text-7xl font-extrabold mb-6 tracking-tight">Desa Adat <span class="text-orange-400">Bengkala</span></h1>
            <p class="text-xl text-indigo-100 mb-10 max-w-2xl mx-auto leading-relaxed">
                Pusat budaya "Kolok" yang mendunia. Temukan kearifan lokal, wisata inklusif, dan produk UMKM asli warga desa dalam satu platform digital.
            </p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('marketplace.index') }}" class="bg-orange-500 text-white font-bold px-8 py-4 rounded-full shadow-lg hover:bg-orange-600 hover:scale-105 transition transform">
                    Mulai Belanja 🛍️
                </a>
                <a href="#wisata" class="bg-white/10 backdrop-blur border border-white/30 text-white font-bold px-8 py-4 rounded-full hover:bg-white/20 transition">
                    Jelajah Wisata
                </a>
            </div>
        </div>
    </section>

   <div class="fixed bottom-6 right-6 z-[999]">
        <button onclick="toggleChat()" class="bg-indigo-600 text-white p-4 rounded-full shadow-2xl hover:bg-indigo-700 hover:scale-110 transition flex items-center gap-2 group cursor-pointer">
            <span class="text-2xl group-hover:animate-bounce">🤖</span> 
            <span class="font-bold pr-2">Tanya AI</span>
        </button>

        <div id="chat-window" class="hidden absolute bottom-20 right-0 w-80 md:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transform transition-all duration-300 origin-bottom-right scale-95 opacity-0">
            <div class="bg-indigo-600 p-4 text-white font-bold flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="bg-white/20 p-1 rounded">🤖</span> Asisten Bengkala
                </div>
                <button type="button" onclick="toggleChat()" class="hover:bg-indigo-700 p-1 rounded">✕</button>
            </div>
            
            <div class="h-80 p-4 overflow-y-auto bg-gray-50 text-sm space-y-3" id="chat-content">
                <div class="flex justify-start">
                    <div class="bg-white border border-gray-200 p-3 rounded-tr-xl rounded-br-xl rounded-bl-xl shadow-sm text-gray-700 max-w-[85%]">
                        👋 Halo! Saya AI Desa Bengkala. Mau tanya tentang wisata, produk, atau cara belanja?
                    </div>
                </div>
            </div>

            <div class="p-3 border-t bg-white">
                <form id="chat-form" class="flex gap-2 relative">
                    <input type="text" id="chat-input" class="w-full bg-gray-100 border-0 rounded-full px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Ketik pesan..." autocomplete="off">
                    <button type="submit" class="bg-indigo-600 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-indigo-700 transition shadow-md">
                        ➤
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Definisi Variabel
        const chatWindow = document.getElementById('chat-window');
        const chatContent = document.getElementById('chat-content');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Fungsi Buka/Tutup Chat (Wajib ada di Global Scope)
        function toggleChat() {
            chatWindow.classList.toggle('hidden');
            
            if(!chatWindow.classList.contains('hidden')) {
                // Animasi Masuk
                setTimeout(() => {
                    chatWindow.classList.remove('scale-95', 'opacity-0');
                    chatWindow.classList.add('scale-100', 'opacity-100');
                    chatInput.focus();
                }, 10);
            } else {
                // Animasi Keluar
                chatWindow.classList.remove('scale-100', 'opacity-100');
                chatWindow.classList.add('scale-95', 'opacity-0');
            }
        }

        // Event Listener untuk Submit Form
        if (chatForm) {
            chatForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Mencegah Refresh Halaman

                const message = chatInput.value.trim();
                if(!message) return;

                // 1. Tampilkan Pesan User
                appendMessage(message, 'user');
                chatInput.value = '';
                
                // 2. Tampilkan Loading
                const loadingId = appendLoading();

                // 3. Kirim ke Server (AJAX)
                fetch("{{ route('chat.public') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({ message: message })
                })
                .then(response => response.json()) // Baca sebagai JSON
                .then(data => {
                    // Hapus Loading
                    const loadingElement = document.getElementById(loadingId);
                    if(loadingElement) loadingElement.remove();
                    
                    // Tampilkan Balasan AI
                    if(data.reply) {
                        appendMessage(data.reply, 'ai');
                    } else {
                        appendMessage("Maaf, saya tidak mengerti.", 'ai');
                    }
                })
                .catch(error => {
                    // Hapus Loading jika Error
                    const loadingElement = document.getElementById(loadingId);
                    if(loadingElement) loadingElement.remove();
                    
                    appendMessage("Maaf, koneksi gangguan.", 'ai');
                    console.error('Error:', error);
                });
            });
        }

        // Helper: Menambah Balon Chat
        function appendMessage(text, sender) {
            const div = document.createElement('div');
            div.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;
            
            const bubble = document.createElement('div');
            // Style berbeda untuk User (Biru) dan AI (Putih)
            bubble.className = sender === 'user' 
                ? 'bg-indigo-600 text-white p-3 rounded-tl-xl rounded-tr-xl rounded-bl-xl shadow-md text-sm max-w-[85%]'
                : 'bg-white border border-gray-200 p-3 rounded-tr-xl rounded-br-xl rounded-bl-xl shadow-sm text-gray-700 max-w-[85%]';
            
            // Render text (bisa pakai innerHTML kalau mau bold/italic, tapi hati-hati XSS)
            // Untuk aman pakai innerText dulu
            bubble.innerText = text; 
            
            div.appendChild(bubble);
            chatContent.appendChild(div);
            
            // Auto Scroll ke Bawah
            chatContent.scrollTop = chatContent.scrollHeight;
        }

        // Helper: Animasi Loading (...)
        function appendLoading() {
            const id = 'loading-' + Date.now();
            const div = document.createElement('div');
            div.id = id;
            div.className = 'flex justify-start';
            div.innerHTML = `
                <div class="bg-gray-100 p-3 rounded-xl max-w-[50px]">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>`;
            chatContent.appendChild(div);
            chatContent.scrollTop = chatContent.scrollHeight;
            return id;
        }
    </script>
</body>
</html>