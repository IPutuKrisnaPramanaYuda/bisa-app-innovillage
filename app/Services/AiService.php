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
        // Tetap pakai 1.5 Flash agar stabil & fungsi kasirnya jalan
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$this->apiKey}";
        $this->umkm = null;
    }

    public function processMessage($userMessage, $history = [])
    {
        $user = Auth::user();
        $now = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm');

        // 1. MODE TAMU (Belum Login) -> Jadi Informan Desa
        if (!$user) {
            return $this->handleGuestConsultation($userMessage, $now);
        }

        $this->umkm = $user->umkm;
        
        // 2. MODE ERP (Punya Toko) -> Bisa Jualan & Cek Stok
        if ($this->umkm) {
            $tools = [
                // READ TOOLS
                ["name" => "get_product_list", "description" => "Melihat daftar produk jualan."],
                ["name" => "get_inventory_list", "description" => "Melihat stok bahan baku gudang."],
                ["name" => "get_financial_summary", "description" => "Melihat laporan keuangan omset dan profit."],

                // ACTION TOOLS
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
            Kamu adalah Asisten Cerdas Toko 'BISA'. Waktu Server: {$now}.
            
            ATURAN EKSEKUSI (KASIR):
            1. Bersihkan input: '10 ribu' -> 10000. 'Jual Kopi 5' -> record_sale('Kopi', 5).
            2. Jangan halusinasi. Panggil function record_sale dulu baru bicara.
            3. Gunakan get_financial_summary untuk data omset.
            ";
            
            return $this->callGeminiApi($userMessage, $history, $tools, $systemPrompt);

        } else {
            // 3. MODE MEMBER NON-TOKO -> Jadi Informan Desa + Ajak Bikin Toko
            $systemPrompt = "
            Kamu adalah Asisten Virtual Desa Bengkala.
            User ini sudah login tapi belum memiliki toko UMKM.
            
            Tugasmu:
            1. Menjawab pertanyaan seputar Desa Bengkala (Wisata, Budaya Kolok, Berita).
            2. Jika user bertanya fitur bisnis/kasir, ajak mereka mendaftarkan UMKM di menu Profil agar bisa menggunakan fitur ERP.
            3. Jawab dengan ramah dan informatif.
            ";

            // Panggil API tanpa tools (Chat Only)
            return $this->callGeminiApi($userMessage, $history, [], $systemPrompt);
        }
    }

    // --- FUNGSI EKSEKUSI (OTAK PHP UNTUK KASIR) ---

    protected function get_financial_summary($args = []) {
        $transaksi = Transaction::where('umkm_id', $this->umkm->id)->where('type', 'IN')->get();
        return "📊 LAPORAN:\n- Omset: Rp " . number_format($transaksi->sum('amount')) . "\n- Profit: Rp " . number_format($transaksi->sum('amount') - $transaksi->sum('cost_amount'));
    }

    protected function record_sale($args) {
        // 1. Bersihkan Nama
        $cleanName = trim(str_ireplace(['pcs', 'buah', 'bungkus', 'porsi', 'gelas', 'cup'], '', $args['product_name']));

        // 2. Cari Produk
        $product = Product::with('ingredients')
                    ->where('umkm_id', $this->umkm->id)
                    ->where('name', 'LIKE', '%' . $cleanName . '%')
                    ->first();

        // Cari Kebalikan (Smart Search)
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

        // 4. Hitung HPP
        $totalHPP = 0;
        foreach($product->ingredients as $ing) {
            $needed = $ing->pivot->amount * $qty;
            $totalHPP += ($ing->price_per_unit * $needed);
            $ing->decrement('stock', $needed);
        }
        
        $totalOmset = $product->price * $qty;
        $profit = $totalOmset - $totalHPP;

        // 5. SIMPAN DATABASE (FIX UNTUK DASHBOARD)
        try {
            $trx = Transaction::create([
                'umkm_id' => $this->umkm->id,
                'product_id' => $product->id,
                'amount' => $totalOmset,
                'cost_amount' => $totalHPP,
                'quantity' => $qty,
                'type' => 'IN',
                'date' => now(),
                
                // --- FIX DASHBOARD ---
                'status' => 'paid',       // Status 'paid' agar tidak error enum
                'buyer_id' => Auth::id(), // ISI ID SENDIRI AGAR MUNCUL DI TABLE (Not Null)
                'created_at' => now(),    // AGAR MUNCUL DI GRAFIK HARI INI
                'updated_at' => now(),
                
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
        
        // 1. FILTER HISTORY
        foreach ($history as $chat) {
            if (!empty($chat->message)) $contents[] = ['role' => 'user', 'parts' => [['text' => $chat->message]]];
            if (!empty($chat->response)) $contents[] = ['role' => 'model', 'parts' => [['text' => $chat->response]]];
        }
        
        // 2. MASUKKAN PROMPT BARU
        $contents[] = ['role' => 'user', 'parts' => [['text' => $systemPrompt . "\n\nUser: " . $message]]];

        $payload = ["contents" => $contents];
        if (!empty($tools)) $payload["tools"] = [["function_declarations" => $tools]];

        try {
            $response = Http::post($this->baseUrl, $payload)->json();
            
            // Cek Error Awal
            if(isset($response['error'])) {
                Log::error("GEMINI REQUEST ERROR: " . json_encode($response['error']));
                return "⚠️ Maaf, sistem AI sedang sibuk. Coba lagi sebentar.";
            }

            $candidate = $response['candidates'][0]['content']['parts'][0] ?? [];

            // 3. HANDLE FUNCTION CALL (BAGIAN YANG DIPERBAIKI)
            if (isset($candidate['functionCall'])) {
                $fName = $candidate['functionCall']['name'];
                $fArgs = $candidate['functionCall']['args'] ?? [];
                
                // --- PERBAIKAN BUG JSON [] vs {} ---
                // Kita simpan dulu functionCall asli
                $fcData = $candidate['functionCall'];
                
                // Kalau args kosong atau array, paksa jadi OBJECT (stdClass)
                // Ini biar json_encode menghasilkan "{}" bukan "[]"
                if (empty($fcData['args']) || is_array($fcData['args'])) {
                    $fcData['args'] = (object)($fcData['args'] ?? []);
                }

                // Eksekusi PHP
                $result = method_exists($this, $fName) ? $this->$fName($fArgs) : "Fungsi tidak ditemukan.";
                
                // Kirim balik ke AI dengan format yang sudah diperbaiki (fcData)
                $contents[] = ['role' => 'model', 'parts' => [['functionCall' => $fcData]]];
                $contents[] = ['role' => 'function', 'parts' => [['functionResponse' => ['name' => $fName, 'response' => ['content' => $result]]]]];
                
                // Request kedua (Jawaban Final)
                $final = Http::post($this->baseUrl, ["contents" => $contents, "tools" => [["function_declarations" => $tools]]])->json();
                
                if(isset($final['error'])) {
                    Log::error("GEMINI FINAL ERROR: " . json_encode($final['error']));
                    return "⚠️ Gagal memproses data: " . ($final['error']['message'] ?? 'Unknown');
                }

                return $final['candidates'][0]['content']['parts'][0]['text'] ?? $result;
            }
            
            return $candidate['text'] ?? "Maaf, saya tidak mengerti.";
            
        } catch (\Exception $e) { 
            Log::error("CONNECTION ERROR: " . $e->getMessage());
            return "Koneksi Error: " . $e->getMessage(); 
        }
    }

    // Fungsi Tamu (Informan Desa)
    protected function handleGuestConsultation($message, $now) {
        $systemPrompt = "
        Kamu adalah Customer Service Desa Bengkala (Desa Inklusi & Bahasa Isyarat Kolok).
        Tugas: Jawab pertanyaan turis seputar wisata desa, budaya tuli-bisu (Kolok), dan produk lokal.
        Jangan bahas fitur kasir atau toko jika user belum login.
        ";
        
        // Panggil API tanpa history & tools
        return $this->callGeminiApi($message, [], [], $systemPrompt);
    }
}