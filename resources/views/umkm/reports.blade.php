<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden" x-data="{ sidebarOpen: window.innerWidth >= 768 }">
    @include('umkm.sidebar') 

    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-[#F5F7FA]">
        @include('umkm.header', ['title' => 'Evaluasi & Laporan'])

        <div class="p-8 space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <div class="bg-[#0F244A] rounded-2xl p-6 text-white shadow-lg relative overflow-hidden group hover:scale-105 transition duration-300">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-full -mr-10 -mt-10"></div>
                    <h3 class="text-lg font-medium mb-1">Keuntungan Bersih</h3>
                    <p class="text-xs text-blue-200 mb-6">Profit dari barang terjual (Omset - HPP)</p>
                    <div class="bg-green-600 rounded-xl p-4 text-center">
                        <span class="block text-xs text-green-100 mb-1">Total Laba</span>
                        <span class="text-2xl font-bold">Rp {{ number_format($labaBersih, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="bg-blue-800 rounded-2xl p-6 text-white shadow-lg flex flex-col justify-between relative overflow-hidden">
                    <div class="absolute left-0 bottom-0 w-24 h-24 bg-white/5 rounded-full -ml-5 -mb-5"></div>
                    <div>
                        <h3 class="font-bold text-lg">Total Omset</h3>
                        <p class="text-blue-200 text-sm">Semua pendapatan penjualan</p>
                    </div>
                    <div class="mt-4 text-3xl font-bold">
                        Rp {{ number_format($omsetKotor, 0, ',', '.') }}
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-[#0F244A] font-bold text-lg">Total Transaksi</h3>
                        <p class="text-xs text-gray-500">Jumlah nota keluar</p>
                    </div>
                    <div class="bg-blue-50 text-blue-800 w-16 h-16 rounded-xl flex items-center justify-center font-bold text-2xl">
                        {{ $totalTerjual }}
                    </div>
                </div>

            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-[#0F244A] flex items-center gap-2">
                            <span>💰</span> Manajemen Kas Toko
                        </h3>
                        <p class="text-sm text-gray-500">Saldo kas saat ini (Real-time)</p>
                    </div>
                    
                    <form action="{{ route('umkm.reports.balance') }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="number" name="balance" placeholder="Set Saldo Awal" class="text-sm border border-gray-300 rounded-lg px-3 py-2 w-40 focus:ring-blue-500 focus:border-blue-500">
                        <button type="submit" class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-lg text-sm font-bold transition">Atur Ulang</button>
                    </form>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 p-8 rounded-2xl text-white shadow-lg flex flex-col justify-center items-center text-center">
                        <p class="text-green-100 text-sm font-medium mb-2">SISA KAS TUNAI</p>
                        <h2 class="text-5xl font-bold tracking-tight">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h2>
                        <p class="text-xs text-green-200 mt-4 bg-white/10 px-3 py-1 rounded-full">
                            Otomatis bertambah saat jual & berkurang saat belanja
                        </p>
                    </div>

                    <div class="space-y-4 text-sm text-gray-600 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <h4 class="font-bold text-gray-800 border-b pb-2 mb-2">Simulasi Perubahan Kas:</h4>
                        
                        <div class="flex justify-between">
                            <span>1. Anda input Modal Awal</span>
                            <span class="font-bold text-green-600">+ Rp 1.000.000</span>
                        </div>
                        <div class="flex justify-between">
                            <span>2. Beli Susu (Inventori)</span>
                            <span class="font-bold text-red-500">- Rp 20.000</span>
                        </div>
                         <div class="flex justify-between">
                            <span>3. Jual Kopi Susu (Omset)</span>
                            <span class="font-bold text-green-600">+ Rp 15.000</span>
                        </div>
                        <div class="pt-2 border-t flex justify-between font-bold text-gray-800">
                            <span>Sisa Kas Akhir</span>
                            <span>Rp 995.000</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>
</html>