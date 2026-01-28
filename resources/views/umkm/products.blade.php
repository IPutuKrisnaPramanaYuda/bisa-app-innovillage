<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Manajemen Produk' }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        
       

        @include('umkm.header', ['title' => 'Manajemen Produk'])

        <div class="p-4 md:p-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r shadow-sm text-sm" role="alert">
                    <p class="font-bold">Sukses!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[800px]"> <thead class="bg-[#0F244A] text-white">
                            <tr>
                                <th class="px-6 py-4 font-semibold text-sm">Produk</th> 
                                <th class="px-6 py-4 font-semibold text-sm">Resep & Modal</th>
                                <th class="px-6 py-4 font-semibold text-sm">Harga Jual</th>
                                <th class="px-6 py-4 font-semibold text-sm">Stok (Otomatis)</th>
                                <th class="px-6 py-4 font-semibold text-sm text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        @forelse($products as $index => $product)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 align-top w-1/3 min-w-[250px]">
                                <div class="flex gap-4">
                                    <span class="text-gray-400 font-bold text-xs mt-1">{{ $index + 1 }}</span>
                                    
                                    <div class="w-12 h-12 rounded-lg bg-gray-200 overflow-hidden border border-gray-200 flex-shrink-0">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs bg-gray-100">No img</div>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="font-bold text-gray-800 text-base">{{ $product->name }}</div>
                                        <div class="text-[10px] text-gray-500 line-clamp-2">{{ $product->description ?? 'Tidak ada deskripsi' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 align-top min-w-[200px]">
                                @if($product->ingredients->count() > 0)
                                    <div class="flex flex-wrap gap-1 mb-1">
                                        @foreach($product->ingredients as $ing)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                {{ $ing->name }}: {{ $ing->pivot->amount + 0 }} {{ $ing->unit }}
                                            </span>
                                        @endforeach
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Estimasi Modal: <span class="font-bold text-gray-700">Rp {{ number_format($product->ingredients->sum(fn($i) => $i->price_per_unit * $i->pivot->amount), 0, ',', '.') }}</span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Produk tanpa resep (Stok Manual)</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 align-top font-bold text-[#0F244A] whitespace-nowrap">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 align-top w-48 min-w-[180px]">
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="{{ $product->computed_stock < 5 ? 'text-red-600 font-bold animate-pulse' : 'text-green-600 font-bold' }}">
                                        {{ $product->computed_stock < 5 ? 'Menipis!' : 'Aman' }}
                                    </span>
                                    <span class="font-bold text-gray-800">{{ $product->computed_stock }} Porsi</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500 {{ $product->computed_stock < 5 ? 'bg-red-500' : 'bg-green-500' }}" 
                                         style="width: {{ min(($product->computed_stock / 50) * 100, 100) }}%"></div>
                                </div>
                                @if($product->computed_stock <= 0 && $product->ingredients->count() > 0)
                                    <p class="text-[9px] text-red-500 mt-1">Cek stok bahan baku!</p>
                                @endif
                            </td>

                            <td class="px-6 py-4 align-top text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('umkm.products.edit', $product->id) }}" class="text-blue-500 hover:text-blue-700 p-1.5 bg-blue-50 rounded-lg transition hover:bg-blue-100" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>

                                    <form action="{{ route('umkm.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin hapus produk ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-1.5 bg-red-50 rounded-lg transition hover:bg-red-100" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-gray-400 flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                                <p class="text-sm font-medium text-gray-500">Belum ada produk.</p>
                                <p class="text-xs text-gray-400 mt-1">Minta AI untuk buatkan produk baru sekarang!</p>
                            </td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <a href="{{ route('umkm.products.create') }}" class="w-full md:w-auto bg-[#0F244A] text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition hover:-translate-y-0.5 flex items-center justify-center gap-2 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tambah Produk Manual
                </a>
            </div>
        </div>
    </main>
    
    <x-umkm-ai-widget />
    <x-accessibility-widget />
</body>
</html>