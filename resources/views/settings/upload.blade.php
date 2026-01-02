<x-app-layout>
    <x-slot name="header">
        Upload Dataset Kontributor
    </x-slot>

    <div class="max-w-4xl mx-auto py-10 px-6">
        <div class="mb-6">
            <a href="{{ route('settings.index') }}" class="text-blue-600 hover:underline text-sm">← Kembali ke Pengaturan</a>
        </div>

        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                </div>
                <h1 class="text-2xl font-bold text-[#0F244A]">Upload Jurnal / Dataset</h1>
                <p class="text-gray-500 mt-2">Kontribusikan data PDF/Doc untuk mencerdaskan AI kita bersama.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 text-sm">
                    <strong class="font-bold block mb-1">Gagal Upload!</strong>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('contributor.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Dokumen</label>
                    <input type="text" name="title" required class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: Strategi Pemasaran UMKM Digital 2025" value="{{ old('title') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat (Opsional)</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Jelaskan isi dokumen ini...">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Dokumen</label>
                    
                    <div class="relative flex items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-lg bg-gray-50 hover:bg-gray-100 transition">
                        
                        <div class="flex flex-col items-center justify-center pt-5 pb-6 pointer-events-none">
                            <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500"><span class="font-bold">Klik untuk upload</span> atau drag and drop</p>
                            <p class="text-xs text-gray-500">PDF, DOCX, TXT (MAX. 10MB)</p>
                        </div>

                        <input id="dropzone-file" name="document" type="file" 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" 
                               accept=".pdf,.doc,.docx,.txt" required />
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#0F244A] text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition shadow-lg">
                    Kirim Kontribusi 🚀
                </button>
            </form>
            </div>
    </div>
</x-app-layout>