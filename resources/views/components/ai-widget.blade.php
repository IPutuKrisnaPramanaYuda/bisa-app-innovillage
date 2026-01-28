<div x-data="{ open: false }" class="fixed bottom-6 right-6 z-[9999]">
    
    <button @click="open = !open" 
            class="w-16 h-16 bg-blue-900 rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition-transform active:scale-95 border-4 border-white group relative overflow-hidden">
        <img x-show="!open" 
             src="{{ asset('images/logo.png') }}" 
             class="w-8 h-8 object-contain invert brightness-0 grayscale text-white group-hover:scale-110 transition">
        
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
         class="absolute bottom-20 right-0 w-[350px] md:w-[380px] h-[500px] bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden ring-1 ring-slate-900/5"
         style="display: none;">
        
        <div class="bg-blue-600 p-4 text-white flex justify-between items-center shadow-md relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8 blur-lg"></div>

            <div class="flex items-center gap-3 relative z-10">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20">
                    <img src="{{ asset('images/logo.png') }}" class="w-6 h-6 object-contain invert brightness-0 grayscale">
                </div>
                <div>
                    <p class="font-bold text-sm leading-tight">Asisten Bengkala</p>
                    <div class="flex items-center gap-1 mt-0.5">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                        <p class="text-[10px] text-blue-100">Online 24 Jam</p>
                    </div>
                </div>
            </div>
            <button @click="open = false" class="text-white/80 hover:text-white transition relative z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div id="widget-chat-box" class="flex-1 p-4 overflow-y-auto bg-slate-50 space-y-4 text-sm scroll-smooth">
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex-shrink-0 flex items-center justify-center shadow-md border border-white">
                    <img src="{{ asset('images/logo.png') }}" class="w-5 h-5 object-contain invert brightness-0 grayscale">
                </div>
                <div class="bg-white p-3.5 rounded-2xl rounded-tl-none shadow-sm border border-slate-100 text-slate-700 leading-relaxed">
                    Halo! 👋 Saya BISA.<br>Ada yang bisa saya bantu jelaskan tentang Desa Bengkala atau produk UMKM?
                </div>
            </div>
        </div>

        <div class="p-3 bg-white border-t border-slate-100">
            <form id="widget-chat-form" class="relative flex gap-2">
                <input type="text" id="widget-chat-input" placeholder="Tanya sesuatu..." autocomplete="off"
                       class="flex-1 pl-4 pr-12 py-3 bg-slate-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-blue-600 focus:bg-white transition placeholder:text-slate-400">
                
                <button type="submit" class="absolute right-2 top-1.5 bottom-1.5 w-9 h-9 bg-blue-600 text-white rounded-lg flex items-center justify-center hover:bg-blue-700 transition shadow-md flex-shrink-0">
                    <svg class="w-4 h-4 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
            <div class="text-center mt-2">
                <p class="text-[9px] text-slate-400">Powered by BISA AI</p>
            </div>
        </div>
    </div>
</div>

<script>
    // URL Logo untuk JS
    const logoUrlPublic = "{{ asset('images/logo.png') }}";

    // --- 1. FUNGSI FORMATTER (Agar Teks Rapi) ---
    function formatMessage(text) {
        if (!text) return "";

        // Ubah **teks** menjadi Bold
        let formatted = text.replace(/\*\*(.*?)\*\*/g, '<b class="font-bold text-slate-900">$1</b>');

        // Ubah * (bullet point) menjadi •
        formatted = formatted.replace(/^\*\s/gm, '• ');

        // Ubah Enter menjadi <br>
        formatted = formatted.replace(/\n/g, '<br>');

        return formatted;
    }

    document.getElementById('widget-chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let input = document.getElementById('widget-chat-input');
        let message = input.value;
        let chatBox = document.getElementById('widget-chat-box');

        if(message.trim() === '') return;

        // Tampilkan Pesan User
        chatBox.innerHTML += `
            <div class="flex justify-end gap-3 animate-fade-in-up">
                <div class="bg-blue-600 text-white p-3.5 rounded-2xl rounded-tr-none shadow-md max-w-[85%] leading-relaxed">
                    ${message}
                </div>
            </div>
        `;
        input.value = '';
        chatBox.scrollTop = chatBox.scrollHeight;

        // Tampilkan Loading
        let loadingId = 'loading-' + Date.now();
        chatBox.innerHTML += `
            <div id="${loadingId}" class="flex gap-3 animate-pulse">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex-shrink-0 flex items-center justify-center shadow-md border border-white">
                    <img src="${logoUrlPublic}" class="w-5 h-5 object-contain invert brightness-0 grayscale">
                </div>
                <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-100 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                    <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                </div>
            </div>
        `;
        chatBox.scrollTop = chatBox.scrollHeight;

        // Kirim ke Server
        fetch("{{ route('chat.send.public') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ message: message, mode: 'regular' })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById(loadingId).remove();
            
            let rawReply = data.success ? data.ai_response : "Maaf, koneksi sedang sibuk.";
            
            // --- 2. TERAPKAN FORMATTER DISINI ---
            let cleanReply = formatMessage(rawReply);
            
            chatBox.innerHTML += `
                <div class="flex gap-3 animate-fade-in-up">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex-shrink-0 flex items-center justify-center shadow-md border border-white">
                         <img src="${logoUrlPublic}" class="w-5 h-5 object-contain invert brightness-0 grayscale">
                    </div>
                    <div class="bg-white p-3.5 rounded-2xl rounded-tl-none shadow-sm border border-slate-100 text-slate-700 max-w-[90%] text-sm leading-relaxed">
                        ${cleanReply}
                    </div>
                </div>
            `;
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(error => {
            if(document.getElementById(loadingId)) document.getElementById(loadingId).remove();
            console.error(error);
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