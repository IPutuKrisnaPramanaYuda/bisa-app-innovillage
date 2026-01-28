<div x-data="{ 
    open: false,
    
   
    x: localStorage.getItem('a11y_x') || 20,
    y: localStorage.getItem('a11y_y') || window.innerHeight - 80,
    isDragging: false,
    startX: 0,
    startY: 0,
    initialLeft: 0,
    initialTop: 0,

 
    fontSize: localStorage.getItem('a11y_fontSize') || 100,
    grayscale: localStorage.getItem('a11y_grayscale') === 'true',
    contrast: localStorage.getItem('a11y_contrast') === 'true',
    invert: localStorage.getItem('a11y_invert') === 'true',
    readableFont: localStorage.getItem('a11y_readableFont') === 'true',
    highlightLinks: localStorage.getItem('a11y_highlightLinks') === 'true',
    bigCursor: localStorage.getItem('a11y_bigCursor') === 'true',
    spacing: localStorage.getItem('a11y_spacing') === 'true',
    hideImages: localStorage.getItem('a11y_hideImages') === 'true',
    
    init() {
        this.applySettings();
        if(this.x < 0) this.x = 20;
        if(this.y < 0) this.y = 20;
    },

    // --- LOGIKA DRAG & DROP (SAMA SEPERTI SEBELUMNYA) ---
    startDrag(e) {
        this.isDragging = false;
        this.startX = e.clientX || e.touches[0].clientX;
        this.startY = e.clientY || e.touches[0].clientY;
        this.initialLeft = parseInt(this.x);
        this.initialTop = parseInt(this.y);
        
        const moveHandler = (e) => this.drag(e);
        const upHandler = () => this.stopDrag(moveHandler, upHandler);

        document.addEventListener('mousemove', moveHandler);
        document.addEventListener('touchmove', moveHandler);
        document.addEventListener('mouseup', upHandler);
        document.addEventListener('touchend', upHandler);
    },

    drag(e) {
        this.isDragging = true;
        e.preventDefault(); 
        const clientX = e.clientX || e.touches[0].clientX;
        const clientY = e.clientY || e.touches[0].clientY;
        this.x = this.initialLeft + (clientX - this.startX);
        this.y = this.initialTop + (clientY - this.startY);
    },

    stopDrag(moveHandler, upHandler) {
        document.removeEventListener('mousemove', moveHandler);
        document.removeEventListener('touchmove', moveHandler);
        document.removeEventListener('mouseup', upHandler);
        document.removeEventListener('touchend', upHandler);
        localStorage.setItem('a11y_x', this.x);
        localStorage.setItem('a11y_y', this.y);
    },

    handleToggle() {
        if (!this.isDragging) {
            this.open = !this.open;
        }
    },
    // ---------------------------

    adjustFont(amount) {
        this.fontSize = parseInt(this.fontSize) + amount;
        if(this.fontSize < 100) this.fontSize = 100;
        if(this.fontSize > 200) this.fontSize = 200;
        this.saveSettings();
    },

    toggleFeature(feature) {
        this[feature] = !this[feature];

        // LOGIKA PENCEGAH BLACKOUT (PENTING!)
        // Jika High Contrast nyala, MATIKAN fitur warna lain biar gak bentrok hitam
        if(feature === 'contrast' && this.contrast) { 
            this.invert = false; 
            this.grayscale = false; 
        }
        // Jika Grayscale/Invert nyala, matikan Contrast
        if((feature === 'grayscale' || feature === 'invert') && this[feature]) {
            this.contrast = false;
        }
        
        this.saveSettings();
    },

    reset() {
        this.fontSize = 100;
        this.grayscale = false;
        this.contrast = false;
        this.invert = false;
        this.readableFont = false;
        this.highlightLinks = false;
        this.bigCursor = false;
        this.spacing = false;
        this.hideImages = false;
        this.x = 20;
        this.y = window.innerHeight - 80;
        this.saveSettings();
    },

    saveSettings() {
        localStorage.setItem('a11y_fontSize', this.fontSize);
        localStorage.setItem('a11y_grayscale', this.grayscale);
        localStorage.setItem('a11y_contrast', this.contrast);
        localStorage.setItem('a11y_invert', this.invert);
        localStorage.setItem('a11y_readableFont', this.readableFont);
        localStorage.setItem('a11y_highlightLinks', this.highlightLinks);
        localStorage.setItem('a11y_bigCursor', this.bigCursor);
        localStorage.setItem('a11y_spacing', this.spacing);
        localStorage.setItem('a11y_hideImages', this.hideImages);
        this.applySettings();
    },

    applySettings() {
        const html = document.documentElement;
        const body = document.body;
        
        html.style.fontSize = this.fontSize + '%';
        
        body.classList.toggle('a11y-contrast', this.contrast);
        body.classList.toggle('a11y-readable-font', this.readableFont);
        body.classList.toggle('a11y-highlight-links', this.highlightLinks);
        body.classList.toggle('a11y-spacing', this.spacing);
        body.classList.toggle('a11y-big-cursor', this.bigCursor);
        body.classList.toggle('a11y-hide-images', this.hideImages);
    }
}" 
x-bind:style="`left: ${x}px; top: ${y}px;`"
class="fixed z-[9999] font-sans touch-none a11y-exclude"> 
<div class="fixed inset-0 pointer-events-none z-[9998]"
         style="transition: backdrop-filter 0.3s;"
         :style="`
            backdrop-filter: 
                ${grayscale ? 'grayscale(100%)' : ''} 
                ${invert ? 'invert(100%)' : ''};
            -webkit-backdrop-filter: 
                ${grayscale ? 'grayscale(100%)' : ''} 
                ${invert ? 'invert(100%)' : ''};
            display: ${contrast ? 'none' : 'block'}; 
         `">
         </div>

    <style>
        /* High Contrast - DIBUAT LEBIH PINTAR */
        /* Hanya warnai elemen yang BUKAN widget (.a11y-exclude) */
        .a11y-contrast *:not(.a11y-exclude):not(.a11y-exclude *) { 
            background-color: #000 !important; 
            color: #ffff00 !important; 
            border-color: #ffff00 !important;
            box-shadow: none !important;
        }
        .a11y-contrast img:not(.a11y-exclude img) { 
            filter: grayscale(100%) contrast(150%); 
        }
        .a11y-contrast { 
            background-color: #000 !important; 
        }

        /* Readable Font */
        .a11y-readable-font *:not(.a11y-exclude *) { font-family: 'Verdana', 'Arial', sans-serif !important; letter-spacing: 0.5px; }

        /* Highlight Links */
        .a11y-highlight-links a:not(.a11y-exclude a) { background: #ffeb3b !important; color: #000 !important; text-decoration: underline !important; font-weight: bold !important; border: 2px solid #000; }
        
        /* Text Spacing */
        .a11y-spacing *:not(.a11y-exclude *) { letter-spacing: 2px !important; line-height: 1.8 !important; word-spacing: 5px !important; }

        /* Big Cursor */
        .a11y-big-cursor, .a11y-big-cursor * { cursor: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewport="0 0 48 48" style="fill:black;stroke:white;stroke-width:2px;"><path d="M10 2l14 36-6-10 12-10z"/></svg>'), auto !important; }

        /* Hide Images */
        .a11y-hide-images img:not(.a11y-exclude img), 
        .a11y-hide-images svg:not(.a11y-exclude svg), 
        .a11y-hide-images video { opacity: 0 !important; visibility: hidden !important; }
    </style>

    <button @mousedown="startDrag($event)" 
            @touchstart="startDrag($event)"
            @click="handleToggle()" 
            class="w-14 h-14 bg-[#0F244A] hover:bg-blue-800 text-white rounded-full shadow-2xl flex items-center justify-center transition-transform transform active:scale-95 border-4 border-white focus:outline-none focus:ring-4 focus:ring-blue-300 group cursor-move"
            style="background-color: #0F244A !important; color: white !important; border-color: white !important;" 
            title="Tarik untuk memindahkan, Klik untuk menu">
        <svg class="w-8 h-8 group-hover:rotate-12 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 14c1.49-1.08 2-3.659 2-5.304 0-2.218-1.545-3.696-3.5-3.696-1.955 0-3.5 1.478-3.5 3.696 0 1.645.51 4.224 2 5.304m0 0V20m0-6l-8-6-4 4 3 6m5-2v8"></path></svg>
        <span class="absolute -top-1 -right-1 bg-gray-500 text-white rounded-full p-0.5 border-2 border-white">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
        </span>
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="open = false"
         :class="y > window.innerHeight / 2 ? 'bottom-16 origin-bottom-left' : 'top-16 origin-top-left'"
         class="absolute left-0 w-[320px] bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden text-gray-800 ring-1 ring-black/5 cursor-default z-[10000]"
         style="background-color: white !important; color: #333 !important;">
        
        <div class="bg-[#0F244A] text-white p-4 flex justify-between items-center" style="background-color: #0F244A !important; color: white !important;">
            <h3 class="font-bold text-sm uppercase tracking-wider flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Menu Aksesibilitas
            </h3>
            <button @click="reset()" class="text-[10px] bg-red-500 hover:bg-red-600 px-2 py-1 rounded text-white font-bold transition" style="background-color: #EF4444 !important; color: white !important;">
                RESET
            </button>
        </div>

        <div class="p-4 max-h-[400px] overflow-y-auto custom-scrollbar">
            <div class="mb-5">
                <p class="text-xs font-bold text-gray-500 mb-2 uppercase flex items-center gap-1">Ukuran Teks</p>
                <div class="flex items-center justify-between bg-gray-100 rounded-lg p-1 border border-gray-200" style="background-color: #F3F4F6 !important;">
                    <button @click="adjustFont(-10)" class="w-10 h-10 flex items-center justify-center rounded bg-white shadow text-lg font-bold" style="color: black !important; background-color: white !important;">-</button>
                    <span class="text-sm font-bold text-blue-900" x-text="fontSize + '%'" style="color: #1E3A8A !important;"></span>
                    <button @click="adjustFont(10)" class="w-10 h-10 flex items-center justify-center rounded bg-white shadow text-lg font-bold" style="color: black !important; background-color: white !important;">+</button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button @click="toggleFeature('readableFont')" class="flex flex-col items-center justify-center p-3 rounded-xl border transition group" :class="readableFont ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-gray-50 border-gray-200'">
                    <span class="text-xl mb-1 font-serif">Aa</span>
                    <span class="text-[10px] font-bold uppercase">Font Terbaca</span>
                    <div class="w-2 h-2 rounded-full mt-1" :class="readableFont ? 'bg-blue-500' : 'bg-gray-300'"></div>
                </button>

                <button @click="toggleFeature('highlightLinks')" class="flex flex-col items-center justify-center p-3 rounded-xl border transition group" :class="highlightLinks ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-gray-50 border-gray-200'">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    <span class="text-[10px] font-bold uppercase">Sorot Link</span>
                    <div class="w-2 h-2 rounded-full mt-1" :class="highlightLinks ? 'bg-blue-500' : 'bg-gray-300'"></div>
                </button>

                <button @click="toggleFeature('grayscale')" class="flex flex-col items-center justify-center p-3 rounded-xl border transition group" :class="grayscale ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-gray-50 border-gray-200'">
                    <div class="w-6 h-6 rounded-full bg-gradient-to-r from-gray-400 to-gray-800 mb-1"></div>
                    <span class="text-[10px] font-bold uppercase">Abu-abu</span>
                    <div class="w-2 h-2 rounded-full mt-1" :class="grayscale ? 'bg-blue-500' : 'bg-gray-300'"></div>
                </button>

                <button @click="toggleFeature('contrast')" class="flex flex-col items-center justify-center p-3 rounded-xl border transition group" :class="contrast ? 'bg-black text-yellow-400 border-yellow-400' : 'bg-gray-50 border-gray-200'">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <span class="text-[10px] font-bold uppercase">Kontras</span>
                    <div class="w-2 h-2 rounded-full mt-1" :class="contrast ? 'bg-yellow-400' : 'bg-gray-300'"></div>
                </button>

                <button @click="toggleFeature('invert')" class="flex flex-col items-center justify-center p-3 rounded-xl border transition group" :class="invert ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-gray-50 border-gray-200'">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span class="text-[10px] font-bold uppercase">Balik Warna</span>
                    <div class="w-2 h-2 rounded-full mt-1" :class="invert ? 'bg-blue-500' : 'bg-gray-300'"></div>
                </button>

                <button @click="toggleFeature('bigCursor')" class="flex flex-col items-center justify-center p-3 rounded-xl border transition group" :class="bigCursor ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-gray-50 border-gray-200'">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                    <span class="text-[10px] font-bold uppercase">Kursor Besar</span>
                    <div class="w-2 h-2 rounded-full mt-1" :class="bigCursor ? 'bg-blue-500' : 'bg-gray-300'"></div>
                </button>

                <button @click="toggleFeature('spacing')" class="flex flex-col items-center justify-center p-3 rounded-xl border transition group" :class="spacing ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-gray-50 border-gray-200'">
                    <span class="text-lg mb-1 font-bold tracking-widest">A B C</span>
                    <span class="text-[10px] font-bold uppercase">Spasi Huruf</span>
                    <div class="w-2 h-2 rounded-full mt-1" :class="spacing ? 'bg-blue-500' : 'bg-gray-300'"></div>
                </button>

                 <button @click="toggleFeature('hideImages')" class="flex flex-col items-center justify-center p-3 rounded-xl border transition group" :class="hideImages ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-gray-50 border-gray-200'">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.05 10.05 0 01-1.563 3.029m5.858.908l3.59 3.59"></path></svg>
                    <span class="text-[10px] font-bold uppercase">Sembunyi Gbr</span>
                    <div class="w-2 h-2 rounded-full mt-1" :class="hideImages ? 'bg-blue-500' : 'bg-gray-300'"></div>
                </button>
            </div>
        </div>
    </div>
</div>