<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Keranjang Belanja - BENGKALA.ID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gray-100">

    <nav class="bg-white border-b">
        <div class="container mx-auto px-6 py-4 flex items-center gap-4">
            <a href="{{ url('/') }}" class="text-2xl font-bold text-orange-600">BENGKALA<span class="text-indigo-700">.ID</span></a>
            <div class="h-6 w-px bg-gray-300"></div>
            <span class="text-xl text-gray-700">Keranjang Belanja</span>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8">
        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">{{ session('error') }}</div>
        @endif

        @if($carts->count() > 0)
        <div class="flex flex-col md:flex-row gap-6">
            <div class="flex-1">
                <div class="bg-white rounded shadow-sm overflow-hidden">
                    <div class="p-4 border-b bg-gray-50 flex items-center text-gray-500 text-sm">
                        <div class="w-1/2">Produk</div>
                        <div class="w-1/4 text-center">Harga Satuan</div>
                        <div class="w-1/4 text-center">Jumlah</div>
                        <div class="w-20 text-center">Aksi</div>
                    </div>

                    @foreach($carts as $cart)
                    <div class="p-4 border-b flex items-center hover:bg-gray-50 transition">
                        <div class="w-1/2 flex items-center gap-4">
                            <img src="{{ $cart->product->image ? asset('storage/'.$cart->product->image) : 'https://via.placeholder.com/80' }}" class="w-20 h-20 object-cover rounded border">
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $cart->product->name }}</h3>
                                <p class="text-xs text-gray-500">Toko: {{ $cart->product->umkm->name ?? 'UMKM Bengkala' }}</p>
                                <p class="text-xs text-red-500 mt-1">Sisa Stok: {{ $cart->product->stock }}</p>
                            </div>
                        </div>
                        
                        <div class="w-1/4 text-center font-semibold text-gray-700">
                            Rp {{ number_format($cart->product->price, 0, ',', '.') }}
                        </div>

                        <div class="w-1/4 text-center">
                            <span class="border px-3 py-1 rounded bg-white">{{ $cart->quantity }}</span>
                        </div>

                        <div class="w-20 text-center">
                            <form action="{{ route('cart.remove', $cart->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 text-sm font-bold">Hapus</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="w-full md:w-1/3">
                <div class="bg-white rounded shadow-sm p-6 sticky top-24">
                    <h3 class="text-lg font-bold mb-4">Ringkasan Pesanan</h3>
                    <div class="flex justify-between mb-2 text-gray-600">
                        <span>Total Item</span>
                        <span>{{ $carts->sum('quantity') }} Barang</span>
                    </div>
                    <div class="flex justify-between mb-6 text-xl font-bold text-orange-600">
                        <span>Total Harga</span>
                        <span>Rp {{ number_format($carts->sum(function($c){ return $c->product->price * $c->quantity; }), 0, ',', '.') }}</span>
                    </div>

                    <form action="{{ route('cart.checkout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-orange-500 text-white py-3 rounded font-bold hover:bg-orange-600 shadow-lg transition transform hover:scale-105">
                            Checkout Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @else
            <div class="text-center py-20 bg-white rounded shadow-sm">
                <div class="text-6xl mb-4">🛒</div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Keranjang Belanja Kosong</h2>
                <p class="text-gray-500 mb-6">Yuk isi dengan produk UMKM Desa Bengkala!</p>
                <a href="{{ url('/mart') }}" class="bg-indigo-600 text-white px-6 py-3 rounded-full font-bold hover:bg-indigo-700">Belanja Sekarang</a>
            </div>
        @endif
    </div>

</body>
</html>