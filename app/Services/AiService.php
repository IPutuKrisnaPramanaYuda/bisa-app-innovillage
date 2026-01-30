<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

        if (!$user) return "Silakan login terlebih dahulu.";

        $this->umkm = $user->umkm;
        
        if ($this->umkm) {
            $tools = [
                // 1. READ TOOLS
                ["name" => "get_product_list", "description" => "Melihat daftar produk jualan."],
                ["name" => "get_inventory_list", "description" => "Melihat stok bahan baku gudang."],
                ["name" => "get_financial_summary", "description" => "Melihat laporan keuangan omset dan profit."],

                // 2. ACTION TOOLS
                [
                    "name" => "add_inventory_item",
                    "description" => "Menambah stok bahan baku.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "name" => ["type" => "STRING", "description" => "Nama bahan"],
                            "stock" => ["type" => "INTEGER", "description" => "Jumlah stok"],
                            "unit" => ["type" => "STRING", "description" => "Satuan"],
                            "price_per_unit" => ["type" => "NUMBER", "description" => "Harga beli per satuan"]
                        ],
                        "required" => ["name", "stock", "unit", "price_per_unit"]
                    ]
                ],
                [
                    "name" => "create_product_with_recipe",
                    "description" => "Membuat menu baru.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "name" => ["type" => "STRING"],
                            "price" => ["type" => "NUMBER"],
                            "ingredients" => [
                                "type" => "ARRAY",
                                "items" => [
                                    "type" => "OBJECT",
                                    "properties" => [
                                        "item_name" => ["type" => "STRING"],
                                        "amount" => ["type" => "NUMBER"]
                                    ]
                                ]
                            ]
                        ],
                        "required" => ["name", "price", "ingredients"]
                    ]
                ],
                [
                    "name" => "record_sale",
                    "description" => "Mencatat penjualan produk. WAJIB DIPANGGIL jika user ingin jual barang.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "product_name" => ["type" => "STRING", "description" => "Nama produk"],
                            "quantity" => ["type" => "INTEGER", "description" => "Jumlah qty"]
                        ],
                        "required" => ["product_name", "quantity"]
                    ]
                ]
            ];

            $systemPrompt = "
            Kamu adalah Kasir Toko 'BISA'. Waktu: {$now}.
            
            ATURAN EKSEKUSI:
            1. Bersihkan input: '10 ribu' -> 10000. 'Jual Kopi 5' -> record_sale('Kopi', 5).
            2. Jangan halusinasi. Panggil function record_sale dulu baru bicara.
            3. Gunakan get_financial_summary untuk data omset.
            ";
        } else {
            return "Arahkan user daftar UMKM.";
        }

        return $this->callGeminiApi($userMessage, $history, $tools, $systemPrompt);
    }

    // --- FUNGSI EKSEKUSI ---

    protected function get_financial_summary($args = []) {
        $transaksi = Transaction::where('umkm_id', $this->umkm->id)->where('type', 'IN')->get();
        return "📊 LAPORAN:\n- Omset: Rp " . number_format($transaksi->sum('amount')) . "\n- Profit: Rp " . number_format($transaksi->sum('amount') - $transaksi->sum('cost_amount'));
    }

    protected function record_sale($args) {
        // 1. Bersihkan Nama (Hapus kata 'pcs', 'buah', dll)
        $cleanName = trim(str_ireplace(['pcs', 'buah', 'bungkus', 'porsi', 'gelas', 'cup'], '', $args['product_name']));

        // 2. Logika Cari Produk (Cerdas: Cari kata kunci di dalam nama produk)
        $product = Product::with('ingredients')
                    ->where('umkm_id', $this->umkm->id)
                    ->where('name', 'LIKE', '%' . $cleanName . '%')
                    ->first();

        // Cari Kebalikan: Cek jika Input User ("Kopi Susu Gula Aren") mengandung nama Produk ("Kopi Susu")
        if(!$product) {
            $allProducts = Product::where('umkm_id', $this->umkm->id)->get();
            foreach($allProducts as $p) {
                if (stripos($cleanName, $p->name) !== false) {
                    $product = $p;
                    $product->load('ingredients');
                    break;
                }
            }
        }

        if(!$product) {
            $list = Product::where('umkm_id', $this->umkm->id)->pluck('name')->implode(', ');
            return "❌ GAGAL: Produk '{$cleanName}' tidak ditemukan. Tersedia: {$list}";
        }
        
        $qty = $args['quantity'];

        // 3. Cek Stok
        if ($product->computed_stock < $qty) {
             return "⛔ STOK KURANG! Sisa {$product->computed_stock}.";
        }

        // 4. Hitung HPP & Kurangi Stok
        $totalHPP = 0;
        foreach($product->ingredients as $ing) {
            $needed = $ing->pivot->amount * $qty;
            $totalHPP += ($ing->price_per_unit * $needed);
            $ing->decrement('stock', $needed);
        }
        
        $totalOmset = $product->price * $qty;
        $profit = $totalOmset - $totalHPP;

        // 5. SIMPAN DATABASE (FIXED FOR DASHBOARD)
        try {
            $trx = Transaction::create([
                'umkm_id' => $this->umkm->id,
                'product_id' => $product->id,
                'amount' => $totalOmset,
                'cost_amount' => $totalHPP,
                'quantity' => $qty,
                'type' => 'IN',
                'date' => now(),
                
                // --- UPDATE PENTING BIAR MUNCUL ---
                'status' => 'paid',       // Status sesuai DB Bos
                'buyer_id' => Auth::id(), // Isi ID Bos sendiri biar gak dianggap 'hantu'
                'created_at' => now(),    // Waktu sekarang untuk grafik
                'updated_at' => now(),
                
                // payment_method dihapus dulu karena belum ada di DB
                
                'description' => "Penjualan via AI: {$product->name}"
            ]);
            
            Log::info("✅ Transaksi AI Sukses: ID {$trx->id}");

            return "✅ BERHASIL (ID #{$trx->id}):\n" .
                   "Terjual {$qty} {$product->name}.\n" .
                   "💰 Omset: Rp " . number_format($totalOmset) . "\n" .
                   "📈 Profit: Rp " . number_format($profit);

        } catch (\Exception $e) {
            Log::error("Database Error: " . $e->getMessage());
            return "❌ ERROR SISTEM: " . $e->getMessage();
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
            return "✅ Bahan '{$args['name']}' tersimpan.";
        } catch (\Exception $e) { return "Gagal: " . $e->getMessage(); }
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
            return "✅ Produk '{$args['name']}' dibuat!";
        } catch (\Exception $e) { return "Gagal: " . $e->getMessage(); }
    }

    protected function get_product_list() {
        return Product::where('umkm_id', $this->umkm->id)->get()->map(fn($p) => "{$p->name}: Rp ".number_format($p->price)." (Stok: {$p->computed_stock})")->implode("\n");
    }

    protected function get_inventory_list() {
        return InventoryItem::where('umkm_id', $this->umkm->id)->get()->map(fn($i) => "{$i->name}: {$i->stock} {$i->unit}")->implode("\n");
    }

    // --- CORE GEMINI ---
    private function callGeminiApi($message, $history, $tools, $systemPrompt) {
        $contents = [];
        foreach ($history as $chat) {
            if (!empty($chat->message)) $contents[] = ['role' => 'user', 'parts' => [['text' => $chat->message]]];
            if (!empty($chat->response)) $contents[] = ['role' => 'model', 'parts' => [['text' => $chat->response]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $systemPrompt . "\n\nUser: " . $message]]];

        $payload = ["contents" => $contents];
        if (!empty($tools)) $payload["tools"] = [["function_declarations" => $tools]];

        try {
            $response = Http::post($this->baseUrl, $payload)->json();
            
            if(isset($response['error'])) {
                Log::error("GEMINI API ERROR: " . json_encode($response['error']));
                return "⚠️ GAGAL API: " . ($response['error']['message'] ?? 'Unknown');
            }

            $candidate = $response['candidates'][0]['content']['parts'][0] ?? [];

            if (isset($candidate['functionCall'])) {
                $fName = $candidate['functionCall']['name'];
                $fArgs = $candidate['functionCall']['args'] ?? [];
                
                $result = method_exists($this, $fName) ? $this->$fName($fArgs) : "Fungsi tidak ditemukan.";
                
                $contents[] = ['role' => 'model', 'parts' => [['functionCall' => $candidate['functionCall']]]];
                $contents[] = ['role' => 'function', 'parts' => [['functionResponse' => ['name' => $fName, 'response' => ['content' => $result]]]]];
                
                $final = Http::post($this->baseUrl, ["contents" => $contents, "tools" => [["function_declarations" => $tools]]])->json();
                
                if(isset($final['error'])) return "⚠️ Gagal Function: " . ($final['error']['message'] ?? 'Unknown');

                return $final['candidates'][0]['content']['parts'][0]['text'] ?? $result;
            }
            return $candidate['text'] ?? "Maaf, saya tidak mengerti.";
            
        } catch (\Exception $e) { return "Koneksi Error: " . $e->getMessage(); }
    }

    protected function handleGuestConsultation($message, $now) {
        return "Login dulu ya!";
    }
}