<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Stok Produk') }}
            </h2>
            <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm">
                + Tambah Produk
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if($products->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-800">
                                <thead>
                                    <tr>
                                        <th class="py-2 px-4 border-b text-left">Foto</th>
                                        <th class="py-2 px-4 border-b text-left">Nama Produk</th>
                                        <th class="py-2 px-4 border-b text-left">Harga</th>
                                        <th class="py-2 px-4 border-b text-left">Stok</th>
                                        <th class="py-2 px-4 border-b text-left">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="py-2 px-4 border-b">
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" class="w-12 h-12 object-cover rounded">
                                            @else
                                                <span class="text-xs text-gray-400">No Image</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b font-bold">{{ $product->name }}</td>
                                        <td class="py-2 px-4 border-b">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <span class="px-2 py-1 rounded text-xs {{ $product->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $product->stock }} Pcs
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <a href="#" class="text-blue-600 hover:text-blue-900 text-sm">Edit</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-10">
                            <p class="text-gray-500 mb-4">Belum ada produk yang dijual.</p>
                            <a href="{{ route('products.create') }}" class="text-indigo-600 hover:underline">Yuk, upload produk pertama!</a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>