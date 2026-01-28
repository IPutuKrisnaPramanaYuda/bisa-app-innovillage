<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat - BISA Assistant</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .prose p { margin-bottom: 0.5rem; }
        .prose ul { list-style-type: disc; padding-left: 1.5rem; }
        .prose strong { font-weight: 600; color: #1e293b; }
        
        /* Animasi Typing */
        .typing-dot {
            animation: typing 1.4s infinite ease-in-out both;
            height: 6px; width: 6px; background-color: #6b7280; border-radius: 50%; display: inline-block;
        }
        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        @keyframes typing { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
        
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>

@php $isChatEmpty = $currentChats->count() == 0; @endphp

<body class="bg-white flex h-screen overflow-hidden text-slate-800" 
      x-data="{ 
          sidebarOpen: window.innerWidth >= 768,
          chatEmpty: {{ $isChatEmpty ? 'true' : 'false' }} 
      }">

    <aside 
        x-show="sidebarOpen" 
        class="w-64 bg-[#0F244A] text-white flex flex-col h-screen flex-shrink-0 z-40 absolute md:relative shadow-xl"
    >
        <div class="p-6 flex items-center justify-between border-b border-white/10">
            <div class="flex items-center gap-3">
                <div class="p-1 rounded-lg">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-6 w-auto object-contain invert brightness-0 grayscale text-white">
                </div>
                <h1 class="text-lg font-bold tracking-wide">BISA AI</h1>
            </div>
            <button @click="sidebarOpen = false" class="md:hidden text-gray-300 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-4">
            <form action="{{ route('chat.reset') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center justify-center gap-2 w-full p-3 bg-blue-600 hover:bg-blue-500 rounded-xl transition text-sm font-bold shadow-lg shadow-blue-900/50">
                    <span>+</span> Chat Baru
                </button>
            </form>
        </div>

        <div class="px-4 pb-2 text-[10px] font-bold text-blue-300 uppercase tracking-wider">Riwayat Chat</div>
        <div class="flex-1 px-2 space-y-1 overflow-y-auto mb-4">
            @auth
                @foreach($history as $session)
                    <a href="{{ route('dashboard', ['s' => $session->session_id]) }}" 
                       class="block px-3 py-2 rounded-lg text-xs text-gray-300 hover:bg-white/10 transition truncate {{ ($currentSessionId ?? '') == $session->session_id ? 'bg-white/10 text-white font-bold' : '' }}">
                        {{ Str::limit($session->message, 30) }}
                    </a>
                @endforeach
            @else
                <p class="text-[10px] text-gray-400 px-4 italic">Login untuk melihat riwayat.</p>
            @endauth
        </div>

        <div class="p-4 border-t border-white/10 mt-auto bg-[#0a1a36]">
            <a href="{{ route('umkm.dashboard') }}" 
               class="flex items-center gap-3 w-full px-4 py-3 bg-white/5 hover:bg-white/10 text-gray-200 hover:text-white rounded-xl transition border border-white/5 group">
                <svg class="w-5 h-5 text-blue-400 group-hover:text-blue-300 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <div class="flex flex-col text-left">
                    <span class="font-bold text-xs leading-none">Kembali ke Toko</span>
                    <span class="text-[9px] text-gray-400 mt-0.5">Dashboard Utama</span>
                </div>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col relative w-full h-full bg-white transition-all duration-300">
        
        <div class="absolute top-0 left-0 w-full p-4 flex justify-between items-center z-30 bg-white/80 backdrop-blur-sm border-b border-gray-50 md:bg-transparent md:border-none">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
            </button>

            <div class="relative" x-data="{ open: false }">
                @auth
                    <button @click="open = !open" @click.outside="open = false" class="w-10 h-10 rounded-full bg-[#0F244A] text-white font-bold flex items-center justify-center hover:shadow-lg transition uppercase border-2 border-white">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </button>
                    
                    <div x-show="open" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden py-1 z-50">
                        <div class="px-4 py-2 border-b border-gray-50 bg-gray-50/50">
                            <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-gray-500 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"><span>⚙️</span> Setelan Akun</a>
                        <a href="{{ route('umkm.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"><span>🏪</span> Kelola Toko</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2 font-medium"><span>🚪</span> Keluar</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-[#0F244A] text-white rounded-lg text-sm font-bold hover:bg-blue-900 transition shadow-md">
                        Login / Daftar
                    </a>
                @endauth
            </div>
        </div>

        <div class="flex-1 flex flex-col w-full max-w-3xl mx-auto h-full relative"
             :class="chatEmpty ? 'justify-center items-center' : 'justify-between'">

            <div x-show="chatEmpty" class="text-center space-y-4 mb-8 fade-in px-4">
                <div class="w-20 h-20 bg-blue-900 border border-gray-100 rounded-2xl flex items-center justify-center mx-auto p-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain invert brightness-0 grayscale">
                </div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight">Apa yang bisa saya bantu?</h2>
                <p class="text-sm text-gray-500 max-w-xs mx-auto">Tanya stok toko, analisis keuangan, atau strategi penjualan.</p>
            </div>

            <div x-show="!chatEmpty" id="chat-container" class="w-full flex-1 overflow-y-auto p-4 pt-24 pb-4 scroll-smooth">
                <div class="space-y-8 pb-10" id="chat-list">
                    @foreach($currentChats as $chat)
                        <div class="flex justify-end">
                            <div class="bg-[#f0f4f9] px-5 py-3 rounded-2xl rounded-tr-sm max-w-[85%] text-gray-800 shadow-sm border border-gray-100">
                                {{ $chat->message }}
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-[#0F244A] flex items-center justify-center text-white text-xs shadow-md mt-1 flex-shrink-0">
                                <img src="{{ asset('images/logo.png') }}" class="w-5 h-5 object-contain invert brightness-0 grayscale">
                            </div>
                            <div class="prose max-w-none text-gray-800 leading-relaxed ai-content bg-white p-1" data-raw="{{ $chat->response }}"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="w-full px-4 pb-6 pt-2 bg-white z-20">
                <form id="chat-form" class="relative group bg-[#f0f4f9] rounded-3xl hover:shadow-md transition border-2 border-transparent focus-within:border-gray-200">
                    
                    <input type="hidden" name="mode" value="regular">
                    
                    <input type="text" id="message-input" required autocomplete="off"
                        class="w-full py-4 pl-6 pr-14 bg-transparent border-none focus:ring-0 text-gray-700 placeholder-gray-400"
                        placeholder="Ketik pesan untuk BISA...">
                    
                    <button type="submit" id="send-btn" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 bg-[#0F244A] rounded-full text-white shadow-sm hover:scale-105 transition disabled:opacity-50">
                        <svg class="w-5 h-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                    </button>
                </form>
                <div class="flex justify-center mt-3">
                    <p class="text-[9px] text-gray-400 text-center">BISA Assistant dapat membuat kesalahan. Cek informasi penting.</p>
                </div>
            </div>

        </div>
    </main>

    <script>
    const chatList = document.getElementById('chat-list');
    const chatContainer = document.getElementById('chat-container');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('message-input');
    const sendBtn = document.getElementById('send-btn');

    // 1. Render Markdown
    document.querySelectorAll('.ai-content').forEach(el => {
        el.innerHTML = marked.parse(el.getAttribute('data-raw'));
    });
    
    // 2. Scroll to Bottom
    function scrollToBottom(behavior = 'smooth') {
        if (!chatContainer) return;
        setTimeout(() => {
            chatContainer.scrollTo({ top: chatContainer.scrollHeight, behavior: behavior });
        }, 100);
    }
    
    // Cek Alpine data dengan aman
    if (typeof Alpine !== 'undefined' && !Alpine.$data(document.body).chatEmpty) {
        scrollToBottom('auto');
    }

    // 3. Submit Chat
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const message = input.value.trim();
        const mode = 'regular'; // Default selalu regular/ERP
        
        if (!message) return;

        // UI Feedback
        const alpineData = typeof Alpine !== 'undefined' ? Alpine.$data(document.body) : null;
        if (alpineData && alpineData.chatEmpty) {
            alpineData.chatEmpty = false; 
        }

        input.value = '';
        input.disabled = true;
        sendBtn.disabled = true;

        appendUserBubble(message);
        const loadingId = appendLoadingBubble();
        scrollToBottom();

        try {
            const response = await fetch("{{ route('chat.send.public') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ 
                    message: message,
                    mode: mode 
                })
            });

            const data = await response.json();
            
            const loadingEl = document.getElementById(loadingId);
            if (loadingEl) loadingEl.remove();

            if (data.success) {
                appendAIBubble(data.ai_response);
            } else {
                appendAIBubble("Maaf Bos, ada kendala: " + (data.error || "Gagal memproses."));
            }

        } catch (error) {
            const loadingEl = document.getElementById(loadingId);
            if (loadingEl) loadingEl.remove();
            appendAIBubble("Maaf Bos, terjadi kesalahan koneksi.");
            console.error(error);
        }

        input.disabled = false;
        sendBtn.disabled = false;
        input.focus();
        scrollToBottom();
    });

    // --- Helper Functions untuk Bubble Chat ---
    function appendUserBubble(text) {
        const div = document.createElement('div');
        div.className = 'flex justify-end fade-in';
        div.innerHTML = `<div class="bg-[#f0f4f9] px-5 py-3 rounded-2xl rounded-tr-sm max-w-[85%] text-gray-800 shadow-sm border border-gray-100">${text}</div>`;
        chatList.appendChild(div);
    }

    function appendLoadingBubble() {
        const id = 'loading-' + Date.now();
        const div = document.createElement('div');
        div.id = id;
        div.className = 'flex gap-4 fade-in';
        div.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-[#0F244A] flex items-center justify-center text-white text-xs shadow-md mt-1 flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" class="w-5 h-5 object-contain invert brightness-0 grayscale">
            </div>
            <div class="bg-white px-4 py-3 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-1">
                <span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span>
            </div>`;
        chatList.appendChild(div);
        return id;
    }

    function appendAIBubble(text) {
        const div = document.createElement('div');
        div.className = 'flex gap-4 fade-in';
        div.innerHTML = `
            <div class="w-8 h-8 rounded-full bg-[#0F244A] flex items-center justify-center text-white text-xs shadow-md mt-1 flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" class="w-5 h-5 object-contain invert brightness-0 grayscale">
            </div>
            <div class="prose max-w-none text-gray-800 leading-relaxed bg-white p-1">${marked.parse(text)}</div>`;
        chatList.appendChild(div);
    }
    </script>

</body>
</html>