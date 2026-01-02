<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\InventoryItem;
use App\Models\Dataset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AiService
{
    protected $apiKey;
    protected $baseUrl;
    protected $umkm;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        // Menggunakan model Gemini 2.0 Flash yang lebih stabil dan cepat
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$this->apiKey}";
        $this->umkm = null;
    }

    /**
     * Proses pesan utama dengan pemisahan mode dan status login
     */
    public function processMessage($userMessage, $history = [], $mode = 'regular')
    {
        $user = Auth::user();

        // 1. Logika untuk Pengguna yang Belum Login (Guest)
        if (!$user) {
            return $this->handleGuestConsultation($userMessage);
        }

        // 2. Logika untuk Pengguna Login tapi Belum Memiliki Toko
        if (!$user->umkm) {
            return "Halo Bos {$user->name}! 👋 Saya perhatikan toko Anda belum terdaftar. Silakan buat profil toko terlebih dahulu di menu 'Buat Toko' agar saya bisa membantu mengelola stok dan laporan keuangan Anda!";
        }

        $this->umkm = $user->umkm;

        // 3. Konfigurasi Tools dan System Prompt Berdasarkan Mode
        if ($mode === 'research') {
            // MODE RESEARCH: Fokus mencari informasi di Jurnal/PDF
            $tools = [
                [
                    "name" => "search_knowledge_base",
                    "description" => "Mencari informasi, teori, nama orang, atau isi dokumen dari database Jurnal & PDF yang diupload.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "keyword" => ["type" => "STRING", "description" => "Kata kunci pencarian"]
                        ],
                        "required" => ["keyword"]
                    ]
                ]
            ];

            $systemPrompt = "
            Kamu adalah 'Research Assistant'. Kamu sedang dalam MODE RESEARCH.
            Tugasmu HANYA mencari informasi dari Jurnal/Dokumen yang tersedia di database.
            WAJIB menggunakan tool 'search_knowledge_base' untuk setiap pertanyaan user.
            Jika informasi tidak ditemukan di dokumen, katakan: 'Maaf Bos, informasi tersebut tidak ditemukan dalam koleksi jurnal Anda.'
            ";
        } else {
            // MODE REGULAR: Fokus pada operasional toko UMKM (ERP)
            $tools = [
                ["name" => "get_product_list", "description" => "Melihat daftar produk toko."],
                ["name" => "get_inventory_report", "description" => "Melihat stok bahan baku di gudang."],
                ["name" => "get_financial_summary", "description" => "Melihat ringkasan omset dan laba rugi."],
                ["name" => "get_transaction_list", "description" => "Melihat riwayat transaksi terakhir."],
                [
                    "name" => "record_sale",
                    "description" => "Mencatat transaksi penjualan produk.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "product_name" => ["type" => "STRING"],
                            "quantity" => ["type" => "INTEGER"]
                        ],
                        "required" => ["product_name", "quantity"]
                    ]
                ],
                [
                    "name" => "record_expense",
                    "description" => "Mencatat biaya pengeluaran toko.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "description" => ["type" => "STRING"],
                            "amount" => ["type" => "INTEGER"]
                        ],
                        "required" => ["description", "amount"]
                    ]
                ],
            ];

            $now = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY, HH:mm');
            $systemPrompt = "
            Kamu adalah 'Growth AI', Partner Bisnis Cerdas untuk toko '{$this->umkm->name}'.
            Waktu saat ini: {$now}.
            Gunakan data toko untuk menjawab. Jika user bertanya tentang strategi umum, kamu boleh memberi saran bisnis.
            Panggil user dengan sebutan 'Bos'.
            ";
        }

        return $this->callGeminiApi($userMessage, $history, $tools, $systemPrompt);
    }

    /**
     * Helper untuk memanggil API Gemini dan menangani Tool Calling secara rekursif
     */
    private function callGeminiApi($message, $history, $tools, $prompt)
    {
        $contents = [];
        // Format history chat
        foreach ($history as $chat) {
            if (!empty($chat->message)) $contents[] = ['role' => 'user', 'parts' => [['text' => $chat->message]]];
            if (!empty($chat->response)) $contents[] = ['role' => 'model', 'parts' => [['text' => $chat->response]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $prompt . "\n\nUser: " . $message]]];

        $payload = [
            "contents" => $contents,
            "tools" => [["function_declarations" => $tools]]
        ];

        try {
            $response = Http::post($this->baseUrl, $payload)->json();
            $candidate = $response['candidates'][0]['content']['parts'][0] ?? [];

            // Jika AI memutuskan untuk memanggil fungsi (Tool Use)
            if (isset($candidate['functionCall'])) {
                $functionName = $candidate['functionCall']['name'];
                $args = $candidate['functionCall']['args'] ?? [];

                // Eksekusi fungsi lokal
                $resultData = method_exists($this, $functionName) ? $this->$functionName($args) : "Fungsi belum tersedia.";

                // Kirim balik hasil fungsi ke AI untuk diformat menjadi kalimat
                $contents[] = ['role' => 'model', 'parts' => [['functionCall' => $candidate['functionCall']]]];
                $contents[] = ['role' => 'function', 'parts' => [['functionResponse' => ['name' => $functionName, 'response' => ['content' => $resultData]]]]];

                $finalResponse = Http::post($this->baseUrl, ["contents" => $contents])->json();
                return $finalResponse['candidates'][0]['content']['parts'][0]['text'] ?? $resultData;
            }

            return $candidate['text'] ?? "Maaf Bos, saya sedang tidak stabil. Bisa diulang?";
        } catch (\Exception $e) {
            return "Koneksi ke otak AI terputus: " . $e->getMessage();
        }
    }

    /**
     * Mode Konsultasi untuk tamu yang belum login
     */
    protected function handleGuestConsultation($message)
    {
        $systemPrompt = "Kamu adalah Growth AI Consultant. User ini belum login. Berikan tips bisnis UMKM secara umum. Ajak mereka login untuk mengelola toko.";
        $payload = ["contents" => [["role" => "user", "parts" => [["text" => $systemPrompt . "\nUser: " . $message]]]]];
        
        $response = Http::post($this->baseUrl, $payload)->json();
        return $response['candidates'][0]['content']['parts'][0]['text'] ?? "Halo! Silakan login untuk fitur lengkap.";
    }

    // =============================================================
    // TOOLS: KNOWLEDGE BASE (RESEARCH MODE)
    // =============================================================

    protected function search_knowledge_base($args)
    {
        $keyword = strtolower($args['keyword'] ?? '');
        $triggers = ['daftar', 'semua', 'list', 'apa saja', 'jurnal'];
        
        $isGeneral = false;
        foreach ($triggers as $t) if (str_contains($keyword, $t)) $isGeneral = true;

        if ($isGeneral || empty($keyword)) {
            $results = Dataset::orderBy('id', 'desc')->take(5)->get();
            $intro = "📂 **Daftar Dokumen Tersedia:**\n\n";
        } else {
            $results = Dataset::where('extracted_text', 'LIKE', "%{$keyword}%")
                        ->orWhere('title', 'LIKE', "%{$keyword}%")
                        ->orderBy('id', 'desc')->take(3)->get();
            $intro = "🔍 **Hasil Pencarian '{$keyword}':**\n\n";
        }

        if ($results->isEmpty()) return "Tidak ditemukan dokumen yang relevan.";

        $output = $intro;
        foreach ($results as $doc) {
            $snippet = Str::limit($doc->extracted_text, 500, '...');
            $output .= "📄 **{$doc->title}** (ID: {$doc->id})\n💡 \"{$snippet}\"\n---\n";
        }
        return $output;
    }

    // =============================================================
    // TOOLS: OPERASIONAL TOKO (REGULAR MODE)
    // =============================================================

    protected function get_product_list()
    {
        $products = Product::where('umkm_id', $this->umkm->id)->get();
        if ($products->isEmpty()) return "Katalog produk Anda masih kosong.";
        
        $output = "Daftar Produk Toko:\n";
        foreach ($products as $p) {
            $stok = $p->ingredients()->exists() ? $p->computed_stock : $p->stock;
            $output .= "- {$p->name}: Rp " . number_format($p->price) . " (Stok: $stok)\n";
        }
        return $output;
    }

    protected function get_financial_summary()
    {
        $omset = Transaction::where('umkm_id', $this->umkm->id)->where('type', 'IN')->sum('amount');
        $pengeluaran = Transaction::where('umkm_id', $this->umkm->id)->where('type', 'OUT')->sum('amount');
        $laba = $omset - $pengeluaran;

        return "Ringkasan Keuangan:\n- Omset: Rp " . number_format($omset) . "\n- Pengeluaran: Rp " . number_format($pengeluaran) . "\n- Estimasi Laba: Rp " . number_format($laba);
    }

    protected function get_inventory_report()
    {
        $items = InventoryItem::where('umkm_id', $this->umkm->id)->get();
        if ($items->isEmpty()) return "Stok gudang bahan baku kosong.";
        
        $output = "Laporan Stok Gudang:\n";
        foreach ($items as $i) $output .= "- {$i->name}: {$i->stock} {$i->unit}\n";
        return $output;
    }

    protected function record_sale($args)
    {
        $product = Product::where('umkm_id', $this->umkm->id)->where('name', 'LIKE', '%' . $args['product_name'] . '%')->first();
        if (!$product) return "Maaf Bos, produk '{$args['product_name']}' tidak ditemukan di katalog.";

        $total = $product->price * $args['quantity'];
        
        // Buat Transaksi
        Transaction::create([
            'umkm_id' => $this->umkm->id,
            'product_id' => $product->id,
            'amount' => $total,
            'quantity' => $args['quantity'],
            'type' => 'IN',
            'date' => now(),
            'description' => "Penjualan via AI: {$product->name}"
        ]);

        // Update Saldo UMKM
        $this->umkm->increment('balance', $total);
        // Update Stok (Jika bukan produk resep/bahan baku)
        if (!$product->ingredients()->exists()) $product->decrement('stock', $args['quantity']);

        return "Berhasil mencatat penjualan {$args['quantity']} unit {$product->name}. Total omset masuk: Rp " . number_format($total);
    }
}