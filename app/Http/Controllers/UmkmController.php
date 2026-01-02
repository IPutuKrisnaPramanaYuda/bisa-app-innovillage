<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UmkmController extends Controller
{
    // ==========================================
    // 1. DASHBOARD UTAMA (Dengan Filter Grafik)
    // ==========================================
public function index(Request $request)
    {
        $user = auth()->user();
        $umkm = $user->umkm;

        if (!$umkm) {
            return redirect()->route('umkm.create');
        }

        // --- A. DATA KEUANGAN ---
        $pendapatanBulanIni = \App\Models\Transaction::where('umkm_id', $umkm->id)
            ->where('type', 'OUT')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $hppBulanIni = \App\Models\Transaction::where('umkm_id', $umkm->id)
            ->where('type', 'OUT')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('cost_amount');

        $labaBulanIni = $pendapatanBulanIni - $hppBulanIni;

        // Hitung Persentase Kenaikan
        $pendapatanBulanLalu = \App\Models\Transaction::where('umkm_id', $umkm->id)
            ->where('type', 'OUT')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('amount');

        $persentaseKenaikan = 0;
        if ($pendapatanBulanLalu > 0) {
            $persentaseKenaikan = (($pendapatanBulanIni - $pendapatanBulanLalu) / $pendapatanBulanLalu) * 100;
        } elseif ($pendapatanBulanIni > 0) {
            $persentaseKenaikan = 100;
        }

        // --- B. DATA STOK & PENJUALAN ---
        
        // 1. Ambil semua produk milik UMKM ini
        $allProducts = \App\Models\Product::where('umkm_id', $umkm->id)->get();

        // 2. Hitung Stok Menipis (Logic Resep)
        $stokMenipis = $allProducts->filter(function ($product) {
            return $product->computed_stock <= 5; 
        })->count();

        // 3. Hitung Total Stok Tersedia (Porsi)
        $totalStok = $allProducts->sum('computed_stock');

        // 4. Total Pesanan (Jumlah Transaksi/Nota)
        $totalPesanan = \App\Models\Transaction::where('umkm_id', $umkm->id)->where('type', 'OUT')->count();

        // 5. Total Item Terjual (Jumlah Pcs Produk) <-- INI YANG TADI ERROR
        $totalItemTerjual = \App\Models\Transaction::where('umkm_id', $umkm->id)
            ->where('type', 'OUT')
            ->sum('quantity');


        // --- C. GRAFIK ---
        $periode = $request->get('periode', 'mingguan');
        $grafikLabel = [];
        $grafikData = [];
        $queryGrafik = \App\Models\Transaction::where('umkm_id', $umkm->id)->where('type', 'OUT');

        switch ($periode) {
            case 'harian':
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $grafikLabel[] = $date->format('d M');
                    $grafikData[] = (clone $queryGrafik)->whereDate('created_at', $date)->sum('amount');
                }
                break;
            case 'mingguan':
                for ($i = 3; $i >= 0; $i--) {
                    $startOfWeek = now()->subWeeks($i)->startOfWeek();
                    $endOfWeek = now()->subWeeks($i)->endOfWeek();
                    $grafikLabel[] = 'Minggu ' . $startOfWeek->format('d');
                    $grafikData[] = (clone $queryGrafik)->whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('amount');
                }
                break;
            case 'bulanan':
                for ($i = 1; $i <= 12; $i++) {
                    $grafikLabel[] = date("M", mktime(0, 0, 0, $i, 10));
                    $grafikData[] = (clone $queryGrafik)->whereMonth('created_at', $i)->whereYear('created_at', now()->year)->sum('amount');
                }
                break;
            case 'tahunan':
                for ($i = 4; $i >= 0; $i--) {
                    $year = now()->subYears($i)->year;
                    $grafikLabel[] = $year;
                    $grafikData[] = (clone $queryGrafik)->whereYear('created_at', $year)->sum('amount');
                }
                break;
        }

        // Pesanan Terbaru
        $pesananBaru = \App\Models\Transaction::where('umkm_id', $umkm->id)
            ->where('type', 'OUT')
            ->with(['buyer', 'product'])
            ->latest()
            ->take(5)
            ->get();

        return view('mikro_erp', compact(
            'umkm', 'user', 'periode',
            'pendapatanBulanIni', 'labaBulanIni', 'persentaseKenaikan',
            'totalPesanan', 'stokMenipis', 'totalStok', 
            'totalItemTerjual', // <--- Pastikan variable ini ada di sini!
            'pesananBaru', 'grafikData', 'grafikLabel'
        ));
    }

    // ==========================================
    // 2. MANAJEMEN PRODUK (CRUD LENGKAP)
    // ==========================================
    
    // Tampilkan Daftar Produk
    public function products()
    {
        $umkm = auth()->user()->umkm;
        $products = Product::where('umkm_id', $umkm->id)->latest()->get();
        return view('umkm.products', compact('umkm', 'products'));
    }
    public function destroyInventory($id)
    {
        $item = \App\Models\InventoryItem::where('umkm_id', auth()->user()->umkm->id)->findOrFail($id);
        $item->delete();
        return back()->with('success', 'Item dihapus.');
    }
    // Tampilkan Form Tambah
    public function createProduct()
    {
        $umkm = auth()->user()->umkm;
        // Kirim data bahan baku ke halaman create product
        $inventoryItems = \App\Models\InventoryItem::where('umkm_id', $umkm->id)->where('category', 'bahan')->get();
        
        return view('umkm.products.create', compact('inventoryItems'));
    }

    // Proses Simpan Produk Baru
public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'cost_price' => 'required|numeric', // Total HPP
            // 'stock' => TIDAK PERLU LAGI DI VALIDASI
            'ingredients' => 'required|array', // Wajib ada bahan
            'ingredients.*.id' => 'required|exists:inventory_items,id',
            'ingredients.*.amount' => 'required|numeric|min:0.1',
        ]);

        $imagePath = $request->hasFile('image') ? $request->file('image')->store('products', 'public') : null;

        // Buat Produk
        $product = \App\Models\Product::create([
        'umkm_id' => auth()->user()->umkm->id,
        'name' => $request->name,
        // 'slug' => Str::slug($request->name),  <-- HAPUS ATAU KOMENTARI BARIS INI
        'price' => $request->price,
        'cost_price' => $request->cost_price,
        'stock' => 0, 
        'image' => $imagePath,
        'description' => $request->description,
    ]);

        // SIMPAN RESEP (BAHAN-BAHAN)
        foreach ($request->ingredients as $ing) {
            \App\Models\ProductIngredient::create([
                'product_id' => $product->id,
                'inventory_item_id' => $ing['id'],
                'amount' => $ing['amount'],
            ]);
        }

        return redirect()->route('umkm.products')->with('success', 'Produk & Resep berhasil dibuat!');
    }

    // Tampilkan Form Edit
    public function editProduct($id)
    {
        $product = Product::where('umkm_id', auth()->user()->umkm->id)->findOrFail($id);
        return view('umkm.products.edit', compact('product'));
    }

    // Proses Update Produk
    public function updateProduct(Request $request, $id)
    {
        $product = Product::where('umkm_id', auth()->user()->umkm->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'cost_price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
            $product->save();
        }

        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'price' => $request->price,
            'cost_price' => $request->cost_price,
            'stock' => $request->stock,
            'description' => $request->description,
        ]);

        return redirect()->route('umkm.products')->with('success', 'Produk berhasil diperbarui!');
    }

    // Hapus Produk
   public function destroyProduct($id)
    {
        $umkm_id = auth()->user()->umkm->id;
        $product = \App\Models\Product::where('umkm_id', $umkm_id)->findOrFail($id);
        
        // 1. Hapus Foto (Jika ada)
        if ($product->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
        }

        // 2. HAPUS TRANSAKSI TERKAIT (SOLUSI ERROR ANDA)
        // Kita wajib menghapus riwayat penjualan produk ini dulu agar Foreign Key tidak error
        \App\Models\Transaction::where('product_id', $product->id)->delete();
        
        // 3. Hapus Produk
        // (Data di tabel product_ingredients akan terhapus otomatis karena kita set 'onDelete cascade' di migrasi)
        $product->delete();

        return redirect()->back()->with('success', 'Produk beserta riwayat transaksinya berhasil dihapus.');
    }


    // ==========================================
    // 3. PENJUALAN & TRANSAKSI
    // ==========================================

    // Halaman Penjualan (Updated dengan Data Produk untuk Modal)
    public function sales()
    {
        $umkm = auth()->user()->umkm;
        
        // 1. Ambil Produk (Untuk Dropdown)
       $products = \App\Models\Product::where('umkm_id', $umkm->id)->get();

        // 2. Riwayat Transaksi
        $transactions = \App\Models\Transaction::where('umkm_id', $umkm->id)
                        ->where('type', 'OUT')
                        ->latest()
                        ->get();
        
        // 3. LOGIKA GRAFIK HARIAN (Bulan Ini)
        $grafikLabel = [];
        $grafikData = [];
        
        // Loop dari tanggal 1 sampai hari ini (atau sampai akhir bulan)
        $daysInMonth = now()->daysInMonth;
        
        for ($i = 1; $i <= $daysInMonth; $i++) {
    $date = now()->setDay($i);
    $grafikLabel[] = $date->format('d M');
    
    $total = \App\Models\Transaction::where('umkm_id', $umkm->id)
                ->where('type', 'OUT')
                ->whereDate('created_at', $date)
                ->sum('amount');
    
    // 🔥 Ubah baris ini: Pakai (int) untuk memastikan formatnya angka
    $grafikData[] = (int) $total; 
}

        return view('umkm.sales', compact('umkm', 'transactions', 'grafikData', 'grafikLabel', 'products'));
    }

    // Proses Simpan Transaksi Manual
    public function storeTransaction(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = \App\Models\Product::with('ingredients.inventory')->findOrFail($request->product_id);

        // 1. Cek Stok (Pakai Logic Computed Stock)
        if ($product->computed_stock < $request->quantity) {
            return back()->with('error', 'Stok bahan baku tidak cukup! Maksimal buat: ' . $product->computed_stock);
        }

        // 2. KURANGI STOK BAHAN BAKU DI GUDANG
        foreach ($product->ingredients as $ing) {
            // Jumlah yg dikurangi = Takaran Resep * Jumlah Beli
            $totalUsage = $ing->amount * $request->quantity;
            
            // Update Stok Inventori
            $ing->inventory->decrement('stock', $totalUsage);
        }

        // 3. Simpan Transaksi (Sama seperti sebelumnya)
        $totalOmset = $product->price * $request->quantity;
        $totalHPP   = $product->cost_price * $request->quantity;

        \App\Models\Transaction::create([
            'umkm_id' => auth()->user()->umkm->id,
            'product_id' => $product->id,
            'type' => 'OUT',
            'amount' => $totalOmset,
            'cost_amount' => $totalHPP,
            'quantity' => $request->quantity,
            'date' => now(),
            'description' => 'Penjualan: ' . $request->quantity . ' pcs',
        ]);

        // Update Kas
        auth()->user()->umkm->increment('balance', $totalOmset);

        return redirect()->route('umkm.sales')->with('success', 'Penjualan berhasil! Stok bahan baku berkurang.');
    }


    // ==========================================
    // 4. FITUR LAINNYA
    // ==========================================

    // 1. TAMPILKAN HALAMAN INVENTORI
    public function inventory()
    {
        $umkm = auth()->user()->umkm;
        
        // Ambil data InventoryItem, bukan Product lagi
        $items = \App\Models\InventoryItem::where('umkm_id', $umkm->id)
                ->orderBy('category', 'asc') // Urutkan biar rapi (Alat/Bahan)
                ->latest()
                ->get();
                
        return view('umkm.inventory', compact('umkm', 'items'));
    }
    // 2. SIMPAN BAHAN BAKU BARU
public function storeInventory(Request $request)
    {
        // ... validasi sama seperti sebelumnya ...

        $umkm = auth()->user()->umkm;

        // CEK SALDO CUKUP GAK?
        if ($umkm->balance < $request->total_price) {
            return back()->with('error', 'Saldo kas tidak cukup untuk belanja bahan! Sisa kas: Rp ' . number_format($umkm->balance,0,',','.'));
        }

        // Hitung harga per unit
        $pricePerUnit = $request->total_price / $request->purchase_amount;

        \App\Models\InventoryItem::create([
            'umkm_id' => $umkm->id,
            'name' => $request->name,
            'category' => $request->category,
            'stock' => $request->stock,
            'unit' => $request->unit,
            'price_per_unit' => $pricePerUnit,
        ]);

        // LOGIKA KAS BERKURANG
        $umkm->decrement('balance', $request->total_price);

        // Catat Pengeluaran di Transaksi (Type IN = Uang Keluar/Belanja)
        \App\Models\Transaction::create([
            'umkm_id' => $umkm->id,
            'type' => 'IN', // Belanja
            'amount' => $request->total_price,
            
            // TAMBAHKAN BARIS INI (Fix Error):
            'quantity' => 1, 
            
            'description' => 'Belanja Inventori: ' . $request->name,
            'date' => now(),
        ]);

        return back()->with('success', 'Bahan baku dibeli & saldo berkurang!');
    }
    public function reports()
    {
        $umkm = auth()->user()->umkm;

        // A. Total Omset (Semua Uang Masuk dari Penjualan)
        $omsetKotor = \App\Models\Transaction::where('umkm_id', $umkm->id)
                      ->where('type', 'OUT')
                      ->sum('amount');

        // B. Total HPP Terjual (Modal dari barang yg laku saja)
        $hppTerjual = \App\Models\Transaction::where('umkm_id', $umkm->id)
                      ->where('type', 'OUT')
                      ->sum('cost_amount');

        // C. Keuntungan Bersih (Profit = Omset - HPP Barang Laku)
        // Bukan dikurang belanja inventori, karena inventori sisa masih aset.
        $labaBersih = $omsetKotor - $hppTerjual;

        // D. Saldo Kas Real (Dari Database)
        $saldoKas = $umkm->balance;

        $totalTerjual = \App\Models\Transaction::where('umkm_id', $umkm->id)->where('type', 'OUT')->count();

        return view('umkm.reports', compact('umkm', 'omsetKotor', 'labaBersih', 'saldoKas', 'totalTerjual'));
    }

    // Form Buka Toko (Jika belum punya)
    public function create()
    {
        if (auth()->user()->umkm) {
            return redirect()->route('umkm.dashboard');
        }
        return view('umkm.create');
    }

    // Simpan Toko Baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:umkms,name',
            'description' => 'nullable|string',
        ]);

        auth()->user()->umkm()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->route('umkm.dashboard')->with('success', 'Toko berhasil dibuat!');
    }
    // 1. UPDATE KAS (Input Modal Awal)
    public function updateBalance(Request $request)
    {
        $request->validate(['balance' => 'required|numeric|min:0']);
        
        $umkm = auth()->user()->umkm;
        $umkm->update(['balance' => $request->balance]); // Set Saldo Awal/Reset
        
        return back()->with('success', 'Saldo kas berhasil diatur!');
    }   
    
    // 4. TAMPILKAN FORM EDIT INVENTORI
    public function editInventory($id)
    {
        $item = \App\Models\InventoryItem::where('umkm_id', auth()->user()->umkm->id)->findOrFail($id);
        return view('umkm.inventory_edit', compact('item'));
    }

    // 5. UPDATE INVENTORI
    public function updateInventory(Request $request, $id)
    {
        $item = \App\Models\InventoryItem::where('umkm_id', auth()->user()->umkm->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:bahan,alat',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:10',
            'total_price' => 'required|numeric', 
            'purchase_amount' => 'required|numeric|min:1',
        ]);

        // Hitung ulang harga per unit (jika user mengubah harga beli/jumlah)
        $pricePerUnit = $request->total_price / $request->purchase_amount;

        $item->update([
            'name' => $request->name,
            'category' => $request->category,
            'stock' => $request->stock,
            'unit' => $request->unit,
            'price_per_unit' => $pricePerUnit,
        ]);

        return redirect()->route('umkm.inventory')->with('success', 'Data inventori diperbarui!');
    }
}