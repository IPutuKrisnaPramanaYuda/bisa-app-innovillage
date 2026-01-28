<x-guest-layout>
    
    <div class="bg-gray-50 min-h-screen font-sans">

        <div class="bg-[#0F244A] shadow-sm border-b border-gray-100 pt-24 pb-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-10">
                    
                    <div class="lg:col-span-8 relative rounded-2xl overflow-hidden shadow-lg h-[300px] md:h-[380px]" 
                         x-data="{ 
                            active: 0, 
                            slides: [
                                {
                                    img: '{{ asset('images/shop/tenunn.png') }}',
                                    title: 'Pesona Tenun Bengkala',
                                    desc: 'Kain tenun ikat autentik karya tangan terampil warga Kolok.',
                                    color: 'bg-purple-900/80'
                                },
                                {
                                    img: '{{ asset('images/shop/jamu.png') }}',
                                    title: 'Jamu Herbal Sakuntala',
                                    desc: 'Warisan kesehatan alami dari rempah pilihan tanah desa.',
                                    color: 'bg-yellow-900/80'
                                },
                                {
                                    img: 'https://images.unsplash.com/photo-1497935586351-b67a49e012bf?auto=format&fit=crop&w=1200&q=80',
                                    title: 'Kopi & Kuliner Lokal',
                                    desc: 'Cita rasa khas yang diolah dengan penuh cinta.',
                                    color: 'bg-blue-900/80'
                                }
                            ],
                            timer: null,
                            startAutoSlide() { this.timer = setInterval(() => { this.active = (this.active + 1) % this.slides.length }, 5000); }
                         }" 
                         x-init="startAutoSlide()">
                        
                        <template x-for="(slide, index) in slides" :key="index">
                            <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
                                 x-show="active === index"
                                 x-transition:enter="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="opacity-100"
                                 x-transition:leave-end="opacity-0">
                                <img :src="slide.img" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-r from-black/80 to-transparent flex flex-col justify-center px-10 md:px-16 text-white">
                                    <span class="text-xs font-bold tracking-widest uppercase mb-2 text-yellow-400">Featured UMKM</span>
                                    <h2 class="text-3xl md:text-5xl font-extrabold mb-4 leading-tight" x-text="slide.title"></h2>
                                    <p class="text-lg text-gray-200 max-w-md mb-6" x-text="slide.desc"></p>
                                    <button class="px-6 py-3 bg-white text-slate-900 font-bold rounded-full w-max hover:bg-blue-50 transition">
                                        Lihat Koleksi
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div class="absolute bottom-5 left-10 flex gap-2">
                            <template x-for="(slide, index) in slides" :key="index">
                                <button @click="active = index; clearInterval(timer); startAutoSlide()" 
                                        class="w-3 h-3 rounded-full transition-all"
                                        :class="active === index ? 'bg-white w-8' : 'bg-white/50 hover:bg-white'"></button>
                            </template>
                        </div>
                    </div>

                    <div class="lg:col-span-4 flex flex-col gap-6 h-[300px] md:h-[380px]">
                        <div class="flex-1 rounded-2xl bg-blue-50 border border-blue-100 p-6 flex flex-col justify-center relative overflow-hidden group cursor-pointer hover:shadow-md transition">
                            <div class="relative z-10">
                                <h3 class="font-bold text-slate-900 text-xl">Fashion & Kriya</h3>
                                <p class="text-slate-500 text-sm mt-1">Tenun, Aksesoris, Kain</p>
                            </div>
                            <img src="{{ asset('images/shop/nun.png') }}" 
                                 class="absolute right-0 bottom-0 w-32 h-32 object-contain opacity-80 group-hover:scale-110 transition">
                        </div>
                        <div class="flex-1 rounded-2xl bg-green-50 border border-green-100 p-6 flex flex-col justify-center relative overflow-hidden group cursor-pointer hover:shadow-md transition">
                            <div class="relative z-10">
                                <h3 class="font-bold text-slate-900 text-xl">Makanan & Minuman</h3>
                                <p class="text-slate-500 text-sm mt-1">Jamu, Kopi, Snack</p>
                            </div>
                            <img src="https://images.unsplash.com/photo-1597481499750-3e6b22637e12?auto=format&fit=crop&w=300&q=80" 
                                 class="absolute -right-4 -bottom-4 w-40 h-40 object-contain opacity-80 group-hover:scale-110 transition transform rotate-12">
                        </div>
                    </div>
                </div>

                <div class="relative max-w-4xl mx-auto -mb-16 z-20">
                    <div class="bg-white p-2 rounded-full shadow-xl border border-gray-100 flex items-center">
                        <div class="pl-6 text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" placeholder="Cari produk UMKM Bengkala (Contoh: Jamu, Kain Tenun)..." 
                               class="w-full border-none focus:ring-0 text-gray-700 placeholder-gray-400 px-4 py-3 bg-transparent text-lg">
                        <button class="bg-[#0F244A] text-white px-8 py-3 rounded-full font-bold hover:bg-blue-800 transition">
                            Cari
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-2xl font-bold text-slate-900">Rekomendasi Untukmu</h3>
                <div class="flex gap-2 text-sm">
                    <span class="text-slate-400">Urutkan:</span>
                    <select class="bg-transparent border-none text-slate-700 font-bold p-0 focus:ring-0 cursor-pointer">
                        <option>Terbaru</option>
                        <option>Termurah</option>
                        <option>Terlaris</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($products as $product)
                <div class="bg-white rounded-2xl border border-gray-100 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col h-full group">
                    
                    <div class="relative aspect-square overflow-hidden bg-gray-100">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <img src="https://source.unsplash.com/400x400/?product,craft&sig={{ $product->id }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @endif
                        
                        <div class="absolute top-3 left-3">
                            @if($product->computed_stock > 0)
                                <span class="bg-white/90 backdrop-blur text-green-700 text-[10px] font-bold px-2 py-1 rounded shadow-sm border border-green-100">
                                    Stok: {{ $product->computed_stock }}
                                </span>
                            @else
                                <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm">Habis</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 flex flex-col flex-1">
                        <div class="flex items-center gap-1 mb-2">
                            <svg class="w-3 h-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                            <span class="text-xs text-slate-500 truncate">{{ $product->umkm->name ?? 'UMKM Bengkala' }}</span>
                        </div>

                        <h3 class="font-bold text-slate-900 text-base mb-1 line-clamp-2 leading-snug group-hover:text-blue-600 transition">
                            {{ $product->name }}
                        </h3>

                        <div class="mt-auto pt-2">
                            <span class="block text-lg font-extrabold text-[#0F244A]">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </span>
                        </div>
                        
                        <div class="mt-4">
                            @php
                                $waNumber = '6281234567890'; // Default number
                                $message = "Halo, saya tertarik membeli produk: *{$product->name}* seharga Rp " . number_format($product->price, 0, ',', '.') . ". Apakah stok masih ada?";
                                $waLink = "https://wa.me/{$waNumber}?text=" . urlencode($message);
                            @endphp

                            <a href="{{ $waLink }}" target="_blank" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-green-200">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.6 1.953.229 3.618 1.195 5.181zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                <span>Beli Sekarang</span>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-20 text-center">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-cart-2130356-1800917.png" class="w-64 h-64 object-contain mx-auto opacity-50">
                    <h3 class="text-xl font-bold text-slate-500 mt-4">Produk belum tersedia</h3>
                    <p class="text-slate-400">Silakan cek kembali nanti.</p>
                </div>
                @endforelse
            </div>
            
            <div class="mt-16 text-center">
                <button class="px-8 py-3 bg-white border border-gray-200 text-slate-600 font-bold rounded-full hover:bg-gray-50 transition shadow-sm">
                    Muat Lebih Banyak
                </button>
            </div>
        </div>

    </div>
</x-guest-layout>