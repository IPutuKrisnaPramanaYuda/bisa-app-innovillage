<x-app-layout>
    <x-slot name="header">
        Evaluasi & Laporan Keuangan
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="flex flex-col lg:flex-row min-h-screen bg-white">
        
        <div class="w-full lg:w-9/12 p-6 space-y-6">
            
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
                            <p class="text-gray-400 text-xs">Total penjualan: <span class="bg-gray-700 px-2 rounded text-white">Hari Ini</span></p>
                            <h2 class="text-3xl font-bold mt-2">{{ $totalItemTerjual }} Botol</h2> 
                        </div>
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
                            <p class="text-xs text-gray-500">Pelanggan Baru</p>
                        </div>
                        <div class="flex-1 space-y-2">
                            <div class="flex justify-between text-xs bg-gray-100 p-2 rounded">
                                <span>Toko</span> <span>{{ $totalPelanggan > 0 ? $totalPelanggan - 5 : 0 }} Pesanan</span>
                            </div>
                            <div class="flex justify-between text-xs bg-gray-100 p-2 rounded">
                                <span>Marketplace</span> <span>5 Pesanan</span>
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
                        <span class="text-xs bg-gray-700 px-2 py-0.5 rounded ml-auto">Minggu ini</span>
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="bg-[#8B5E3C] p-4 rounded-xl flex flex-col justify-center">
                            <p class="text-xs text-yellow-100 mb-1">Laba kotor</p>
                            <h2 class="text-2xl font-bold">Rp {{ number_format($labaKotor, 0, ',', '.') }}</h2>
                        </div>
                        <div class="bg-[#4CAF50] p-4 rounded-xl flex flex-col justify-center">
                            <p class="text-xs text-green-100 mb-1">Laba bersih</p>
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
                         <h2 class="text-2xl font-bold">{{ $totalItemTerjual }} <span class="text-sm font-normal">Botol</span></h2>
                    </div>
                    <div class="bg-[#334155] text-white p-5 rounded-2xl">
                         <h3 class="text-sm font-semibold flex items-center gap-2 mb-2">
                            📉 Kerugian
                         </h3>
                         <div class="flex items-center gap-3">
                             <span class="text-2xl font-bold">0%</span>
                             <span class="text-xs bg-gray-600 px-2 py-1 rounded">Dari Modal</span>
                         </div>
                    </div>
                </div>
            </div>

            <div class="bg-[#1E293B] rounded-2xl p-6 text-white">
                <h3 class="text-lg font-semibold flex items-center gap-2 mb-4">
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                     Uang Kas
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-3">
                        <div class="bg-[#A53F3F] p-4 rounded-xl">
                             <p class="text-xs text-red-100">Kas Kotor</p>
                             <h2 class="text-2xl font-bold">Rp {{ number_format($labaKotor * 5, 0, ',', '.') }}</h2>
                        </div>
                        <div class="bg-[#795548] p-4 rounded-xl">
                             <p class="text-xs text-orange-100">Kas Modal</p>
                             <h2 class="text-2xl font-bold">Rp {{ number_format($totalModal, 0, ',', '.') }}</h2>
                        </div>
                    </div>
                    <div class="bg-[#4CAF50] p-4 rounded-xl flex flex-col justify-between">
                         <div>
                             <p class="text-sm text-green-100 font-bold">Total Kas Bersih</p>
                             <h2 class="text-3xl font-bold mt-1">Rp {{ number_format($labaBersih + ($labaKotor * 3), 0, ',', '.') }}</h2>
                         </div>
                         <div class="flex gap-2 mt-4">
                             <div class="h-12 w-full bg-green-700/50 rounded-lg"></div>
                             <div class="h-12 w-full bg-green-700/50 rounded-lg"></div>
                         </div>
                    </div>
                </div>
            </div>

        </div> <div class="w-full lg:w-3/12 bg-[#0F172A] p-6 text-white flex flex-col justify-between min-h-screen lg:fixed lg:right-0 lg:h-full lg:overflow-y-auto border-l border-gray-700">
            
            <div>
                <h2 class="text-xl font-bold mb-6">Rekomendasi AI</h2>
                
                <div class="space-y-4">
                    @foreach($aiRecommendations as $rec)
                    <div class="bg-white text-gray-800 p-4 rounded-tl-2xl rounded-tr-2xl rounded-br-2xl shadow-md text-sm leading-relaxed">
                        {{ $rec }}
                    </div>
                    @endforeach
                    
                    @if(empty($aiRecommendations))
                    <div class="bg-white text-gray-800 p-4 rounded-tl-2xl rounded-tr-2xl rounded-br-2xl shadow-md text-sm leading-relaxed">
                        Lakukan pengecekan inventori agar tidak terjadi kekurangan stok di minggu berikutnya.
                    </div>
                    @endif
                </div>
                
                <hr class="border-gray-600 my-6 w-1/2 mx-auto">
            </div>

            <div class="mt-4">
                <form action="{{ route('chat.send') }}" method="POST" class="relative">
                    @csrf
                    <div class="absolute -left-10 bottom-2 text-white">
                         <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path></svg>
                    </div>

                    <input type="text" name="message" placeholder="Minta rekomendasi AI BISA" 
                           class="w-full bg-white text-gray-800 rounded-full py-3 px-5 pr-12 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-lg text-sm">
                    
                    <button type="submit" class="absolute right-2 top-1.5 text-blue-600 hover:text-blue-800 p-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
                    </button>
                </form>
            </div>
        </div> </div>

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
</x-app-layout>