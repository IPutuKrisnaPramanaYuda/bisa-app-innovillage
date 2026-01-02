<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden" x-data="{ sidebarOpen: window.innerWidth >= 768 }">

    @include('umkm.sidebar')

    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-[#F5F7FA]">
        
        @include('umkm.header', ['title' => 'Dashboard'])

        <div class="p-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Pendapatan Bulan Ini</p>
                            <h3 class="text-2xl font-bold text-gray-800">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h3>
                            
                            @if($pendapatanBulanIni > 0)
                                @if($persentaseKenaikan >= 0)
                                    <p class="text-xs text-green-500 mt-2 font-semibold flex items-center gap-1">
                                        <span class="bg-green-100 px-1.5 py-0.5 rounded">▲ {{ round($persentaseKenaikan, 1) }}%</span> bulan ini
                                    </p>
                                @else
                                    <p class="text-xs text-red-500 mt-2 font-semibold flex items-center gap-1">
                                        <span class="bg-red-100 px-1.5 py-0.5 rounded">▼ {{ round(abs($persentaseKenaikan), 1) }}%</span> bulan ini
                                    </p>
                                @endif
                            @else
                                <p class="text-xs text-gray-400 mt-2">Belum ada transaksi</p>
                            @endif
                        </div>
                        <div class="p-2 bg-gray-50 rounded-lg">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Keuntungan Bersih</p>
                            <h3 class="text-2xl font-bold text-[#0F244A]">Rp {{ number_format($labaBulanIni, 0, ',', '.') }}</h3>
                            <p class="text-xs text-gray-400 mt-2">
                                (Omset - Modal HPP) bulan ini
                            </p>
                        </div>
                        <div class="p-2 bg-green-50 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-start">
                         <div>
                            <p class="text-sm text-gray-500 font-medium mb-1">Stok Menipis</p>
                            <h3 class="text-3xl font-bold text-gray-800">{{ $stokMenipis }} <span class="text-sm font-normal text-gray-400">Item</span></h3>
                         </div>
                         @if($stokMenipis > 0)
                            <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wider">Perlu Restok</span>
                         @else
                            <span class="bg-green-100 text-green-600 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wider">Aman</span>
                         @endif
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-sm text-gray-500 font-medium mb-1">Total Item Terjual</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $totalItemTerjual ?? 0 }} <span class="text-sm font-normal text-gray-400">pcs</span></h3>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            Tren Penjualan
                        </h3>
                        
                        <form method="GET" action="{{ route('umkm.dashboard') }}" class="flex gap-2">
                            <select name="periode" onchange="this.form.submit()" class="bg-gray-50 border border-gray-200 text-xs rounded-lg px-3 py-2 font-medium text-gray-600 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                <option value="harian" {{ $periode == 'harian' ? 'selected' : '' }}>Harian</option>
                                <option value="mingguan" {{ $periode == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                                <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                <option value="tahunan" {{ $periode == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                            </select>
                        </form>
                    </div>
                    <div class="relative h-64 w-full">
                        @if(array_sum($grafikData) > 0)
                            <canvas id="salesChart"></canvas>
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400 text-sm">
                                Belum ada data penjualan pada periode ini.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-lg text-gray-800 mb-6">Pesanan Baru</h3>
                    
                    <div class="space-y-4">
                        @forelse($pesananBaru as $order)
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition cursor-pointer border border-transparent hover:border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs uppercase">
                                    {{ substr($order->buyer->name ?? 'G', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 truncate max-w-[120px]">{{ $order->buyer->name ?? 'Pembeli Umum' }}</p>
                                    <p class="text-xs text-gray-500 truncate max-w-[120px]">{{ $order->product->name ?? 'Produk dihapus' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-800">Rp {{ number_format($order->amount, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-gray-400">{{ $order->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-10 opacity-50 flex flex-col items-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">📦</div>
                            <p class="text-sm text-gray-400">Belum ada pesanan masuk.</p>
                        </div>
                        @endforelse
                    </div>

                    @if($pesananBaru->count() > 0)
                    <a href="{{ route('umkm.sales') }}" class="block w-full mt-6 py-2 text-center text-sm text-blue-600 font-medium border border-blue-200 rounded-xl hover:bg-blue-50 transition">
                        Lihat Semua Pesanan
                    </a>
                    @endif
                </div>

            </div>

        </div>
    </main>

    <script>
        // Grafik Batang
        const salesChartEl = document.getElementById('salesChart');
        if (salesChartEl) {
            const ctx = salesChartEl.getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($grafikLabel) !!}, 
                    datasets: [{
                        label: 'Penjualan (Rp)',
                        data: {!! json_encode($grafikData) !!}, 
                        backgroundColor: '#1E40AF', 
                        borderRadius: 6,
                        barThickness: 'flex',
                        maxBarThickness: 35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.raw.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { borderDash: [2, 2] },
                            ticks: {
                                callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    </script>
</body>
</html>