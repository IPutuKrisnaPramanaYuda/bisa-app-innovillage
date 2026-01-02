<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Belanja - Oleh-Oleh Desa Bengkala</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-2xl font-bold text-indigo-600">BISA<span class="text-gray-800">.Mart</span></a>
            
            <div class="hidden md:flex space-x-6 items-center">
                <a href="{{ url('/') }}" class="text-gray-600 hover:text-indigo-600">Beranda</a>
                <a href="{{ route('marketplace.index') }}" class="text-indigo-600 font-bold">Belanja</a>
            </div>

            <div>
                @auth
                    <a href="{{ url('/dashboard') }}" class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-full font-semibold hover:bg-indigo-200">Dashboard Saya</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 mr-4">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700">Daftar Toko</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="bg-indigo-600 py-12 mb-10">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Oleh-Oleh Khas Bengkala</h1>
            <p class="text-indigo-100 mb-8">Temukan produk tenun, jamu, dan kerajinan asli desa.</p>
            
            <form action="{{ route('marketplace.index') }}" method="GET" class="max-w-xl mx-auto flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kopi, kain, atau nama toko..." class="flex-1 px-5 py-3 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-300 shadow-lg">
                <button type="submit" class="bg-yellow-400 text-yellow-900 font-bold px-6 py-3 rounded-full hover:bg-yellow-300 shadow-lg transition">Cari</button>
            </form>
        </div>
    </div>

    <div class="container mx-auto px-6 pb-20">
        
        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                @foreach($products as $product)
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden group">
                    <div class="h-48 overflow-hidden bg-gray-200 relative">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400">
                                <span>No Image</span>
                            </div>
                        @endif
                        
                        <div class="absolute top-2 right-2 bg-white/90 backdrop-blur px-2 py-1 rounded text-xs font-bold {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                            Stok: {{ $product->stock }}
                        </div>
                    </div>

                    <div class="p-5">
                        <p class="text-xs text-indigo-500 font-semibold mb-1 uppercase tracking-wide">{{ $product->umkm->name }}</p>
                        <h3 class="text-lg font-bold text-gray-900 mb-2 truncate">{{ $product->name }}</h3>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ $product->description ?? 'Produk asli berkualitas dari warga desa.' }}</p>
                        
                        <div class="flex items-center justify-between mt-4">
                            <span class="text-xl font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        </div>

                        <a href="https://wa.me/?text=Halo%20{{ $product->umkm->name }},%20saya%20tertarik%20beli%20{{ urlencode($product->name) }}" target="_blank" class="block w-full mt-4 bg-green-500 text-white text-center py-2 rounded-lg font-semibold hover:bg-green-600 transition flex items-center justify-center gap-2">
                            <span>💬 Beli via WA</span>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $products->withQueryString()->links() }}
            </div>

        @else
            <div class="text-center py-20">
                <div class="text-6xl mb-4">😢</div>
                <h3 class="text-xl font-bold text-gray-700">Produk tidak ditemukan</h3>
                <p class="text-gray-500">Coba kata kunci lain atau kembali lagi nanti.</p>
                <a href="{{ route('marketplace.index') }}" class="inline-block mt-4 text-indigo-600 hover:underline">Lihat Semua Produk</a>
            </div>
        @endif

    </div>

    <footer class="bg-gray-900 text-white py-8 text-center mt-auto">
        <p>&copy; {{ date('Y') }} BISA Market. Karya Mahasiswa Innovillage.</p>
    </footer>

</body>
</html>