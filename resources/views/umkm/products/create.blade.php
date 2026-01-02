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
        @include('umkm.header', ['title' => 'Tambah Produk Baru'])

        <div class="p-8 max-w-4xl mx-auto w-full">
            <form action="{{ route('umkm.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <div class="md:col-span-2 space-y-6">
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Informasi Produk</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                                    <input type="text" name="name" required placeholder="Contoh: Kopi Susu Aren" class="w-full rounded-lg border-gray-300 border p-2.5 focus:ring-[#0F244A] focus:border-[#0F244A]">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Foto</label>
                                    <input type="file" name="image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                    <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 border p-2.5"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 flex justify-between items-center">
                                <span>Resep & HPP</span>
                                <span class="text-xs font-normal bg-blue-100 text-blue-700 px-2 py-1 rounded">Stok Otomatis</span>
                            </h3>
                            
                            <div class="bg-blue-50 p-4 rounded-xl mb-4 text-xs text-blue-800">
                                Tentukan bahan apa saja yang dipakai untuk membuat <b>1 Porsi</b> produk ini. <br>
                                Stok produk akan otomatis dihitung berdasarkan ketersediaan bahan di gudang.
                            </div>

                            <div id="ingredient-list" class="space-y-3 mb-4"></div>

                            <button type="button" onclick="addIngredientRow()" class="text-sm text-blue-600 font-bold hover:underline mb-6">+ Tambah Bahan Lain</button>

                            <div class="bg-gray-100 p-4 rounded-xl flex justify-between items-center border border-gray-200">
                                <label class="font-bold text-gray-700">Total Modal (HPP)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500 font-bold">Rp</span>
                                    <input type="number" id="total_hpp" name="cost_price" readonly required value="0" class="pl-10 w-40 font-bold text-gray-800 bg-transparent border-none focus:ring-0 text-right text-lg">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-[#0F244A] text-white p-6 rounded-2xl shadow-lg sticky top-6">
                            <h3 class="font-bold text-lg mb-4">Penetapan Harga</h3>
                            
                            <div class="mb-6">
                                <label class="block text-sm text-blue-200 mb-1">Harga Jual Konsumen</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-gray-500 font-bold">Rp</span>
                                    <input type="number" id="selling_price" name="price" required oninput="calculateProfit()" class="w-full pl-10 py-3 rounded-xl text-gray-900 font-bold text-xl focus:ring-2 ring-blue-400 outline-none">
                                </div>
                            </div>

                            <div class="border-t border-white/20 pt-4">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-300">Estimasi Laba/Porsi:</span>
                                    <span class="font-bold text-green-400" id="est_profit">Rp 0</span>
                                </div>
                                <div class="w-full bg-white/20 h-2 rounded-full mt-2">
                                    <div id="profit_bar" class="bg-green-400 h-2 rounded-full transition-all duration-500" style="width: 0%"></div>
                                </div>
                                <p class="text-xs text-gray-400 mt-2 text-center" id="margin_text">Margin: 0%</p>
                            </div>

                            <button type="submit" class="w-full mt-8 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl shadow-lg transition transform hover:scale-105">
                                Simpan Produk
                            </button>
                            <a href="{{ route('umkm.products') }}" class="block text-center text-sm text-gray-400 mt-4 hover:text-white">Batal</a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </main>

    <script>
    let rowCount = 0; // Index unik untuk array input

    // Template Option (Dari PHP ke JS)
    const inventoryOptions = `
        <option value="" data-price="0">-- Pilih Bahan --</option>
        @foreach($inventoryItems as $inv)
            <option value="{{ $inv->id }}" data-price="{{ $inv->price_per_unit }}" data-unit="{{ $inv->unit }}">
                {{ $inv->name }} (Stok: {{ $inv->stock }} {{ $inv->unit }})
            </option>
        @endforeach
    `;

    // Fungsi Tambah Baris
    function addIngredientRow() {
        const container = document.getElementById('ingredient-list');
        const div = document.createElement('div');
        div.className = 'flex gap-2 ingredient-row items-center';
        
        // Perhatikan name="ingredients[...][...]" ini PENTING untuk Controller
        div.innerHTML = `
            <select name="ingredients[${rowCount}][id]" class="flex-1 text-sm rounded-lg border p-2 ingredient-select" onchange="updateRowPrice(this)" required>
                ${inventoryOptions}
            </select>
            
            <div class="relative w-24">
                <input type="number" step="any" name="ingredients[${rowCount}][amount]" placeholder="0" class="w-full text-sm rounded-lg border p-2 pr-8 ingredient-amount" oninput="updateRowPrice(this)" required>
                <span class="absolute right-2 top-2 text-xs text-gray-400 unit-label">-</span>
            </div>

            <input type="text" readonly placeholder="Rp 0" class="w-24 text-sm bg-gray-100 rounded-lg border p-2 text-right font-bold ingredient-cost-display">
            <input type="hidden" class="ingredient-cost" value="0">
            
            <button type="button" onclick="this.closest('.ingredient-row').remove(); calculateHPP();" class="text-red-500 hover:text-red-700 font-bold px-2 text-xl">&times;</button>
        `;
        container.appendChild(div);
        rowCount++; // Naikkan counter agar index array beda
    }

    // Fungsi Update Harga per Baris
    function updateRowPrice(element) {
        const row = element.closest('.ingredient-row');
        const select = row.querySelector('.ingredient-select');
        const amountInput = row.querySelector('.ingredient-amount');
        const unitLabel = row.querySelector('.unit-label');
        const costDisplay = row.querySelector('.ingredient-cost-display');
        const costInput = row.querySelector('.ingredient-cost');

        // Ambil data
        const pricePerUnit = parseFloat(select.options[select.selectedIndex].getAttribute('data-price')) || 0;
        const unit = select.options[select.selectedIndex].getAttribute('data-unit') || '-';
        const amount = parseFloat(amountInput.value) || 0;

        // Hitung
        const totalCost = pricePerUnit * amount;

        // Update UI
        unitLabel.innerText = unit;
        costDisplay.value = 'Rp ' + Math.round(totalCost).toLocaleString('id-ID');
        costInput.value = totalCost;

        // Hitung Total HPP Keseluruhan
        calculateHPP();
    }

    // Hitung Total HPP (Loop semua baris)
    function calculateHPP() {
        let total = 0;
        document.querySelectorAll('.ingredient-cost').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        
        // Masukkan ke input hidden yang dikirim ke server
        document.getElementById('total_hpp').value = Math.round(total);
        calculateProfit();
    }

    // Hitung Margin Keuntungan
    function calculateProfit() {
        const hpp = parseFloat(document.getElementById('total_hpp').value) || 0;
        const jual = parseFloat(document.getElementById('selling_price').value) || 0;
        const profit = jual - hpp;
        const margin = jual > 0 ? (profit / jual) * 100 : 0;

        document.getElementById('est_profit').innerText = 'Rp ' + profit.toLocaleString('id-ID');
        document.getElementById('est_profit').className = profit > 0 ? 'font-bold text-green-400' : 'font-bold text-red-400';
        document.getElementById('margin_text').innerText = 'Margin: ' + margin.toFixed(1) + '%';
        
        let barWidth = margin;
        if(barWidth < 0) barWidth = 0;
        if(barWidth > 100) barWidth = 100;
        document.getElementById('profit_bar').style.width = barWidth + '%';
    }

    // Jalankan otomatis saat halaman load (tambah 1 baris kosong)
    window.onload = function() {
        addIngredientRow();
    };
    </script>
</body>
</html> 