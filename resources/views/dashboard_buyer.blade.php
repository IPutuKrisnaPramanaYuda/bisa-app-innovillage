<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profil Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-100 p-4 rounded-full text-2xl">👤</div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>

                <div>
                    @if($umkm)
                        <div class="text-right">
                            <p class="text-xs text-gray-500 mb-1">Anda Pemilik Toko: <strong>{{ $umkm->name }}</strong></p>
                            
                            <a href="{{ route('umkm.dashboard') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 transition">
                                🏪 Kelola Toko Saya
                            </a>

                        </div>
                    @else
                        <div class="text-right">
                            <p class="text-xs text-gray-500 mb-1">Mau jualan di Bengkala?</p>
                            <a href="{{ route('umkm.create') }}" class="inline-block bg-orange-500 text-white px-4 py-2 rounded shadow hover:bg-orange-600 transition">
                                🚀 Buka Toko Gratis
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">📦 Pesanan Saya</h3>
                
                @if($riwayatBelanja->count() > 0)
                    <div class="space-y-4">
                        @foreach($riwayatBelanja as $trx)
                        <div class="border rounded-lg p-4 flex justify-between items-center dark:border-gray-700">
                            <div class="flex items-center gap-4">
                                <img src="{{ $trx->product && $trx->product->image ? asset('storage/'.$trx->product->image) : 'https://via.placeholder.com/60' }}" class="w-16 h-16 object-cover rounded">
                                
                                <div>
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200">{{ $trx->product->name ?? 'Produk Dihapus' }}</h4>
                                    <p class="text-sm text-gray-500">{{ $trx->quantity }} x Rp {{ number_format($trx->amount / $trx->quantity, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-400">{{ $trx->created_at->format('d M Y') }}</p>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="font-bold text-orange-600">Total: Rp {{ number_format($trx->amount, 0, ',', '.') }}</p>
                                <span class="px-2 py-1 text-xs rounded 
                                    {{ $trx->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $trx->status == 'paid' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ strtoupper($trx->status) }}
                                </span>
                                @if($trx->status == 'pending')
                                    <a href="{{ route('payment.page') }}" class="block mt-2 text-xs text-blue-600 underline">Bayar Sekarang</a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Belum ada pesanan. <a href="{{ route('marketplace.index') }}" class="text-indigo-600">Yuk belanja!</a></p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>