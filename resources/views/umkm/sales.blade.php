<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden">
    @include('umkm.sidebar') 

    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-[#F5F7FA]">
        @include('umkm.header', ['title' => 'Penjualan'])

        <div class="p-8 space-y-8">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex justify-end">
                <button onclick="toggleSalesModal()" class="bg-[#0F244A] hover:bg-blue-900 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition flex items-center gap-2 transform hover:scale-105">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Catat Penjualan Baru
                </button>
            </div>

            <div class="bg-white p-6 rounded-2xl border-2 border-blue-500 shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-700">Grafik Penjualan Bulan Ini</h3>
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ now()->format('F Y') }}</span>
                </div>
                <div class="h-80">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <div class="bg-[#0F244A] rounded-2xl p-6 text-white shadow-lg">
                <h3 class="text-lg font-bold mb-4">Riwayat Transaksi</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="uppercase tracking-wider border-b border-white/20 text-gray-300">
                            <tr>
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3">Waktu</th>
                                <th class="px-4 py-3">Total Harga</th>
                                <th class="px-4 py-3">Ket</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse($transactions as $index => $trx)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-4">{{ $index + 1 }}</td>
                                <td class="px-4 py-4 font-bold">
                                    {{ $trx->product->name ?? 'Produk Terhapus' }}
                                </td>
                                <td class="px-4 py-4">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-4 font-bold">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-xs text-gray-300">
                                    {{ $trx->description ?? 'Selesai' }}
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

    <div id="salesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100">
            <div class="bg-[#0F244A] text-white px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold">Catat Penjualan Manual</h3>
                <button onclick="toggleSalesModal()" class="text-white hover:text-gray-300">&times;</button>
            </div>

            <form action="{{ route('umkm.sales.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Produk</label>
                    <select id="productSelect" name="product_id" required onchange="updateTotal()" class="w-full rounded-lg border-gray-300 border p-2.5 focus:ring-[#0F244A] focus:border-[#0F244A]">
    <option value="" data-price="0">-- Pilih Produk --</option>
    @foreach($products as $p)
        {{-- Hitung stok otomatis --}}
        @php $stokNyata = $p->computed_stock; @endphp

        {{-- Tampilkan di opsi, disable jika stok 0 --}}
        <option value="{{ $p->id }}" 
                data-price="{{ $p->price }}" 
                {{ $stokNyata <= 0 ? 'disabled' : '' }} 
                class="{{ $stokNyata <= 0 ? 'text-gray-400 bg-gray-100' : '' }}">
            
            {{ $p->name }} 
            (Stok: {{ $stokNyata }} Porsi) 
            - Rp {{ number_format($p->price, 0, ',', '.') }}
            
            @if($stokNyata <= 0) [Bahan Habis] @endif
        </option>
    @endforeach
</select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Pcs)</label>
                    <input type="number" id="qtyInput" name="quantity" min="1" value="1" required oninput="updateTotal()" class="w-full rounded-lg border-gray-300 border p-2.5 focus:ring-[#0F244A] focus:border-[#0F244A]">
                </div>
                <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 flex justify-between items-center">
                    <span class="text-sm text-blue-800">Total Harga:</span>
                    <span id="totalPriceDisplay" class="text-xl font-bold text-[#0F244A]">Rp 0</span>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="toggleSalesModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2.5 rounded-xl transition">Batal</button>
                    <button type="submit" class="flex-1 bg-[#0F244A] hover:bg-blue-900 text-white font-bold py-2.5 rounded-xl transition shadow-lg">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Logic Modal & Hitung Harga tetap sama
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

    // 2. CONFIG CHART JS (DIWRAPPING DOMContentLoaded)
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Memastikan data ter-inject dengan benar
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
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    });
</script>
</body>
</html>