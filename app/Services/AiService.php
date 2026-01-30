<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log; // Import Log Wajib Ada

class AiService
{
    protected $apiKey;
    protected $baseUrl;
    protected $umkm;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        // PENTING: Gunakan 1.5 Flash agar stabil untuk Function Calling & Logika
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$this->apiKey}";
        $this->umkm = null;
    }

    public function processMessage($userMessage, $history = [])
    {
        $user = Auth::user();
        $now = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm');

        if (!$user) return $this->handleGuestConsultation($userMessage, $now);

        $this->umkm = $user->umkm;
        
        if ($this->umkm) {
            $tools = [
                // 1. CEK DATA (READ) - HAPUS 'PARAMETERS' AGAR TIDAK ERROR
                [
                    "name" => "get_product_list", 
                    "description" => "Melihat daftar produk jualan dan stok tersedia."
                ],
                [
                    "name" => "get_inventory_list", 
                    "description" => "Melihat stok bahan baku mentah di gudang."
                ],
                [
                    "name" => "get_financial_summary", 
                    "description" => "Melihat laporan keuangan: Omset (Uang Masuk), HPP (Modal), dan Profit (Untung Bersih)."
                ],

                // 2. CREATE / ACTION TOOLS
                [
                    "name" => "add_inventory_item",
                    "description" => "Menambah stok bahan baku ke gudang.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "name" => ["type" => "STRING", "description" => "Nama bahan (cth: Gula Pasir)"],
                            "stock" => ["type" => "INTEGER", "description" => "Jumlah stok"],
                            "unit" => ["type" => "STRING", "description" => "Satuan (kg, liter, pcs)"],
                            "price_per_unit" => ["type" => "NUMBER", "description" => "Harga beli per satuan"]
                        ],
                        "required" => ["name", "stock", "unit", "price_per_unit"]
                    ]
                ],
                [
                    "name" => "create_product_with_recipe",
                    "description" => "Membuat menu/produk baru yang dijual ke pelanggan.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "name" => ["type" => "STRING", "description" => "Nama Menu"],
                            "price" => ["type" => "NUMBER", "description" => "Harga Jual"],
                            "ingredients" => [
                                "type" => "ARRAY",
                                "items" => [
                                    "type" => "OBJECT",
                                    "properties" => [
                                        "item_name" => ["type" => "STRING", "description" => "Nama bahan di gudang"],
                                        "amount" => ["type" => "NUMBER", "description" => "Jumlah pakai"]
                                    ]
                                ]
                            ]
                        ],
                        "required" => ["name", "price", "ingredients"]
                    ]
                ],
                [
                    "name" => "record_sale",
                    "description" => "WAJIB DIPANGGIL untuk mencatat penjualan produk ke database.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "product_name" => ["type" => "STRING", "description" => "Nama produk yang dijual"],
                            "quantity" => ["type" => "INTEGER", "description" => "Jumlah yang terjual"]
                        ],
                        "required" => ["product_name", "quantity"]
                    ]
                ]
            ];

            $systemPrompt = "
            Kamu adalah Asisten Cerdas Toko 'BISA'.
            Waktu Server: {$now}.

            ATURAN KERAS (LOGIKA SISTEM):
            1. Jika user bilang '10 ribu', anggap angka 10000. Jangan buat produk bernama 'ribu'.
            2. Jika user bilang 'Jual Kopi 5', artinya panggil fungsi record_sale dengan nama='kopi' dan qty=5.
            3. JANGAN HALUSINASI. Jangan bilang 'berhasil dicatat' kalau kamu belum memanggil fungsi record_sale.
            4. Gunakan fungsi get_financial_summary untuk menjawab pertanyaan soal omset/laporan.
            ";
        } else {
            return $this->callGeminiApi($userMessage, $history, [], "Arahkan user untuk mendaftarkan UMKM-nya dulu di menu profil.");
        }

        return $this->callGeminiApi($userMessage, $history, $tools, $systemPrompt);
    }

    // --- FUNGSI EKSEKUSI (PHP) ---

    protected function get_financial_summary($args = []) {
        // Filter type 'IN' sesuai database Bos
        $transaksi = Transaction::where('umkm_id', $this->umkm->id)->where('type', 'IN')->get();
        
        $omset = $transaksi->sum('amount');
        $hpp = $transaksi->sum('cost_amount');
        $profit = $omset - $hpp;
        $count = $transaksi->count();
        
        return "📊 LAPORAN KEUANGAN:\n" .
               "- Transaksi: {$count} kali\n" .
               "- Omset: Rp " . number_format($omset, 0, ',', '.') . "\n" .
               "- HPP (Modal): Rp " . number_format($hpp, 0, ',', '.') . "\n" .
               "- ✅ PROFIT: Rp " . number_format($profit, 0, ',', '.');
    }

    protected function record_sale($args) {
        // 1. Bersihkan Nama Produk
        $cleanName = trim(str_ireplace(['pcs', 'buah', 'bungkus', 'porsi', 'gelas'], '', $args['product_name']));

        // 2. Cari Produk
        $product = Product::with('ingredients')
                    ->where('umkm_id', $this->umkm->id)
                    ->where('name', 'LIKE', '%' . $cleanName . '%')
                    ->first();

        if(!$product) {
            // Fallback cari tanpa cleaning
            $product = Product::with('ingredients')
                        ->where('umkm_id', $this->umkm->id)
                        ->where('name', 'LIKE', '%'.$args['product_name'].'%')
                        ->first();
            
            if(!$product) return "⚠️ Gagal: Produk '{$cleanName}' tidak ditemukan. Cek ejaan nama produk.";
        }
        
        $qty = $args['quantity'];

        // 3. Cek Stok (Computed Stock)
        if ($product->computed_stock < $qty) {
             return "⛔ Stok Kurang! Stok '{$product->name}' sisa {$product->computed_stock}. Cek bahan baku.";
        }

        // 4. Hitung HPP & Kurangi Stok Bahan
        $totalHPP = 0;
        foreach($product->ingredients as $ing) {
            $needed = $ing->pivot->amount * $qty;
            $totalHPP += ($ing->price_per_unit * $needed);
            $ing->decrement('stock', $needed);
        }
        
        $totalOmset = $product->price * $qty;
        $profit = $totalOmset - $totalHPP;

        // 5. Simpan Transaksi (SESUAI FORMAT DASHBOARD BOS)
        try {
            $trx = Transaction::create([
                'umkm_id' => $this->umkm->id,
                'product_id' => $product->id,
                'amount' => $totalOmset,
                'cost_amount' => $totalHPP,
                'quantity' => $qty,
                'type' => 'IN',           // <-- Sesuai screenshot DBeaver
                'date' => now(),
                'status' => 'paid',       // <-- Status aman agar muncul di rekap
                'payment_method' => 'cash', // <-- Tambahan agar tidak difilter dashboard
                'description' => "Penjualan via AI: {$product->name}"
            ]);
            
            Log::info("✅ Transaksi AI Sukses: ID {$trx->id}");

            return "✅ BERHASIL (ID #{$trx->id}):\n" .
                   "Terjual {$qty} {$product->name}.\n" .
                   "💰 Omset: Rp " . number_format($totalOmset) . "\n" .
                   "📈 Profit: Rp " . number_format($profit);

        } catch (\Exception $e) {
            Log::error("Database Error: " . $e->getMessage());
            return "❌ Error Sistem: " . $e->getMessage();
        }
    }

    protected function add_inventory_item($args) {
        $price = str_replace(['.', ','], '', (string)$args['price_per_unit']); 
        try {
            InventoryItem::create([
                'umkm_id' => $this->umkm->id,
                'name' => $args['name'],
                'category' => 'bahan',
                'stock' => $args['stock'],
                'unit' => $args['unit'],
                'price_per_unit' => (float)$price
            ]);
            return "✅ Bahan '{$args['name']}' berhasil disimpan.";
        } catch (\Exception $e) { return "Gagal simpan: " . $e->getMessage(); }
    }

    protected function create_product_with_recipe($args) {
        try {
            $product = Product::create([
                'umkm_id' => $this->umkm->id,
                'name' => $args['name'],
                'price' => $args['price'],
                'stock' => 0, 
                'is_available' => true
            ]);

            $missing = [];
            foreach ($args['ingredients'] as $ing) {
                $item = InventoryItem::where('umkm_id', $this->umkm->id)
                            ->where('name', 'LIKE', '%' . $ing['item_name'] . '%')->first();

                if ($item) {
                    $product->ingredients()->attach($item->id, ['amount' => $ing['amount']]);
                } else {
                    $missing[] = $ing['item_name'];
                }
            }
            return "✅ Produk '{$args['name']}' berhasil dibuat!";
        } catch (\Exception $e) { return "Gagal buat produk: " . $e->getMessage(); }
    }

    protected function get_product_list() {
        $products = Product::with('ingredients')->where('umkm_id', $this->umkm->id)->get();
        if($products->isEmpty()) return "Belum ada produk.";
        return $products->map(fn($p) => "- {$p->name}: Rp " . number_format($p->price) . " (Stok: {$p->computed_stock})")->implode("\n");
    }

    protected function get_inventory_list() {
        $items = InventoryItem::where('umkm_id', $this->umkm->id)->get();
        if($items->isEmpty()) return "Gudang kosong.";
        return $items->map(fn($i) => "- {$i->name}: {$i->stock} {$i->unit}")->implode("\n");
    }

    // --- CORE GEMINI & ERROR HANDLING ---

    private function callGeminiApi($message, $history, $tools, $systemPrompt) {
        $contents = [];
        
        // Filter History: Hanya masukkan yang punya pesan/respon valid
        foreach ($history as $chat) {
            if (!empty($chat->message)) {
                $contents[] = ['role' => 'user', 'parts' => [['text' => $chat->message]]];
            }
            if (!empty($chat->response)) {
                $contents[] = ['role' => 'model', 'parts' => [['text' => $chat->response]]];
            }
        }
        
        $contents[] = ['role' => 'user', 'parts' => [['text' => $systemPrompt . "\n\nUser: " . $message]]];

        $payload = ["contents" => $contents];
        if (!empty($tools)) $payload["tools"] = [["function_declarations" => $tools]];

        try {
            $response = Http::post($this->baseUrl, $payload)->json();
            
            // Cek Error dari Google
            if(isset($response['error'])) {
                Log::error("GEMINI API ERROR: " . json_encode($response['error']));
                return "⚠️ Maaf, ada gangguan teknis AI: " . ($response['error']['message'] ?? 'Unknown');
            }

            $candidate = $response['candidates'][0]['content']['parts'][0] ?? [];

            // Handle Function Call
            if (isset($candidate['functionCall'])) {
                $fName = $candidate['functionCall']['name'];
                $fArgs = $candidate['functionCall']['args'] ?? [];
                
                // Panggil Fungsi PHP
                $result = method_exists($this, $fName) ? $this->$fName($fArgs) : "Fungsi tidak ditemukan.";
                
                // Kirim hasil balik ke AI
                $contents[] = ['role' => 'model', 'parts' => [['functionCall' => $candidate['functionCall']]]];
                $contents[] = ['role' => 'function', 'parts' => [['functionResponse' => ['name' => $fName, 'response' => ['content' => $result]]]]];
                
                // Minta respon final
                $final = Http::post($this->baseUrl, ["contents" => $contents, "tools" => [["function_declarations" => $tools]]])->json();
                
                if(isset($final['error'])) {
                    return "⚠️ Gagal memproses hasil fungsi: " . ($final['error']['message'] ?? 'Unknown');
                }

                return $final['candidates'][0]['content']['parts'][0]['text'] ?? $result;
            }
            
            return $candidate['text'] ?? "Maaf, saya tidak mengerti.";
            
        } catch (\Exception $e) { 
            Log::error("Connection Error: " . $e->getMessage());
            return "Koneksi Error: " . $e->getMessage(); 
        }
    }

    protected function handleGuestConsultation($message, $now) {
        return "Silakan login terlebih dahulu.";
    }
}