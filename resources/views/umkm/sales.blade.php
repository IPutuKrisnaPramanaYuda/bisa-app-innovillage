<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Penjualan' }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Poppins', sans-serif; } 
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gray-100 flex h-screen overflow-hidden" 
      x-data="{ sidebarOpen: window.innerWidth >= 768 }" 
      @resize.window="sidebarOpen = window.innerWidth >= 768">

    <div x-show="sidebarOpen && window.innerWidth < 768" 
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black bg-opacity-50 z-[90] md:hidden"
         x-transition.opacity>
    </div>

    <div x-show="sidebarOpen" 
         class="fixed inset-y-0 left-0 z-[99] w-64 bg-[#0F244A] transition-transform duration-300 transform md:relative md:translate-x-0 shrink-0"
         :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full">
         
         @include('umkm.sidebar') 
    </div>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-[#F5F7FA] w-full relative z-0">
        
        @include('umkm.header', ['title' => 'Penjualan'])

        <div class="p-4 md:p-8 space-y-6 md:space-y-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 md:p-4 rounded shadow-sm text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 md:p-4 rounded shadow-sm text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex justify-end">
                <button onclick="toggleSalesModal()" class="w-full md:w-auto bg-[#0F244A] hover:bg-blue-900 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition flex items-center justify-center gap-2 transform hover:scale-105 active:scale-95 text-sm md:text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Catat Penjualan
                </button>
            </div>

            <div class="bg-white p-4 md:p-6 rounded-2xl border-2 border-blue-500 shadow-sm">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-2">
                    <h3 class="font-bold text-gray-700 text-sm md:text-base">Grafik Penjualan Bulan Ini</h3>
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ now()->format('F Y') }}</span>
                </div>
                <div class="h-64 md:h-80 w-full">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="bg-[#0F244A] rounded-2xl p-4 md:p-6 text-white shadow-lg">
                <h3 class="text-lg font-bold mb-4">Riwayat Transaksi</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="uppercase tracking-wider border-b border-white/20 text-gray-300">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">Total</th>
                                <th class="px-4 py-3">Ket</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse($transactions as $index => $trx)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-4">{{ $index + 1 }}</td>
                                <td class="px-4 py-4 font-bold">
                                    {{ $trx->product->name ?? 'Terhapus' }}
                                </td>
                                <td class="px-4 py-4">{{ $trx->created_at->format('d/m/H:i') }}</td>
                                <td class="px-4 py-4 font-bold">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-xs text-gray-300">
                                    {{ Str::limit($trx->description ?? 'Selesai', 20) }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="px-4 py-4 text-center text-gray-400">Belum ada transaksi</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div id="salesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4 backdrop-blur-sm z-[100]">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col max-h-[90vh]">
            <div class="bg-[#0F244A] text-white px-6 py-4 flex justify-between items-center shrink-0">
                <h3 class="text-lg font-bold">Catat Penjualan</h3>
                <button onclick="toggleSalesModal()" class="text-white hover:text-gray-300 text-xl">&times;</button>
            </div>

            <form action="{{ route('umkm.sales.store') }}" method="POST" class="p-6 space-y-4 overflow-y-auto">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Produk</label>
                    <select id="productSelect" name="product_id" required onchange="updateTotal()" class="w-full rounded-lg border-gray-300 p-2.5 focus:ring-[#0F244A] focus:border-[#0F244A] text-sm">
                        <option value="" data-price="0">-- Pilih Produk --</option>
                        @foreach($products as $p)
                            @php $stokNyata = $p->computed_stock; @endphp
                            <option value="{{ $p->id }}" 
                                    data-price="{{ $p->price }}" 
                                    {{ $stokNyata <= 0 ? 'disabled' : '' }} 
                                    class="{{ $stokNyata <= 0 ? 'bg-gray-100 text-gray-400' : '' }}">
                                {{ $p->name }} (Stok: {{ $stokNyata }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                    <input type="number" id="qtyInput" name="quantity" min="1" value="1" required oninput="updateTotal()" class="w-full rounded-lg border-gray-300 p-2.5 focus:ring-[#0F244A] text-sm">
                </div>
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 flex justify-between items-center">
                    <span class="text-sm text-blue-800">Total:</span>
                    <span id="totalPriceDisplay" class="text-xl font-bold text-[#0F244A]">Rp 0</span>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="toggleSalesModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 rounded-xl text-sm">Batal</button>
                    <button type="submit" class="flex-1 bg-[#0F244A] hover:bg-blue-900 text-white font-bold py-2.5 rounded-xl shadow-lg text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function toggleSalesModal() {
        document.getElementById('salesModal').classList.toggle('hidden');
    }
    function updateTotal() {
        const select = document.getElementById('productSelect');
        const qtyInput = document.getElementById('qtyInput');
        const display = document.getElementById('totalPriceDisplay');
        const price = parseFloat(select.options[select.selectedIndex].getAttribute('data-price')) || 0;
        const qty = parseFloat(qtyInput.value) || 0;
        display.innerText = 'Rp ' + (price * qty).toLocaleString('id-ID');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        const labels = {!! json_encode($grafikLabel) !!};
        const data = {!! json_encode($grafikData) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Penjualan',
                    data: data,
                    borderColor: '#0F244A',
                    backgroundColor: 'rgba(15, 36, 74, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            font: { size: window.innerWidth < 768 ? 10 : 12 },
                            callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); }
                        }
                    },
                    x: { ticks: { font: { size: window.innerWidth < 768 ? 10 : 12 } } }
                }
            }
        });
    });
    </script>

    <x-umkm-ai-widget />
    <x-accessibility-widget />
</body>
</html>