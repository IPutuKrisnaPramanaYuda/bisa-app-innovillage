<x-app-layout>
    <x-slot name="header">
        Pengaturan
    </x-slot>

    <div class="max-w-4xl mx-auto py-10 px-6">
        <h1 class="text-3xl font-bold text-[#0F244A] mb-8">Setelan</h1>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center shadow-sm">
                <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <div>
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center shadow-sm">
                <svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <strong class="font-bold">Gagal!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if($umkm)
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm mb-6">
            <div class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-100">
                <span class="text-2xl">🏪</span>
                <h3 class="font-bold text-xl text-[#0F244A]">Profil Toko (UMKM)</h3>
            </div>

            <form action="{{ route('settings.update-shop') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="flex items-start gap-6">
                    <div class="shrink-0">
                        <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-100 border-2 border-dashed border-gray-300 relative group">
                            @if($umkm->image)
                                <img src="{{ asset('storage/' . $umkm->image) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-2xl">🏪</div>
                            @endif
                            
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition cursor-pointer">
                                <span class="text-white text-xs font-bold">Ganti</span>
                            </div>
                            <input type="file" name="image" class="absolute inset-0 opacity-0 cursor-pointer" accept="image/*">
                        </div>
                        <p class="text-xs text-gray-500 mt-2 text-center">Klik untuk ganti</p>
                    </div>

                    <div class="flex-1 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko</label>
                            <input type="text" name="name" value="{{ old('name', $umkm->name) }}" required
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
                            <textarea name="description" rows="2"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description', $umkm->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp / Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $umkm->phone) }}" placeholder="08123456789"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <input type="text" name="address" value="{{ old('address', $umkm->address) }}" placeholder="Jl. Desa Bengkala No..."
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <button type="submit" class="bg-[#0F244A] hover:bg-blue-900 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg transition transform hover:scale-105">
                        Simpan Perubahan Toko
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="bg-yellow-50 p-6 rounded-2xl border border-yellow-200 shadow-sm mb-6 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-lg text-yellow-800">Anda belum memiliki Toko</h3>
                <p class="text-yellow-600 text-sm">Buat toko sekarang untuk mulai berjualan dan mengelola stok.</p>
            </div>
            <a href="{{ route('umkm.create') }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg font-bold hover:bg-yellow-600 transition">Buat Toko</a>
        </div>
        @endif

        

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="font-bold text-lg mb-4 text-gray-800">Akun Pengguna</h3>
            <div class="space-y-4">
                <a href="{{ route('profile.edit') }}" class="block p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition flex justify-between items-center group">
                    <span class="font-medium text-gray-700 group-hover:text-gray-900">Edit Profil User & Password</span>
                    <span class="text-gray-400 group-hover:text-gray-600">➔</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>