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

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm mb-6 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-lg text-gray-800">Tema Gelap (Dark Mode)</h3>
                <p class="text-gray-500 text-sm">Ubah tampilan aplikasi menjadi gelap untuk kenyamanan mata.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" value="" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0F244A]"></div>
            </label>
        </div>

        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-100 shadow-sm mb-6">
            <div class="flex justify-between items-start">
                <div class="pr-8">
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="font-bold text-lg text-[#0F244A]">Mode Kontributor</h3>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">Beta</span>
                    </div>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Aktifkan fitur ini untuk berkontribusi pada dataset AI Global.
                        <br>
                        <span class="text-xs text-red-500 font-semibold mt-1 block">
                            ⚠ Perhatian: Jurnal/Dokumen yang Anda upload akan menjadi dataset publik (Open Source) dan dapat diakses oleh "Research Tool" seluruh pengguna lain.
                        </span>
                    </p>
                </div>
                
                <form action="{{ route('settings.toggle-contributor') }}" method="POST">
    @csrf
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" name="is_contributor" onchange="this.form.submit()" class="sr-only peer" {{ auth()->user()->is_contributor ? 'checked' : '' }}>
        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
    </label>
</form>
            </div>

            @if(auth()->user()->is_contributor)
                <div class="mt-6 pt-4 border-t border-blue-200">
                    <a href="{{ route('contributor.upload') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-blue-300 rounded-lg text-blue-700 hover:bg-blue-50 transition text-sm font-medium shadow-sm w-full sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Upload Jurnal / Dataset
                    </a>
                </div>
            @endif
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="font-bold text-lg mb-4 text-gray-800">Akun</h3>
            <div class="space-y-4">
                <a href="{{ route('profile.edit') }}" class="block p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition flex justify-between items-center group">
                    <span class="font-medium text-gray-700 group-hover:text-gray-900">Edit Profil & Password</span>
                    <span class="text-gray-400 group-hover:text-gray-600">➔</span>
                </a>
                
                </div>
        </div>
    </div>
</x-app-layout>