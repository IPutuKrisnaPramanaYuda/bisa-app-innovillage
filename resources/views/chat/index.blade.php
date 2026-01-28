<x-app-layout>
    <x-slot name="header">
        Evaluasi dan Laporan Keuangan
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="flex flex-col lg:flex-row min-h-screen bg-white">
        
        <div class="w-full lg:w-9/12 p-6 space-y-6 bg-gray-50 h-screen overflow-y-auto">
            <div class="lg:hidden flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-gray-800">Evaluasi & Keuangan</h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-900 text-white rounded-2xl p-5 shadow-lg relative overflow-hidden">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="text-lg font-semibold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                Keuntungan
                            </h3>
                            <p class="text-gray-400 text-xs">Total penjualan: <span class="bg-gray-700 px-2 rounded text-white">Total</span></p>
                            <h2 class="text-3xl font-bold mt-2">{{ $totalItemTerjual }} Botol</h2> </div>
                    </div>
                    <div class="h-32 mt-4 bg-white rounded-xl p-2">
                        <canvas id="profitChart"></canvas>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 005.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Pelanggan
                    </h3>
                    <div class="flex items-center gap-4">
                        <div class="text-center">
                            <h4 class="text-3xl font-bold text-green-600">+{{ $pelangganBaru }}</h4>
                            <p class="text-xs text-gray-500">Bulan Ini</p>
                        </div>
                        <div class="flex-1 space-y-2">
                            <div class="flex justify-between text-xs bg-gray-100 p-2 rounded">
                                <span>Toko</span> <span>{{ $totalPelanggan }} User</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-xs text-gray-500 font-bold">Total: {{ $totalPelanggan }} Pelanggan Terdaftar</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2 bg-gray-900 rounded-2xl p-5 text-white">
                     <h3 class="text-lg font-semibold flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        Pendapatan
                        <span class="text-xs bg-gray-700 px-2 py-0.5 rounded ml-auto">Akumulasi</span>
                    </h3>
                    <div class="space-y-3">
                        <div class="bg-[#8B5E3C] p-4 rounded-xl flex flex-col justify-center">
                            <p class="text-xs text-yellow-100 mb-1">Total Omset (Laba Kotor)</p>
                            <h2 class="text-2xl font-bold">Rp {{ number_format($labaKotor, 0, ',', '.') }}</h2>
                        </div>
                        <div class="bg-[#4CAF50] p-4 rounded-xl flex flex-col justify-center">
                            <p class="text-xs text-green-100 mb-1">Profit (Laba Bersih)</p>
                            <h2 class="text-2xl font-bold">Rp {{ number_format($labaBersih, 0, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-[#1E293B] text-white p-5 rounded-2xl">
                         <h3 class="text-sm font-semibold flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Total Penjualan
                         </h3>
                         <h2 class="text-2xl font-bold">{{ $totalItemTerjual }} <span class="text-sm font-normal">Pcs</span></h2>
                    </div>
                    <div class="bg-[#334155] text-white p-5 rounded-2xl">
                         <h3 class="text-sm font-semibold flex items-center gap-2 mb-2">
                            📉 Modal Terpakai
                         </h3>
                         <div class="flex items-center gap-3">
                             <span class="text-xl font-bold">Rp {{ number_format($totalModal, 0, ',', '.') }}</span>
                         </div>
                    </div>
                </div>
            </div>

            <div class="bg-[#1E293B] rounded-2xl p-6 text-white">
                <h3 class="text-lg font-semibold flex items-center gap-2 mb-4">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                     Uang Kas (Real-time)
                </h3>
                <div class="bg-[#4CAF50] p-6 rounded-xl flex flex-col justify-between items-center text-center">
                     <div>
                         <p class="text-sm text-green-100 font-bold mb-2">SISA KAS TUNAI</p>
                         <h2 class="text-4xl font-bold mt-1">Rp {{ number_format(Auth::user()->umkm->balance ?? 0, 0, ',', '.') }}</h2>
                         <p class="text-xs text-white/70 mt-2">Otomatis bertambah saat jual & berkurang saat belanja</p>
                     </div>
                </div>
            </div>
            
        </div> 
        
        <div class="w-full lg:w-3/12 bg-[#0F172A] border-l border-gray-700 flex flex-col h-screen relative">
            
            <div class="p-4 border-b border-gray-700 bg-[#0F172A] z-10 shadow-md">
                <h2 class="text-lg font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Growth AI Assistant
                </h2>
                <div class="flex justify-between items-center mt-1">
                    <p class="text-xs text-gray-400">Micro ERP Expert & CFO</p>
                    
                    <span id="research-badge" class="hidden text-[10px] bg-blue-900 text-blue-200 px-2 py-0.5 rounded-full border border-blue-500 animate-pulse">
                        🎓 Research Mode ON
                    </span>
                </div>
            </div>

            <div id="chat-container" class="flex-1 overflow-y-auto p-4 space-y-4">
                
                @if($currentChats->isEmpty())
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 text-white font-bold text-xs">AI</div>
                    <div class="bg-gray-800 text-gray-200 p-3 rounded-tr-xl rounded-bl-xl rounded-br-xl text-sm shadow-md border border-gray-700">
                        Halo Bos! 👋 Saya Growth AI. <br><br>
                        Pilih mode di bawah:
                        <ul class="list-disc pl-4 mt-2 space-y-1">
                            <li>🏪 <strong>Mode Toko:</strong> Urus stok & keuangan.</li>
                            <li>🎓 <strong>Mode Research:</strong> Tanya jawab Jurnal/PDF.</li>
                        </ul>
                    </div>
                </div>
                @endif

                @foreach($currentChats as $chat)
                    <div class="flex gap-3 justify-end">
                        <div class="bg-blue-600 text-white p-3 rounded-tl-xl rounded-bl-xl rounded-br-xl text-sm shadow-md max-w-[85%]">
                            {{ $chat->message }}
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 text-white font-bold text-xs">AI</div>
                        <div class="bg-gray-800 text-gray-200 p-3 rounded-tr-xl rounded-bl-xl rounded-br-xl text-sm shadow-md border border-gray-700 max-w-[90%] whitespace-pre-wrap leading-relaxed">{{ $chat->response }}</div>
                    </div>
                @endforeach
                
                <div id="loading-indicator" class="hidden flex gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 text-white font-bold text-xs">AI</div>
                    <div class="bg-gray-800 text-gray-400 p-3 rounded-tr-xl rounded-bl-xl rounded-br-xl text-xs border border-gray-700 italic">
                        Sedang berpikir...
                    </div>
                </div>

            </div>
            
            <div class="p-4 bg-[#0F172A] border-t border-gray-700">
                <form id="chat-form" class="relative flex items-center gap-2">
                    @csrf
                    
                    <input type="hidden" id="chat-mode" name="mode" value="regular">

                    <button type="button" onclick="toggleMode()" id="btn-toggle" 
                        class="p-2.5 rounded-full bg-gray-700 hover:bg-gray-600 text-gray-300 transition-all border border-gray-600 group relative"
                        title="Ganti Mode (Toko / Research)">
                        
                        <svg id="icon-store" class="w-5 h-5 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        
                        <svg id="icon-research" class="w-5 h-5 hidden text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                    </button>

                    <div class="relative w-full">
                        <input type="text" id="message-input" name="message" placeholder="Tanya stok / keuangan..." 
                               class="w-full bg-gray-800 text-white rounded-full py-3 px-5 pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 border border-gray-600 shadow-lg text-sm placeholder-gray-500 transition-all" autocomplete="off">
                        
                        <button type="submit" class="absolute right-2 top-1.5 p-1.5 bg-blue-600 hover:bg-blue-700 rounded-full text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-2 flex justify-between items-center px-2">
                     <span id="mode-text" class="text-[10px] text-gray-500">Mode: Toko (Regular)</span>
                     <form action="{{ route('chat.reset') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-[10px] text-gray-500 hover:text-gray-300 underline">Reset Chat</button>
                    </form>
                </div>

            </div>

        </div> 
    </div>

    <script>
        const ctx = document.getElementById('profitChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb', 'Mg'],
                datasets: [{
                    label: 'Penjualan',
                    data: [12, 19, 3, 5, 2, 3, 15], 
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { display: false }, y: { display: false } },
                elements: { point: { radius: 0 } }
            }
        });
    </script>

    <script>
        // FUNGSI GANTI MODE (Regular <-> Research)
        function toggleMode() {
            const modeInput = document.getElementById('chat-mode');
            const iconStore = document.getElementById('icon-store');
            const iconResearch = document.getElementById('icon-research');
            const messageInput = document.getElementById('message-input');
            const badge = document.getElementById('research-badge');
            const modeText = document.getElementById('mode-text');
            const btn = document.getElementById('btn-toggle');

            if (modeInput.value === 'regular') {
                // AKTIFKAN MODE RESEARCH
                modeInput.value = 'research';
                
                // Ubah Visual
                iconStore.classList.add('hidden');
                iconResearch.classList.remove('hidden');
                badge.classList.remove('hidden');
                
                // Ubah Style Input biar kerasa bedanya
                messageInput.placeholder = "Tanya tentang Jurnal / Teori...";
                messageInput.classList.add('border-blue-500', 'bg-blue-900/20');
                btn.classList.add('bg-blue-900/30', 'border-blue-500');
                
                modeText.innerText = "Mode: Research (Jurnal)";
            } else {
                // BALIK KE MODE REGULAR (TOKO)
                modeInput.value = 'regular';
                
                // Ubah Visual
                iconStore.classList.remove('hidden');
                iconResearch.classList.add('hidden');
                badge.classList.add('hidden');
                
                // Balikin Style Input
                messageInput.placeholder = "Tanya stok / keuangan...";
                messageInput.classList.remove('border-blue-500', 'bg-blue-900/20');
                btn.classList.remove('bg-blue-900/30', 'border-blue-500');
                
                modeText.innerText = "Mode: Toko (Regular)";
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const chatForm = document.getElementById('chat-form');
            const messageInput = document.getElementById('message-input');
            const chatContainer = document.getElementById('chat-container');
            const loadingIndicator = document.getElementById('loading-indicator');
            const modeInput = document.getElementById('chat-mode'); // Ambil input mode

            function scrollToBottom() {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
            scrollToBottom(); 

            chatForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const message = messageInput.value.trim();
                const mode = modeInput.value; // AMBIL NILAI MODE SAAT INI

                if (!message) return;

                // Tampilkan Pesan User
                const userBubble = `
                    <div class="flex gap-3 justify-end">
                        <div class="bg-blue-600 text-white p-3 rounded-tl-xl rounded-bl-xl rounded-br-xl text-sm shadow-md max-w-[85%]">
                            ${message}
                        </div>
                    </div>
                `;
                loadingIndicator.insertAdjacentHTML('beforebegin', userBubble);
                
                messageInput.value = '';
                loadingIndicator.classList.remove('hidden');
                scrollToBottom();

                // KIRIM KE SERVER (SERTAKAN MODE)
                fetch("{{ route('chat.send') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ 
                        message: message,
                        mode: mode  // <--- INI PENTING: KITA KIRIM DATA MODENYA
                    })
                })
                .then(response => response.json())
                .then(data => {
                    loadingIndicator.classList.add('hidden');

                    if(data.success) {
                        const aiBubble = `
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0 text-white font-bold text-xs">AI</div>
                                <div class="bg-gray-800 text-gray-200 p-3 rounded-tr-xl rounded-bl-xl rounded-br-xl text-sm shadow-md border border-gray-700 max-w-[90%] whitespace-pre-wrap leading-relaxed">${data.ai_response}</div>
                            </div>
                        `;
                        loadingIndicator.insertAdjacentHTML('beforebegin', aiBubble);
                        scrollToBottom();
                    } else {
                        alert("Error: " + (data.error || "Gagal mengirim pesan"));
                    }
                })
                .catch(error => {
                    loadingIndicator.classList.add('hidden');
                    alert("Terjadi kesalahan koneksi.");
                    console.error(error);
                });
            });
        });
    </script>
</x-app-layout>