<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrasi UMKM Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <h3 class="text-lg font-bold mb-4">Halo, {{ Auth::user()->name }}! 👋</h3>
                    <p class="mb-6">Sebelum menggunakan fitur AI dan ERP, silakan daftarkan usaha Anda terlebih dahulu.</p>

                    <form method="POST" action="{{ route('umkm.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Usaha (UMKM)</label>
                            <input type="text" name="name" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1" placeholder="Contoh: Jamu Sakuntala" required />
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Alamat Lengkap</label>
                            <textarea name="address" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1" rows="3" required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Deskripsi Singkat</label>
                            <textarea name="description" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1" rows="3" placeholder="Jual jamu tradisional khas Bengkala..."></textarea>
                        </div>

                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Simpan & Buka Toko
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>