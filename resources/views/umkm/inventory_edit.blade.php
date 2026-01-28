<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name') }}</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 flex h-screen overflow-hidden" x-data="{ sidebarOpen: window.innerWidth >= 768 }">
    @include('umkm.sidebar') 

    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-[#F5F7FA]">
        @include('umkm.header', ['title' => 'Edit Item Inventori'])

        <div class="p-8 max-w-2xl mx-auto w-full">
            <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                
                <h3 class="font-bold text-[#0F244A] text-xl mb-6 border-b pb-4">Edit Data: {{ $item->name }}</h3>
                
                <form action="{{ route('umkm.inventory.update', $item->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Item</label>
                        <input type="text" name="name" required value="{{ old('name', $item->name) }}" class="w-full rounded-lg border-gray-300 border p-3 focus:ring-[#0F244A] focus:border-[#0F244A]">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                            <select name="category" class="w-full rounded-lg border-gray-300 border p-3">
                                <option value="bahan" {{ $item->category == 'bahan' ? 'selected' : '' }}>Bahan Baku</option>
                                <option value="alat" {{ $item->category == 'alat' ? 'selected' : '' }}>Alat/Perlengkapan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Satuan</label>
                            <input type="text" name="unit" required value="{{ old('unit', $item->unit) }}" class="w-full rounded-lg border-gray-300 border p-3">
                        </div>
                    </div>

                    <div class="bg-blue-50 p-5 rounded-xl space-y-4 border border-blue-100">
                        <div class="flex justify-between items-center">
                            <p class="text-xs font-bold text-blue-800 uppercase">Kalkulator Harga Pokok</p>
                            <span class="text-xs bg-white px-2 py-1 rounded text-blue-600 border">Saat ini: Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}/{{ $item->unit }}</span>
                        </div>
                        
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Harga Beli Total (Rp)</label>
                            <input type="number" name="total_price" required value="{{ old('total_price', $item->price_per_unit * 1) }}" class="w-full rounded-lg border-gray-300 border p-2 text-sm">
                            <p class="text-[10px] text-gray-400 mt-1">*Masukkan harga beli terbaru untuk update HPP.</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Untuk Jumlah (Satuan)</label>
                            <input type="number" name="purchase_amount" required value="1" class="w-full rounded-lg border-gray-300 border p-2 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Stok Saat Ini</label>
                        <input type="number" name="stock" required value="{{ old('stock', $item->stock) }}" class="w-full rounded-lg border-gray-300 border p-3">
                    </div>

                    <div class="flex gap-4 pt-4">
                        <a href="{{ route('umkm.inventory') }}" class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition text-center">Batal</a>
                        <button type="submit" class="flex-1 py-3 bg-[#0F244A] text-white rounded-xl font-bold hover:bg-blue-900 transition shadow-lg">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <x-umkm-ai-widget />
    <x-accessibility-widget />
</body>
</html>