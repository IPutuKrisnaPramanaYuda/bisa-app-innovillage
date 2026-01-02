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
        @include('umkm.header', ['title' => 'Manajemen Produk'])

        <div class="p-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#0F244A] text-white">
                        <tr>
                            <th class="px-6 py-4 font-semibold text-sm rounded-tl-lg">Produk</th> <th class="px-6 py-4 font-semibold text-sm">Modal (HPP)</th>
                            <th class="px-6 py-4 font-semibold text-sm">Harga Jual</th>
                            <th class="px-6 py-4 font-semibold text-sm">Stok</th>
                            <th class="px-6 py-4 font-semibold text-sm rounded-tr-lg text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
    @forelse($products as $index => $product)
    <tr class="hover:bg-gray-50 transition">
        <td class="px-6 py-4">
            <div class="flex items-center gap-4">
                <span class="text-gray-400 font-bold text-xs">{{ $index + 1 }}</span>
                
                <div class="w-12 h-12 rounded-lg bg-gray-200 overflow-hidden border border-gray-200 flex-shrink-0">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No img</div>
                    @endif
                </div>

                <div>
                    <div class="font-bold text-gray-800">{{ $product->name }}</div>
                    <div class="text-[10px] text-gray-500 mb-1">{{ Str::limit($product->description, 30) }}</div>
                    
                    <div class="text-[10px] text-blue-500 bg-blue-50 px-2 py-1 rounded inline-block">
                        <strong>Resep:</strong>
                        @foreach($product->ingredients as $ing)
                           {{ $ing->inventory->name ?? '?' }} ({{ $ing->amount + 0 }}), 
                        @endforeach
                    </div>
                    </div>
            </div>
        </td>

        <td class="px-6 py-4 text-gray-500 text-sm">
            Rp {{ number_format($product->cost_price, 0, ',', '.') }}
        </td>

        <td class="px-6 py-4 font-bold text-[#0F244A]">
            Rp {{ number_format($product->price, 0, ',', '.') }}
        </td>

        <td class="px-6 py-4 w-48">
            <div class="flex justify-between text-xs mb-1">
                <span class="{{ $product->computed_stock < 5 ? 'text-red-600 font-bold' : 'text-green-600' }}">
                    {{ $product->computed_stock < 5 ? 'Bahan Menipis' : 'Ready' }}
                </span>
                <span class="font-bold">{{ $product->computed_stock }} Porsi</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-[#0F244A] h-2 rounded-full" style="width: {{ min($product->computed_stock, 100) }}%"></div>
            </div>
        </td>

        <td class="px-6 py-4 text-center">
            <div class="flex items-center justify-center gap-2">
                <a href="{{ route('umkm.products.edit', $product->id) }}" class="text-blue-400 hover:text-blue-600 transition p-2 bg-blue-50 rounded-lg hover:bg-blue-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </a>

                <form action="{{ route('umkm.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin hapus produk ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-400 hover:text-red-600 transition p-2 bg-red-50 rounded-lg hover:bg-red-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="5" class="px-6 py-10 text-center text-gray-400 flex flex-col items-center justify-center">
            <div class="text-4xl mb-2">📦</div>
            Belum ada produk. Tambahkan sekarang!
        </td>
    </tr>
    @endforelse
</tbody>
                </table>
            </div>
            
            <div class="mt-6 flex justify-end">
                <a href="{{ route('umkm.products.create') }}" class="bg-[#0F244A] text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition flex items-center gap-2">
                    <span>+</span> Tambah Produk Baru
                </a>
            </div>
        </div>
    </main>
</body>
</html>