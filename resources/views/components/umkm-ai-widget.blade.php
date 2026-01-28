<div x-data="{ open: false }" class="fixed bottom-6 right-6 z-[9999]">
    
    <button @click="open = !open" 
            class="w-16 h-16 bg-[#0F244A] rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition-transform active:scale-95 border-4 border-white/20 group relative overflow-hidden">
        
        <div class="absolute inset-0 bg-gradient-to-tr from-blue-600/20 to-transparent"></div>

        <img x-show="!open" 
             src="{{ asset('images/logo.png') }}" 
             alt="BISA AI" 
             class="w-8 h-8 object-contain group-hover:rotate-12 transition duration-300 filter brightness-0 invert"> 
             <svg x-show="open" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
        </svg>

        
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-10 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-10 scale-95"
         class="absolute bottom-20 right-0 w-[350px] h-[500px] bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden ring-1 ring-slate-900/5"
         style="display: none;">
        
        <div class="bg-[#0F244A] p-4 flex justify-between items-center text-white shadow-md relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-white/5 rounded-full -mr-10 -mt-10 blur-xl"></div>
            
            <div class="flex items-center gap-3 relative z-10">
                <div class="relative">
                    <div class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center border border-white/20 backdrop-blur-sm">
                        <img src="{{ asset('images/logo.png') }}" class="w-6 h-6 object-contain filter brightness-0 invert">
                    </div>
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-[#0F244A] rounded-full animate-pulse"></div>
                </div>
                <div>
                    <h3 class="font-bold text-sm tracking-wide">BISA Assistant</h3>
                    <p class="text-[10px] text-blue-200">Micro ERP Support</p>
                </div>
            </div>
            
            <div class="flex items-center gap-1 relative z-10">
                <a href="{{ route('dashboard') }}" class="p-1.5 hover:bg-white/10 rounded-lg text-blue-100 hover:text-white transition" title="Dashboard Full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                </a>
                <button @click="open = false" class="p-1.5 hover:bg-white/10 rounded-lg text-blue-100 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <div id="mini-chat-box" class="flex-1 p-4 overflow-y-auto bg-slate-50 space-y-4 text-sm scroll-smooth">
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-full bg-white border border-slate-200 flex-shrink-0 flex items-center justify-center p-1 shadow-sm">
                    <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-contain">
                </div>
                <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-100 text-slate-700 text-sm">
                    Halo Bos! 👋 Ada yang bisa saya bantu untuk cek stok, laporan, atau input data hari ini?
                </div>
            </div>
        </div>

        <div class="p-3 bg-white border-t border-slate-100">
            <form id="mini-chat-form" class="relative flex gap-2 items-center">
                @csrf
                <input type="text" id="mini-chat-input" placeholder="Tanya BISA..." 
                       class="w-full pl-4 pr-10 py-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-[#0F244A] focus:bg-white transition placeholder:text-slate-400">
                
                <button type="submit" class="absolute right-2 top-2 bottom-2 aspect-square bg-[#0F244A] text-white rounded-lg hover:bg-blue-900 transition shadow-md flex items-center justify-center">
                    <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const logoUrl = "{{ asset('images/logo.png') }}";

    // --- FUNGSI FORMATTER (PENERJEMAH GAYA) ---
    function formatMessage(text) {
        if (!text) return "";

        // 1. Ubah **teks** menjadi <b>teks</b> (Bold)
        let formatted = text.replace(/\*\*(.*?)\*\*/g, '<b class="font-bold text-slate-800">$1</b>');

        // 2. Ubah * (bullet point) di awal baris menjadi •
        formatted = formatted.replace(/^\*\s/gm, '• ');

        // 3. Ubah Baris Baru (\n) menjadi <br>
        formatted = formatted.replace(/\n/g, '<br>');

        return formatted;
    }

    document.getElementById('mini-chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let input = document.getElementById('mini-chat-input');
        let message = input.value;
        let chatBox = document.getElementById('mini-chat-box');

        if(message.trim() === '') return;

        // Tampilkan Pesan User
        chatBox.innerHTML += `
            <div class="flex justify-end gap-2 animate-fade-in-up mb-4">
                <div class="bg-[#0F244A] text-white p-3 rounded-2xl rounded-tr-none shadow-md text-sm max-w-[85%]">
                    ${message}
                </div>
            </div>
        `;
        input.value = '';
        chatBox.scrollTop = chatBox.scrollHeight;

        // Tampilkan Loading
        let loadingId = 'loading-' + Date.now();
        chatBox.innerHTML += `
            <div id="${loadingId}" class="flex gap-3 animate-pulse mb-4">
                <div class="w-8 h-8 rounded-full bg-white border border-slate-200 flex-shrink-0 flex items-center justify-center p-1 shadow-sm">
                    <img src="${logoUrl}" class="w-full h-full object-contain">
                </div>
                <div class="bg-gray-100 p-3 rounded-2xl rounded-tl-none text-gray-500 text-xs flex items-center">
                    Sedang memproses...
                </div>
            </div>
        `;
        chatBox.scrollTop = chatBox.scrollHeight;

        // Kirim ke Server
        fetch("{{ route('chat.send') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById(loadingId).remove();

            let rawReply = data.ai_response || "Maaf, tidak ada balasan.";
            
            // 🔥 TERAPKAN FORMATTER DISINI 🔥
            let cleanReply = formatMessage(rawReply);
            
            chatBox.innerHTML += `
                <div class="flex gap-3 animate-fade-in-up mb-4">
                    <div class="w-8 h-8 rounded-full bg-white border border-slate-200 flex-shrink-0 flex items-center justify-center p-1 shadow-sm">
                         <img src="${logoUrl}" class="w-full h-full object-contain">
                    </div>
                    <div class="bg-white p-3.5 rounded-2xl rounded-tl-none shadow-sm border border-slate-100 text-slate-600 text-sm max-w-[90%] leading-relaxed">
                        ${cleanReply}
                    </div>
                </div>
            `;
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(error => {
            if(document.getElementById(loadingId)) document.getElementById(loadingId).remove();
            chatBox.innerHTML += `<div class="text-center text-xs text-red-500 mt-2">Gagal terhubung.</div>`;
        });
    });
</script>

<style>
    @keyframes fade-in-up {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fade-in-up 0.3s ease-out forwards;
    }
</style>