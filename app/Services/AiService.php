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
        // SAYA GANTI KE 1.5 KARENA LEBIH PINTAR UNTUK LOGIKA UANG & STOK
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
                // 1. CEK DATA (READ)
                ["name" => "get_product_list", "description" => "Melihat daftar produk jualan dan stok tersedia."],
                ["name" => "get_inventory_list", "description" => "Melihat stok bahan baku mentah di gudang."],
                
                // 2. CEK DUIT (FINANCE) - INI YANG KEMARIN HILANG
                [
                    "name" => "get_financial_summary",
                    "description" => "Melihat laporan keuangan: Omset (Uang Masuk), HPP (Modal), dan Profit (Untung Bersih).",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [], // Tanpa parameter
                    ]
                ],

                // 3. TAMBAH BARANG (CREATE)
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

                // 4. CATAT PENJUALAN (TRANSACTION)
                [
                    "name" => "record_sale",
                    "description" => "Mencatat penjualan produk ke pembeli.",
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

            // SYSTEM PROMPT SAYA PERTEGAS BIAR GAK "HALU"
            $systemPrompt = "
            Kamu adalah Asisten Cerdas Toko 'BISA'.
            Waktu: {$now}.

            ATURAN PENTING (LOGIKA MANUSIA):
            1. Jika user bilang '10 ribu', itu artinya angka 10000. Jangan buat produk bernama 'ribu'.
            2. Jika user bilang 'jual kopi 5', artinya product_name='kopi', quantity=5.
            3. HINDARI kata 'pcs', 'buah', 'bungkus' masuk ke nama produk.
            4. JANGAN PERNAH MENJAWAB 'Berhasil dicatat' kalau kamu belum memanggil Function Tool.
            5. Selalu panggil 'get_financial_summary' jika user tanya omset/untung/laporan.
            ";
        } else {
            return $this->callGeminiApi($userMessage, $history, [], "Arahkan user untuk mendaftarkan UMKM-nya dulu di menu profil.");
        }

        return $this->callGeminiApi($userMessage, $history, $tools, $systemPrompt);
    }

    // --- FUNGSI EKSEKUSI (OTAK PHP) ---

    protected function get_financial_summary($args = []) {
        $transaksi = Transaction::where('umkm_id', $this->umkm->id)->where('type', 'IN')->get();
        
        $omset = $transaksi->sum('amount');
        $hpp = $transaksi->sum('cost_amount'); // Pastikan kolom ini ada di DB!
        $profit = $omset - $hpp;
        $count = $transaksi->count();
        
        return "📊 LAPORAN KEUANGAN SAAT INI:\n" .
               "- Total Transaksi: {$count} kali\n" .
               "- Omset (Masuk): Rp " . number_format($omset, 0, ',', '.') . "\n" .
               "- Modal HPP (Keluar): Rp " . number_format($hpp, 0, ',', '.') . "\n" .
               "- ✅ PROFIT BERSIH: Rp " . number_format($profit, 0, ',', '.');
    }

    protected function record_sale($args) {
        // PEMBERSIH KATA: Hapus kata sampah biar pencarian akurat
        $cleanName = str_ireplace(['pcs', 'buah', 'bungkus', 'porsi', 'gelas'], '', $args['product_name']);
        $cleanName = trim($cleanName);

        $product = Product::with('ingredients')
                    ->where('umkm_id', $this->umkm->id)
                    ->where('name', 'LIKE', '%' . $cleanName . '%') // Cari yang mirip
                    ->first();

        if(!$product) {
            // Coba cari lagi tanpa cleaning (siapa tau nama produknya emang 'Es Buah')
            $product = Product::with('ingredients')->where('umkm_id', $this->umkm->id)->where('name', 'LIKE', '%'.$args['product_name'].'%')->first();
            if(!$product) return "⚠️ Gagal: Produk '{$cleanName}' tidak ditemukan di sistem. Pastikan nama sesuai daftar produk.";
        }
        
        $qty = $args['quantity'];

        // Cek Stok
        if ($product->computed_stock < $qty) {
             return "⛔ Stok Kurang! Stok '{$product->name}' cuma sisa {$product->computed_stock}. Cek bahan baku di gudang.";
        }

        // Kurangi Stok & Hitung HPP
        $totalHPP = 0;
        foreach($product->ingredients as $ing) {
            $needed = $ing->pivot->amount * $qty;
            $totalHPP += ($ing->price_per_unit * $needed);
            $ing->decrement('stock', $needed);
        }
        
        $totalOmset = $product->price * $qty;
        $profit = $totalOmset - $totalHPP;

        // Simpan Transaksi (Pakai try-catch biar ketahuan kalau error DB)
        try {
            Transaction::create([
                'umkm_id' => $this->umkm->id,
                'product_id' => $product->id,
                'amount' => $totalOmset,
                'cost_amount' => $totalHPP, // Kolom ini wajib ada!
                'quantity' => $qty,
                'type' => 'IN',
                'date' => now(),
                'status' => 'paid',
                'description' => "Penjualan {$product->name} via AI"
            ]);
        } catch (\Exception $e) {
            Log::error("Database Error: " . $e->getMessage());
            return "❌ Error Sistem: Gagal menyimpan ke database. " . $e->getMessage();
        }

        return "✅ BERHASIL: Terjual {$qty} {$product->name}.\n" .
               "💰 Uang Masuk: Rp " . number_format($totalOmset) . "\n" .
               "📈 Untung Bersih: Rp " . number_format($profit);
    }

    protected function add_inventory_item($args) {
        // FIX HARGA: Kalau AI kirim "10.000" jadi string, ubah ke angka
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
            return "✅ Bahan '{$args['name']}' berhasil disimpan! Stok: {$args['stock']} {$args['unit']}.";
        } catch (\Exception $e) { return "Gagal simpan: " . $e->getMessage(); }
    }

    protected function create_product_with_recipe($args) {
        // Logic sama seperti punya Bos, cuma dirapikan error handlingnya
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
            
            $msg = "✅ Menu '{$args['name']}' jadi! Harga: Rp " . number_format($args['price']);
            if($missing) $msg .= "\n⚠️ Tapi bahan ini belum ada di gudang: " . implode(', ', $missing);
            
            return $msg;
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

    // --- CORE GEMINI (JANGAN UBAH INI) ---

    private function callGeminiApi($message, $history, $tools, $systemPrompt) {
        $contents = [];
        // Format history yang aman
        foreach ($history as $chat) {
            if ($chat->message) $contents[] = ['role' => 'user', 'parts' => [['text' => $chat->message]]];
            if ($chat->response) $contents[] = ['role' => 'model', 'parts' => [['text' => $chat->response]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $systemPrompt . "\n\nUser bilang: " . $message]]];

        $payload = ["contents" => $contents];
        if ($tools) $payload["tools"] = [["function_declarations" => $tools]];

        try {
            $response = Http::post($this->baseUrl, $payload)->json();
            
            // Cek Error API
            if(isset($response['error'])) return "Maaf, AI sedang pusing (Error API). Coba lagi.";

            $candidate = $response['candidates'][0]['content']['parts'][0] ?? [];

            // Jika AI minta panggil fungsi
            if (isset($candidate['functionCall'])) {
                $fName = $candidate['functionCall']['name'];
                $fArgs = $candidate['functionCall']['args'] ?? [];
                
                // Eksekusi PHP
                $result = method_exists($this, $fName) ? $this->$fName($fArgs) : "Fungsi tidak ditemukan.";
                
                // Kirim balik hasil ke AI
                $contents[] = ['role' => 'model', 'parts' => [['functionCall' => $candidate['functionCall']]]];
                $contents[] = ['role' => 'function', 'parts' => [['functionResponse' => ['name' => $fName, 'response' => ['content' => $result]]]]];
                
                // Minta jawaban final
                $final = Http::post($this->baseUrl, ["contents" => $contents, "tools" => [["function_declarations" => $tools]]])->json();
                return $final['candidates'][0]['content']['parts'][0]['text'] ?? $result;
            }
            
            return $candidate['text'] ?? "Maaf, saya tidak mengerti.";
            
        } catch (\Exception $e) { return "Koneksi Error: " . $e->getMessage(); }
    }

    protected function handleGuestConsultation($message, $now) {
        // Versi simple untuk tamu
        return "Silakan login dulu untuk akses fitur toko.";
    }
}