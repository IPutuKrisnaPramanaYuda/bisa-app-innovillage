<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AiService
{
    protected $apiKey;
    protected $baseUrl;
    protected $umkm;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$this->apiKey}";
        $this->umkm = null;
    }

    public function processMessage($userMessage, $history = [])
    {
        $user = Auth::user();
        $now = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm');

        if (!$user) return $this->handleGuestConsultation($userMessage, $now);

        $this->umkm = $user->umkm;
        $tools = [];
        
        if ($this->umkm) {
            $tools = [
                // --- TOOLS READ ---
                ["name" => "get_product_list", "description" => "Melihat daftar produk dan stok hitungan otomatis."],
                ["name" => "get_inventory_list", "description" => "Melihat stok bahan baku di gudang. CEK INI SEBELUM BUAT RESEP."],
                
                // --- TOOLS CREATE (RESEP) ---
                [
                    "name" => "create_product_with_recipe",
                    "description" => "Membuat produk baru dengan resep bahan baku.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "name" => ["type" => "STRING", "description" => "Nama Produk (cth: Kopi Robusta)"],
                            "price" => ["type" => "NUMBER", "description" => "Harga Jual"],
                            "ingredients" => [
                                "type" => "ARRAY",
                                "description" => "Daftar bahan baku",
                                "items" => [
                                    "type" => "OBJECT",
                                    "properties" => [
                                        "item_name" => ["type" => "STRING", "description" => "Nama bahan baku persis di gudang"],
                                        "amount" => ["type" => "NUMBER", "description" => "Jumlah pakai per porsi (angka saja)"]
                                    ]
                                ]
                            ]
                        ],
                        "required" => ["name", "price", "ingredients"]
                    ]
                ],

                // --- TOOLS LAIN ---
                [
                    "name" => "add_inventory_item",
                    "description" => "Menambah master bahan baku baru ke gudang.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "name" => ["type" => "STRING"],
                            "stock" => ["type" => "INTEGER"],
                            "unit" => ["type" => "STRING"],
                            "category" => ["type" => "STRING", "description" => "'bahan' atau 'alat'"],
                            "price_per_unit" => ["type" => "NUMBER"]
                        ],
                        "required" => ["name", "stock", "unit", "category", "price_per_unit"]
                    ]
                ],
                [
                    "name" => "record_sale",
                    "description" => "Mencatat penjualan (stok bahan otomatis berkurang).",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "product_name" => ["type" => "STRING"],
                            "quantity" => ["type" => "INTEGER"]
                        ],
                        "required" => ["product_name", "quantity"]
                    ]
                ]
            ];

            $systemPrompt = "
        Kamu adalah Manajer Toko 'BISA'.
        
        ATURAN KERAS (WAJIB PATUH):
        1. JANGAN PERNAH bilang 'sudah ditambahkan' atau 'berhasil' JIKA KAMU BELUM MEMANGGIL TOOL (Function Calling).
        2. Jika user minta tambah data, KAMU WAJIB panggil fungsi 'add_inventory_item' atau 'create_product'.
        3. Jangan mengarang data sendiri. Jika data kurang (misal harga belum ada), TANYA USER dulu.
        4. Setelah panggil fungsi, gunakan hasil return dari fungsi tersebut untuk menjawab user.
        ";
        } else {
            $systemPrompt = "Ajak user buat toko.";
        }

        return $this->callGeminiApi($userMessage, $history, $tools, $systemPrompt);
    }

    // --- FUNGSI EKSEKUSI ---

    protected function create_product_with_recipe($args) {
        try {
            // 1. Buat Produk
            $product = Product::create([
                'umkm_id' => $this->umkm->id,
                'name' => $args['name'],
                'price' => $args['price'],
                'stock' => 0, // Diabaikan karena pakai resep
                'is_available' => true
            ]);

            // 2. Sambungkan Resep (Pakai kolom 'amount')
            $missingItems = [];
            foreach ($args['ingredients'] as $ing) {
                $item = InventoryItem::where('umkm_id', $this->umkm->id)
                            ->where('name', 'LIKE', '%' . $ing['item_name'] . '%')
                            ->first();

                if ($item) {
                    // PERBAIKAN: Gunakan 'amount'
                    $product->ingredients()->attach($item->id, ['amount' => $ing['amount']]);
                } else {
                    $missingItems[] = $ing['item_name'];
                }
            }

            $msg = "✅ Produk '{$args['name']}' berhasil dibuat! ";
            if (!empty($missingItems)) {
                $msg .= "\n⚠️ Peringatan: Bahan berikut tidak ditemukan di gudang: " . implode(', ', $missingItems) . ". Harap input bahan tersebut agar stok terbaca.";
            } else {
                $msg .= "Stok terhitung saat ini: " . $product->computed_stock . " porsi.";
            }
            return $msg;

        } catch (\Exception $e) { return "Gagal buat produk: " . $e->getMessage(); }
    }

    protected function record_sale($args) {
        $product = Product::with('ingredients')->where('umkm_id', $this->umkm->id)->where('name', 'LIKE', '%'.$args['product_name'].'%')->first();
        if(!$product) return "Produk '{$args['product_name']}' tidak ditemukan.";
        
        // 1. Cek Stok (Penting!)
        if ($product->computed_stock < $args['quantity']) {
             return "⚠️ Stok tidak cukup! Stok tersedia cuma {$product->computed_stock} porsi. Cek stok bahan baku di gudang.";
        }

        // 2. Hitung Total HPP (Modal) & Kurangi Bahan Baku
        $totalHPP = 0;
        foreach($product->ingredients as $ing) {
            $needed = $ing->pivot->amount * $args['quantity'];
            
            // Hitung modal bahan yang dipakai
            $totalHPP += ($ing->price_per_unit * $needed);
            
            // Kurangi stok fisik
            $ing->decrement('stock', $needed);
        }
        
        $totalOmset = $product->price * $args['quantity'];

        // 3. Catat Transaksi Lengkap (Dengan Profit)
        Transaction::create([
            'umkm_id' => $this->umkm->id,
            'product_id' => $product->id,
            'amount' => $totalOmset,      // Uang Masuk
            'cost_amount' => $totalHPP,   // Modal Keluar (HPP)
            'quantity' => $args['quantity'],
            'type' => 'IN',
            'date' => now(),
            'status' => 'paid',
            'description' => "Penjualan {$product->name}"
        ]);
        
        // Update Saldo UMKM (Laba Bersih yang masuk kas)
        $profit = $totalOmset - $totalHPP;
        // $this->umkm->increment('balance', $profit); // Opsional jika ada kolom balance

        return "✅ Terjual {$args['quantity']} {$product->name}.\n💰 Omset: Rp " . number_format($totalOmset) . "\n📉 HPP: Rp " . number_format($totalHPP) . "\n📈 Profit: Rp " . number_format($profit);
    }

    protected function get_inventory_list() {
        $items = InventoryItem::where('umkm_id', $this->umkm->id)->get();
        if($items->isEmpty()) return "Gudang kosong.";
        return $items->map(fn($i) => "- {$i->name}: {$i->stock} {$i->unit}")->implode("\n");
    }

    protected function get_product_list() {
        // Load ingredients agar computed_stock valid
        $products = Product::with('ingredients')->where('umkm_id', $this->umkm->id)->get();
        if($products->isEmpty()) return "Belum ada produk.";
        
        return $products->map(function($p) {
            $stokInfo = $p->ingredients->count() > 0 
                ? "Stok: {$p->computed_stock} (Resep)" 
                : "Stok: {$p->stock} (Manual)";
            return "- {$p->name}: Rp " . number_format($p->price) . " | {$stokInfo}";
        })->implode("\n");
    }
    protected function get_financial_summary() {
        $transaksi = Transaction::where('umkm_id', $this->umkm->id)->where('type', 'IN')->get();
        
        $omset = $transaksi->sum('amount');
        $hpp = $transaksi->sum('cost_amount');
        $profit = $omset - $hpp;
        
        return "📊 **Laporan Keuangan Real-Time**:\n\n" .
               "- 💰 Total Omset: Rp " . number_format($omset) . "\n" .
               "- 📉 Total Modal (HPP): Rp " . number_format($hpp) . "\n" .
               "- 🟢 **Keuntungan Bersih: Rp " . number_format($profit) . "**";
    }

    protected function add_inventory_item($args) {
        try {
            // Log untuk debugging (Cek di storage/logs/laravel.log)
            \Illuminate\Support\Facades\Log::info('AI mencoba nambah inventori:', $args);

            // 1. Validasi Input Minimal
            if (empty($args['name']) || empty($args['stock'])) {
                return "Gagal: Nama barang dan jumlah stok wajib diisi.";
            }

            // 2. Beri nilai default jika AI lupa isi
            $category = isset($args['category']) ? strtolower($args['category']) : 'bahan';
            $unit = $args['unit'] ?? 'pcs';
            $price = $args['price_per_unit'] ?? 0;

            // 3. Simpan ke Database
            InventoryItem::create([
                'umkm_id' => $this->umkm->id,
                'name' => $args['name'],
                'category' => $category,
                'stock' => $args['stock'],
                'unit' => $unit,
                'price_per_unit' => $price
            ]);

            return "✅ SUKSES: Bahan '{$args['name']}' berhasil disimpan ke database (Stok: {$args['stock']} {$unit}).";

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal tambah inventori: ' . $e->getMessage());
            return "❌ ERROR SISTEM: " . $e->getMessage(); 
        }
    }

    // --- Helper Wajib (Jangan Dihapus) ---
    protected function handleGuestConsultation($message, $now) {
        $systemPrompt = "Kamu adalah CS Desa Bengkala. Jawab ramah.";
        $payload = ["contents" => [["role" => "user", "parts" => [["text" => $systemPrompt . "\nUser: " . $message]]]]];
        try {
             $response = Http::post($this->baseUrl, $payload)->json();
             return $response['candidates'][0]['content']['parts'][0]['text'] ?? "Halo!";
        } catch (\Exception $e) { return "Gangguan server."; }
    }
    
    private function callGeminiApi($message, $history, $tools, $prompt) {
        $contents = [];
        foreach ($history as $chat) {
            if (!empty($chat->message)) $contents[] = ['role' => 'user', 'parts' => [['text' => $chat->message]]];
            if (!empty($chat->response)) $contents[] = ['role' => 'model', 'parts' => [['text' => $chat->response]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $prompt . "\n\nUser: " . $message]]];

        $payload = ["contents" => $contents];
        if (!empty($tools)) $payload["tools"] = [["function_declarations" => $tools]];

        try {
            $response = Http::post($this->baseUrl, $payload)->json();
            $candidate = $response['candidates'][0]['content']['parts'][0] ?? [];

            if (isset($candidate['functionCall'])) {
                $functionName = $candidate['functionCall']['name'];
                $args = $candidate['functionCall']['args'] ?? [];
                $resultData = method_exists($this, $functionName) ? $this->$functionName($args) : "Fungsi error.";
                
                $contents[] = ['role' => 'model', 'parts' => [['functionCall' => $candidate['functionCall']]]];
                $contents[] = ['role' => 'function', 'parts' => [['functionResponse' => ['name' => $functionName, 'response' => ['content' => $resultData]]]]];
                
                $finalResponse = Http::post($this->baseUrl, ["contents" => $contents])->json();
                return $finalResponse['candidates'][0]['content']['parts'][0]['text'] ?? $resultData;
            }
            return $candidate['text'] ?? "Maaf, coba lagi.";
        } catch (\Exception $e) { return "Error: " . $e->getMessage(); }
    }
}