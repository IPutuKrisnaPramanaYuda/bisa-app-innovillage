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
        // PERBAIKAN 1: Ganti ke 1.5-flash yang stabil
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
                ["name" => "get_inventory_list", "description" => "Melihat stok bahan baku di gudang."],
                
                // PERBAIKAN 2: DAFTARKAN TOOL KEUANGAN DISINI!
                [
                    "name" => "get_financial_summary",
                    "description" => "Melihat laporan keuangan, omset, total pengeluaran (HPP), dan keuntungan bersih (profit) saat ini.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [], // Tidak butuh parameter
                    ]
                ],
                
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
        Waktu Server: {$now}
        
        ATURAN KERAS (WAJIB PATUH):
        1. JANGAN MENGARANG DATA. Gunakan Tools yang tersedia.
        2. Jika user bertanya 'Berapa omset hari ini?' atau 'Laporan keuangan', PANGGIL fungsi 'get_financial_summary'.
        3. Jika user input penjualan, panggil 'record_sale'.
        4. Jawablah dengan format yang rapi dan profesional.
        ";
        } else {
            $systemPrompt = "Ajak user buat toko.";
        }

        return $this->callGeminiApi($userMessage, $history, $tools, $systemPrompt);
    }

    // --- FUNGSI EKSEKUSI ---

    protected function get_financial_summary($args = []) {
        // Query database
        $transaksi = Transaction::where('umkm_id', $this->umkm->id)
                    ->where('type', 'IN')
                    ->get();
        
        $omset = $transaksi->sum('amount');
        $hpp = $transaksi->sum('cost_amount');
        $profit = $omset - $hpp;
        
        $countTrans = $transaksi->count();

        return json_encode([
            "status" => "success",
            "message" => "Data keuangan berhasil ditarik.",
            "data" => [
                "total_transaksi" => $countTrans . " kali",
                "total_omset" => "Rp " . number_format($omset, 0, ',', '.'),
                "total_modal_hpp" => "Rp " . number_format($hpp, 0, ',', '.'),
                "total_profit_bersih" => "Rp " . number_format($profit, 0, ',', '.')
            ]
        ]);
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

            $missingItems = [];
            foreach ($args['ingredients'] as $ing) {
                $item = InventoryItem::where('umkm_id', $this->umkm->id)
                            ->where('name', 'LIKE', '%' . $ing['item_name'] . '%')
                            ->first();

                if ($item) {
                    $product->ingredients()->attach($item->id, ['amount' => $ing['amount']]);
                } else {
                    $missingItems[] = $ing['item_name'];
                }
            }

            $msg = "✅ Produk '{$args['name']}' berhasil dibuat! ";
            if (!empty($missingItems)) {
                $msg .= "\n⚠️ Peringatan: Bahan berikut tidak ditemukan: " . implode(', ', $missingItems);
            } else {
                // Refresh model untuk hitung stok
                $product->refresh(); 
                $msg .= "Stok terhitung saat ini: " . $product->computed_stock . " porsi.";
            }
            return $msg;

        } catch (\Exception $e) { return "Gagal buat produk: " . $e->getMessage(); }
    }

    protected function record_sale($args) {
        $product = Product::with('ingredients')->where('umkm_id', $this->umkm->id)->where('name', 'LIKE', '%'.$args['product_name'].'%')->first();
        if(!$product) return "Produk '{$args['product_name']}' tidak ditemukan.";
        
        if ($product->computed_stock < $args['quantity']) {
             return "⚠️ Stok tidak cukup! Stok tersedia cuma {$product->computed_stock} porsi.";
        }

        $totalHPP = 0;
        foreach($product->ingredients as $ing) {
            $needed = $ing->pivot->amount * $args['quantity'];
            $totalHPP += ($ing->price_per_unit * $needed);
            $ing->decrement('stock', $needed);
        }
        
        $totalOmset = $product->price * $args['quantity'];

        Transaction::create([
            'umkm_id' => $this->umkm->id,
            'product_id' => $product->id,
            'amount' => $totalOmset,
            'cost_amount' => $totalHPP,
            'quantity' => $args['quantity'],
            'type' => 'IN',
            'date' => now(),
            'status' => 'paid',
            'description' => "Penjualan {$product->name}"
        ]);
        
        $profit = $totalOmset - $totalHPP;

        return "✅ Terjual {$args['quantity']} {$product->name}.\n💰 Omset: Rp " . number_format($totalOmset) . "\n📉 HPP: Rp " . number_format($totalHPP) . "\n📈 Profit: Rp " . number_format($profit);
    }

    protected function get_inventory_list() {
        $items = InventoryItem::where('umkm_id', $this->umkm->id)->get();
        if($items->isEmpty()) return "Gudang kosong.";
        return $items->map(fn($i) => "- {$i->name}: {$i->stock} {$i->unit}")->implode("\n");
    }

    protected function get_product_list() {
        $products = Product::with('ingredients')->where('umkm_id', $this->umkm->id)->get();
        if($products->isEmpty()) return "Belum ada produk.";
        
        return $products->map(function($p) {
            $stokInfo = $p->ingredients->count() > 0 
                ? "Stok: {$p->computed_stock} (Resep)" 
                : "Stok: {$p->stock} (Manual)";
            return "- {$p->name}: Rp " . number_format($p->price) . " | {$stokInfo}";
        })->implode("\n");
    }

    protected function add_inventory_item($args) {
        try {
            Log::info('AI mencoba nambah inventori:', $args);

            if (empty($args['name']) || empty($args['stock'])) {
                return "Gagal: Nama barang dan jumlah stok wajib diisi.";
            }

            $category = isset($args['category']) ? strtolower($args['category']) : 'bahan';
            $unit = $args['unit'] ?? 'pcs';
            $price = $args['price_per_unit'] ?? 0;

            InventoryItem::create([
                'umkm_id' => $this->umkm->id,
                'name' => $args['name'],
                'category' => $category,
                'stock' => $args['stock'],
                'unit' => $unit,
                'price_per_unit' => $price
            ]);

            return "✅ SUKSES: Bahan '{$args['name']}' berhasil disimpan (Stok: {$args['stock']} {$unit}).";

        } catch (\Exception $e) {
            Log::error('Gagal tambah inventori: ' . $e->getMessage());
            return "❌ ERROR SISTEM: " . $e->getMessage(); 
        }
    }

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
        // Perbaikan format history agar tidak error di Gemini
        foreach ($history as $chat) {
            if (!empty($chat->message)) $contents[] = ['role' => 'user', 'parts' => [['text' => $chat->message]]];
            // Respon model kadang kosong kalau function call, jadi perlu dicek
            if (!empty($chat->response)) $contents[] = ['role' => 'model', 'parts' => [['text' => $chat->response]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $prompt . "\n\nUser: " . $message]]];

        $payload = ["contents" => $contents];
        if (!empty($tools)) $payload["tools"] = [["function_declarations" => $tools]];

        try {
            $response = Http::post($this->baseUrl, $payload)->json();
            
            // Debugging kalau error
            if (isset($response['error'])) {
                Log::error("Gemini API Error: " . json_encode($response['error']));
                return "Maaf, ada gangguan pada AI. Coba lagi nanti.";
            }

            $candidate = $response['candidates'][0]['content']['parts'][0] ?? [];

            // Jika AI minta panggil fungsi
            if (isset($candidate['functionCall'])) {
                $functionName = $candidate['functionCall']['name'];
                $args = $candidate['functionCall']['args'] ?? [];
                
                // Panggil fungsi PHP
                $resultData = method_exists($this, $functionName) ? $this->$functionName($args) : "Fungsi tidak ditemukan.";
                
                // Kirim hasil fungsi balik ke AI
                $contents[] = ['role' => 'model', 'parts' => [['functionCall' => $candidate['functionCall']]]];
                $contents[] = ['role' => 'function', 'parts' => [['functionResponse' => ['name' => $functionName, 'response' => ['content' => $resultData]]]]];
                
                // Request kedua untuk dapat jawaban teks final
                $finalResponse = Http::post($this->baseUrl, ["contents" => $contents, "tools" => [["function_declarations" => $tools]]])->json();
                return $finalResponse['candidates'][0]['content']['parts'][0]['text'] ?? $resultData;
            }
            
            return $candidate['text'] ?? "Saya tidak mengerti.";
            
        } catch (\Exception $e) { 
            Log::error("AI Exception: " . $e->getMessage());
            return "Error sistem: " . $e->getMessage(); 
        }
    }
}