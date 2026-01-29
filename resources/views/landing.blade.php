<x-guest-layout>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>

    <section id="beranda" class="relative h-screen overflow-hidden" 
             x-data="{ 
                active: 0, 
                images: [
                    '{{ asset('images/slider/1.jpg') }}', 
                    '{{ asset('images/slider/2.jpg') }}', 
                    '{{ asset('images/slider/3.jpg') }}'
                ],
                init() { setInterval(() => { this.active = (this.active + 1) % this.images.length; }, 5000); }
             }">
        
        <template x-for="(img, index) in images" :key="index">
            <div class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000 ease-in-out"
                 :style="`background-image: url('${img}')`"
                 x-show="active === index"
                 x-transition:enter="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="opacity-100"
                 x-transition:leave-end="opacity-0">
            </div>
        </template>

        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/30 to-slate-900/90"></div>

        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
            <span class="inline-block py-1 px-4 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-xs font-bold tracking-widest mb-6 animate-bounce">
                WELCOME TO BENGKALA VILLAGE
            </span>
            <h1 class="text-5xl md:text-7xl font-extrabold text-white leading-tight drop-shadow-lg mb-6">
                Desa <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Kolok</span> <br> 
                Yang Mendunia
            </h1>
            <p class="text-lg md:text-xl text-gray-200 max-w-2xl mx-auto mb-10 font-light">
                Jelajahi keunikan budaya bisu tuli yang harmonis, kearifan lokal yang autentik, dan produk UMKM berkualitas dari tangan warga.
            </p>
            <div class="flex gap-4">
                <a href="#profil" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-full font-bold transition shadow-lg shadow-blue-900/50">
                    Jelajahi Desa
                </a>
                <a href="#360view" class="px-8 py-3 bg-white/10 hover:bg-white/20 backdrop-blur border border-white/30 text-white rounded-full font-bold transition flex items-center gap-2">
                     Virtual 360°
                </a>
            </div>
        </div>

        <div class="absolute bottom-0 w-full leading-none z-20">
            <svg class="block w-full h-16 md:h-24 text-slate-50" viewBox="0 0 1440 320" preserveAspectRatio="none">
                <path fill="currentColor" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,250.7C960,235,1056,181,1152,165.3C1248,149,1344,171,1392,181.3L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            </svg>
        </div>
    </section>

    <section id="profil" class="py-24 bg-slate-50 relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-64 h-64 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>

        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center relative z-10">
            
            <div class="order-2 lg:order-1">
                <div class="grid grid-cols-12 grid-rows-2 gap-4 h-[450px] md:h-[550px] relative">
                    
                    <div class="col-span-7 row-span-2 relative rounded-3xl overflow-hidden shadow-lg group">
                        <img src="{{ asset('images/profil/sd2.jpg') }}" alt="Desa Bengkala" 
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent transition group-hover:bg-black/10"></div>
                    </div>

                    <div class="col-span-5 row-span-1 relative rounded-3xl overflow-hidden shadow-md group">
                        <img src="{{ asset('images/profil/jangerkoloks.jpg') }}" alt="Aktivitas Warga" 
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    </div>

                    <div class="col-span-5 row-span-1 relative rounded-3xl overflow-hidden shadow-md group">
                        <img src="{{ asset('images/profil/bengkala.jpg') }}" alt="Budaya Bengkala" 
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    </div>

                    
                </div>
            </div>

            <div class="order-1 lg:order-2">
                <span class="inline-block py-1 px-3 rounded-full bg-blue-100 text-blue-600 text-xs font-bold tracking-widest uppercase mb-4">Tentang Kami</span>
                <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900 mb-8 leading-tight">
                    Desa Bengkala <br> 
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-blue-400">Harmoni dalam Kesunyian</span>
                </h2>
                
                <div class="space-y-6 text-slate-600 text-lg leading-relaxed mb-10">
                    <p>
                        Terletak di Kabupaten Buleleng, Bengkala dikenal dunia sebagai "Desa Tuli" di mana warga dengar dan tuli hidup berdampingan menggunakan bahasa isyarat unik yang disebut <strong class="text-slate-800 font-bold">Kata Kolok</strong>.
                    </p>
                    <p>
                        Kami bukan hanya sekadar destinasi wisata, tapi laboratorium sosial hidup tentang inklusivitas, toleransi, dan pemberdayaan ekonomi masyarakat melalui UMKM digital.
                    </p>
                </div>
                
                <div class="grid grid-cols-3 gap-4 md:gap-6 text-center">
                    <div class="p-5 bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition group">
                        <span class="block text-3xl md:text-4xl font-extrabold text-blue-600 group-hover:scale-110 transition">3rb+</span>
                        <span class="text-xs md:text-sm font-medium text-slate-500 uppercase tracking-wider mt-1 inline-block">Penduduk</span>
                    </div>
                    <div class="p-5 bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition group">
                        <span class="block text-3xl md:text-4xl font-extrabold text-blue-600 group-hover:scale-110 transition">20+</span>
                        <span class="text-xs md:text-sm font-medium text-slate-500 uppercase tracking-wider mt-1 inline-block">UMKM Aktif</span>
                    </div>
                    <div class="p-5 bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition group">
                        <span class="block text-3xl md:text-4xl font-extrabold text-blue-600 group-hover:scale-110 transition">100%</span>
                        <span class="text-xs md:text-sm font-medium text-slate-500 uppercase tracking-wider mt-1 inline-block">Inklusif</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="360view" class="py-20 bg-[#0F172A] relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
    
    <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
        {{-- <h2 class="text-3xl font-bold text-white mb-2">Virtual Tour 360°</h2>
        <p class="text-slate-400 mb-8">Rasakan sensasi berdiri langsung di depan Gerbang Desa Bengkala.</p> --}}
        
        <div class="w-full h-[500px] rounded-3xl shadow-2xl border-4 border-white/10 overflow-hidden relative z-20 bg-slate-800">
            <iframe 
                src="/tour.html" 
                class="w-full h-full"
                frameborder="0" 
                allowfullscreen>
            </iframe>
        </div>
        
        <p class="mt-4 text-xs text-slate-500 flex items-center justify-center gap-2">
            <span>🖱️</span> Geser mouse atau sentuh layar untuk melihat sekeliling.
        </p>

        <div class="mt-6 flex justify-center">
            <a href="https://www.google.com/results?search_query=bengkala" target="_blank" 
               class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white border border-slate-200 text-slate-700 text-sm hover:border-blue-500 hover:text-blue-600 transition shadow-sm group font-medium hover:scale-105 transform duration-200">
                <span>Jelajahi Virtual Tour</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </a>
        </div>

    </div>
</section>               

        <section id="culture" class="py-24 bg-slate-50 relative overflow-hidden">
        
        <div class="absolute inset-0 opacity-[0.03] bg-[url('https://www.transparenttextures.com/patterns/diagonal-stripes.png')]"></div>
        
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-200/40 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-cyan-100/40 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
                <div class="max-w-2xl">
                    <span class="inline-block py-1 px-3 rounded-full bg-blue-100 text-blue-600 text-xs font-bold tracking-widest uppercase mb-3">
                        Warisan Budaya Tak Benda
                    </span>
                    <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900 leading-tight">
                        Janger Kolok
                    </h2>
                    <p class="text-slate-500 mt-4 text-lg leading-relaxed">
                        Harmoni dalam kesunyian. Tarian unik dunia yang diciptakan dan ditarikan oleh warga Tuli Bengkala, mengandalkan ritme visual dan kekompakan hati.
                    </p>
                </div>
                
                <a href="https://www.youtube.com/results?search_query=janger+kolok" target="_blank" class="px-6 py-3 rounded-full bg-white border border-slate-200 text-slate-700 hover:border-blue-500 hover:text-blue-600 transition shadow-sm flex items-center gap-2 group font-medium">
                    <span>Lihat Dokumenter</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <div class="lg:col-span-8">
                    <div class="relative w-full aspect-video rounded-3xl overflow-hidden shadow-xl border-4 border-white group">
                        <iframe 
                            class="w-full h-full object-cover"
                            src="https://www.youtube.com/embed/1MkQIYgedGw?si=E_qG9R5wll5bbfKV" 
                            title="Janger Kolok Performance" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            allowfullscreen>
                        </iframe>
                    </div>

                    <div class="mt-6 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex gap-5 items-start">
                        <div class="hidden sm:flex flex-col items-center gap-2 min-w-[60px] text-blue-600">
                            <span class="text-3xl"></span>
                            <div class="h-10 w-0.5 bg-blue-100"></div>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-lg mb-2">Filosofi Gerak & Irama</h4>
                            <p class="text-slate-600 text-sm leading-relaxed mb-4">
                                Berbeda dengan tari Bali pada umumnya yang dipandu gamelan, Janger Kolok murni mengandalkan <b>kode visual</b> dan "Mudra" (gerakan tangan). Para penari wanita (Janger) dan pria (Kecak) saling memberikan isyarat mata untuk menjaga tempo, menciptakan pertunjukan magis yang membuktikan bahwa seni melampaui batas suara.
                            </p>
                            <div class="flex gap-4 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <span class="flex items-center gap-1"><div class="w-2 h-2 bg-blue-500 rounded-full"></div> 16 Penari</span>
                                <span class="flex items-center gap-1"><div class="w-2 h-2 bg-purple-500 rounded-full"></div> Kostum Tradisional</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-col gap-4 h-full">
                    
                    <div class="relative h-48 rounded-2xl overflow-hidden group border-2 border-white shadow-md">
                        <img src="{{ asset('images/jangerkolok/tatarias.jpg') }}"
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent p-4 flex items-end opacity-0 group-hover:opacity-100 transition">
                            <p class="text-xs font-bold text-white">Tata Rias Khas</p>
                        </div>
                    </div>

                    <div class="relative h-48 rounded-2xl overflow-hidden group border-2 border-white shadow-md">
                        <img src="{{ asset('images/jangerkolok/formasi.jpg') }}" 
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent p-4 flex items-end opacity-0 group-hover:opacity-100 transition">
                            <p class="text-xs font-bold text-white">Formasi Penari</p>
                        </div>
                    </div>

                    <div class="flex-1 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl p-6 text-white flex flex-col justify-center items-center text-center shadow-lg relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mr-4 -mt-4 w-20 h-20 bg-white/10 rounded-full blur-xl"></div>
                        <span class="text-4xl font-extrabold mb-1">75+</span>
                        <span class="text-sm text-blue-100 font-medium">Tahun Lestari</span>
                        <p class="text-xs text-blue-200 mt-2 px-2">Diakui sebagai Warisan Budaya Tak Benda oleh UNESCO.</p>
                    </div>

                </div>
            </div>
            
        </div>
    </section>

<section id="umkm" class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">
            
            <div class="text-center mb-20">
                <span class="inline-block py-1 px-3 rounded-full bg-blue-50 text-blue-600 text-xs font-bold tracking-widest uppercase mb-4 border border-blue-100">
                    Karya & Karsa
                </span>
                <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900">
                    Permata Tersembunyi Bengkala
                </h2>
                <p class="text-slate-500 mt-6 max-w-2xl mx-auto text-lg leading-relaxed">
                    Kami bangga mempersembahkan dua pilar ekonomi kreatif desa yang memadukan tradisi leluhur dengan semangat inklusivitas.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center mb-32">
                
                <div class="lg:col-span-5 space-y-8 order-2 lg:order-1">
                    <div class="flex items-center gap-3">
                        
                        <h3 class="text-3xl font-bold text-slate-900">Jamu Sakuntala</h3>
                    </div>
                    
                    <p class="text-slate-600 text-lg leading-relaxed">
                        Lebih dari sekadar minuman, Jamu Sakuntala adalah warisan kesehatan yang diracik dari rempah-rempah segar hasil bumi tanah Bengkala. Diproses secara tradisional dan alami tanpa bahan pengawet, setiap tetesnya membawa kesegaran alami.
                    </p>

                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                        <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <span>✨</span> Keunggulan Produk:
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span><strong class="text-slate-800">100% Organik:</strong> Kunyit, jahe, dan temulawak pilihan.</span>
                            </li>
                            <li class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span><strong class="text-slate-800">Gula Aren Asli:</strong> Pemanis alami rendah indeks glikemik.</span>
                            </li>
                            <li class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span><strong class="text-slate-800">Varian Lengkap:</strong> Beras Kencur, Kunyit Asam, & Gula Asem.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <span class="text-3xl font-bold text-blue-600">Rp 10.000<span class="text-sm text-slate-400 font-normal">/botol</span></span>
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Best Seller</span>
                    </div>
                </div>

                <div class="lg:col-span-7 h-[500px] grid grid-cols-2 grid-rows-2 gap-4 order-1 lg:order-2">
                    <div class="row-span-2 relative group overflow-hidden rounded-3xl">
                        <img src="{{ asset('images/jamu/sakuntala1.png') }}"
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                        <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition"></div>
                    </div>
                    <div class="relative group overflow-hidden rounded-3xl">
                        <img src= "{{ asset('images/jamu/proses.png') }}"
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    </div>
                    <div class="relative group overflow-hidden rounded-3xl">
                        <img src= "{{ asset('images/jamu/display.png') }}"
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <div class="lg:col-span-7 h-[500px] grid grid-cols-6 grid-rows-2 gap-4 order-1">
                    <div class="col-span-6 row-span-1 relative group overflow-hidden rounded-3xl">
                        <img src= "{{ asset('images/tenun/kegiatan.png') }}"
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    </div>
                    <div class="col-span-3 row-span-1 relative group overflow-hidden rounded-3xl">
                        <img src= "{{ asset('images/tenun/tenun.png') }}"
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    </div>
                    <div class="col-span-3 row-span-1 relative group overflow-hidden rounded-3xl">
                        <img src="{{ asset('images/tenun/tenun2.png') }}"
                             class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-8 order-2">
                    <div class="flex items-center gap-3">
                        
                        <h3 class="text-3xl font-bold text-slate-900">Tenun Ikat Bengkala</h3>
                    </div>
                    
                    <p class="text-slate-600 text-lg leading-relaxed">
                        Mahakarya kain yang lahir dari kesunyian. Ditenun oleh tangan-tangan terampil warga Tuli (Kolok) menggunakan alat tenun bukan mesin (ATBM). Setiap motif menceritakan filosofi kehidupan desa yang harmonis.
                    </p>

                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100">
                        <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <span>✨</span> Keunikan Produk:
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-purple-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span><strong class="text-slate-800">Inklusif:</strong> 100% dibuat oleh komunitas Difabel.</span>
                            </li>
                            <li class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-purple-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span><strong class="text-slate-800">Pewarna Alam:</strong> Menggunakan akar & dedaunan lokal.</span>
                            </li>
                            <li class="flex items-start gap-3 text-slate-600">
                                <svg class="w-5 h-5 text-purple-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span><strong class="text-slate-800">Eksklusif:</strong> Motif tidak diproduksi massal.</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="flex items-center gap-4 pt-4">
                        <span class="text-3xl font-bold text-blue-600">Rp 500.000<span class="text-sm text-slate-400 font-normal">/meter</span></span>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">Limited</span>
                    </div>
                </div>
            </div>

            <div class="mt-28 text-center relative z-10">
                <div class="inline-block relative group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full blur opacity-25 group-hover:opacity-100 transition duration-1000 group-hover:duration-200"></div>
                    <a href="{{ route('shop') }}" class="relative flex items-center gap-4 px-12 py-5 bg-white rounded-full leading-none shadow-xl divide-x divide-gray-200">
                        <span class="flex items-center gap-2 text-slate-800 font-bold text-lg">
                            
                            <span class="pr-2">Ayo Belanja</span>
                        </span>
                        <span class="pl-4 text-blue-600 group-hover:text-purple-600 transition font-medium text-sm">
                            Lihat Stok & Harga &rarr;
                        </span>
                    </a>
                </div>
            </div>

        </div>
    </section>

    <section class="py-24 bg-white relative">
        <div class="max-w-4xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-900">Kata Mereka</h2>
                <p class="text-slate-500 mt-2">Apa kata pengunjung dan warga tentang Desa Bengkala?</p>
            </div>

            <div class="space-y-6 mb-12 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($testimonials as $comment)
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 flex gap-4 transition hover:shadow-md">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->name) }}&background=random&color=fff" 
                         class="w-12 h-12 rounded-full border-2 border-white shadow-sm">
                    
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="font-bold text-slate-900 capitalize">{{ $comment->name }}</h4>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold 
                                {{ $comment->user_id ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }}">
                                {{ $comment->role }}
                            </span>
                            <span class="text-[10px] text-slate-400">• {{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-slate-600 text-sm mt-2 leading-relaxed">"{{ $comment->message }}"</p>
                    </div>
                </div>
                @endforeach

                @if($testimonials->isEmpty())
                    <p class="text-center text-slate-400 italic">Belum ada komentar. Jadilah yang pertama!</p>
                @endif
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100 relative overflow-hidden" id="form-komentar">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 z-0"></div>
                
                <h3 class="text-xl font-bold text-slate-900 mb-6 relative z-10">Tinggalkan Pesan</h3>

                @if(session('success'))
                    <div class="bg-green-100 text-green-700 p-3 rounded-xl mb-4 text-sm font-bold relative z-10">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('comment.store') }}" method="POST" class="relative z-10 space-y-4">
                    @csrf
                    
                    @auth
                        <div class="flex items-center gap-3 mb-2 bg-blue-50 p-3 rounded-xl border border-blue-100">
                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm text-slate-700">Berkomentar sebagai <span class="font-bold">{{ Auth::user()->name }}</span></p>
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text" name="name" required placeholder="Nama Lengkap" 
                                   class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 placeholder-slate-400">
                            <input type="email" name="email" required placeholder="Email (Tidak akan dipublikasikan)" 
                                   class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 placeholder-slate-400">
                        </div>
                    @endauth

                    <textarea name="message" required rows="3" placeholder="Tulis pengalaman, dukungan, atau masukan Anda..." 
                              class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 placeholder-slate-400"></textarea>
                    
                    <button type="submit" class="bg-slate-900 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-600 transition w-full md:w-auto shadow-lg">
                        Kirim Komentar
                    </button>
                </form>
            </div>
        </div>
    </section>

    <footer class="bg-[#0b1120] text-slate-300 pt-20 pb-10 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-4 gap-12 mb-16">
            
            <div class="col-span-1 md:col-span-1">
                <div class="flex items-center gap-2 mb-6">
                    <img src="{{ asset('images/logo.png') }}" class="h-8 w-auto invert brightness-0 grayscale">
                    <span class="font-bold text-xl text-white">BISA</span>
                </div>
                <p class="text-sm leading-relaxed mb-6 text-slate-400">
                    Platform digital terintegrasi untuk pemberdayaan Desa Bengkala, Buleleng, Bali. Menghubungkan potensi lokal dengan dunia global.
                </p>
               
            </div>

            <div>
                <h4 class="text-white font-bold mb-6">Jelajahi</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="#beranda" class="hover:text-blue-500 transition">Beranda</a></li>
                    <li><a href="#profil" class="hover:text-blue-500 transition">Profil Desa</a></li>
                    <li><a href="#umkm" class="hover:text-blue-500 transition">Produk UMKM</a></li>
                    <li><a href="#" class="hover:text-blue-500 transition">Virtual Tour</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-blue-500 transition">Login Mitra</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-white font-bold mb-6">Hubungi Kami</h4>
                <ul class="space-y-4 text-sm">
                    <li class="flex gap-3">
                        <span class="text-blue-500">📍</span>
                        <span>Desa Bengkala, Kec. Kubutambahan, Kab. Buleleng, Bali</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-blue-500">📧</span>
                        <span>bisadesabengkala@gmail.com</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-blue-500">📞</span>
                        <span>+62 877 5231 8457</span>
                    </li>
                </ul>
            </div>

            <div class="col-span-1 md:col-span-1">
                <h4 class="text-white font-bold mb-6">Lokasi Desa</h4>
                <div class="w-full h-48 bg-slate-800 rounded-xl overflow-hidden shadow-lg border border-slate-700">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.424363294767!2d115.1844973147804!3d-8.159989994126388!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd19a5bd0085731%3A0x5030bfbca831200!2sBengkala%2C%20Kubutambahan%2C%20Buleleng%20Regency%2C%20Bali!5e0!3m2!1sen!2sid!4v1673840000000!5m2!1sen!2sid" 
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>


    </footer>

</x-guest-layout>