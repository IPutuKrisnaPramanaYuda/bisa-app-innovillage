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
        @include('umkm.header', ['title' => 'Inventori & Bahan Baku'])

        <div class="p-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-[#0F244A] text-white px-6 py-4 font-bold flex justify-between items-center">
                        <span>Daftar Barang & Bahan</span>
                        <span class="text-xs bg-white/20 px-2 py-1 rounded">{{ $items->count() }} Item</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-600 border-b">
                                <tr>
                                    <th class="px-6 py-3">Nama</th>
                                    <th class="px-6 py-3">Kategori</th>
                                    <th class="px-6 py-3">Harga/Unit</th>
                                    <th class="px-6 py-3 text-right">Stok</th>
                                    <th class="px-6 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($items as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-800">{{ $item->name }}</td>
                                    <td class="px-6 py-4">
                                        @if($item->category == 'bahan')
                                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold uppercase">Bahan Baku</span>
                                        @else
                                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold uppercase">Alat / Cup</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        Rp {{ number_format($item->price_per_unit, 0, ',', '.') }} <span class="text-[10px]">/{{ $item->unit }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="font-bold text-lg {{ $item->stock < 10 ? 'text-red-500' : 'text-[#0F244A]' }}">
                                            {{ $item->stock }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
    <div class="flex items-center justify-center gap-2">
        <a href="{{ route('umkm.inventory.edit', $item->id) }}" class="text-blue-400 hover:text-blue-600 transition p-1.5 bg-blue-50 rounded-lg hover:bg-blue-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        </a>

        <form action="{{ route('umkm.inventory.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini?');">
            @csrf @method('DELETE')
            <button type="submit" class="text-red-400 hover:text-red-600 transition p-1.5 bg-red-50 rounded-lg hover:bg-red-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </form>
    </div>
</td>
                                @empty
                                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada data inventori.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 h-fit">
                <h3 class="font-bold text-[#0F244A] text-lg mb-1">Tambah Inventori</h3>
                <p class="text-xs text-gray-400 mb-4">Input bahan baku atau alat baru.</p>
                
                <form action="{{ route('umkm.inventory.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Nama Item</label>
                        <input type="text" name="name" required placeholder="Contoh: Susu Full Cream" class="w-full text-sm rounded-lg border-gray-300 border p-2 focus:ring-[#0F244A] focus:border-[#0F244A]">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Kategori</label>
                            <select name="category" class="w-full text-sm rounded-lg border-gray-300 border p-2">
                                <option value="bahan">Bahan Baku</option>
                                <option value="alat">Alat/Perlengkapan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Satuan</label>
                            <input type="text" name="unit" required placeholder="ml, gr, pcs" class="w-full text-sm rounded-lg border-gray-300 border p-2">
                        </div>
                    </div>

                    <div class="border-t border-dashed my-2"></div>
                    
                    <div class="bg-blue-50 p-3 rounded-xl space-y-3">
                        <p class="text-[10px] font-bold text-blue-800 uppercase">Kalkulator Harga Pokok</p>
                        
                        <div>
                            <label class="block text-xs text-gray-500">Harga Beli Total (Rp)</label>
                            <input type="number" name="total_price" required placeholder="Mis: 20000" class="w-full text-sm rounded-lg border-gray-300 border p-2">
                        </div>
                        
                        <div>
                            <label class="block text-xs text-gray-500">Untuk Jumlah (Isi Satuan)</label>
                            <input type="number" name="purchase_amount" required placeholder="Mis: 1000 (jika 1 Liter)" class="w-full text-sm rounded-lg border-gray-300 border p-2">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1">Stok Awal Tersedia</label>
                        <input type="number" name="stock" required placeholder="0" class="w-full text-sm rounded-lg border-gray-300 border p-2">
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#0F244A] text-white rounded-xl font-bold hover:bg-blue-900 transition flex justify-center items-center gap-2 shadow-lg mt-2">
                        <span>+</span> Simpan Item
                    </button>
                </form>
            </div>

        </div>
    </main>
</body>
</html>