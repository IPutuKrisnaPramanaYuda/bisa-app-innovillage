<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\ChatSession;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Umkm;
use App\Services\AiService;

class ChatController extends Controller
{
    protected $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $currentSessionId = $request->get('s', session('current_chat_session_id', (string) Str::uuid()));
        session(['current_chat_session_id' => $currentSessionId]);
        
        $history = collect();
        if ($user) {
            $history = ChatSession::where('user_id', $user->id)
                ->select('session_id', 'message', 'created_at')
                ->latest()
                ->get()
                ->unique('session_id');
        }

        $currentChats = ChatSession::where('session_id', $currentSessionId)->oldest()->get();
        return view('dashboard_ai', compact('currentChats', 'history', 'currentSessionId'));
    }

    // --- LOGIKA UTAMA ---
    public function sendMessage(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        $user = auth()->user();
        $messageRaw = $request->message;
        $messageLower = strtolower($messageRaw);

        // 1. DETEKSI PENJUALAN
        $qty = 0;
        $productName = '';
        $isSales = false;

        // Pola A: "Jual 5 Kopi"
        if (preg_match('/(laku|jual|terjual)\s+(\d+)\s+(.+)/i', $messageRaw, $matches)) {
            $qty = $matches[2];
            $productName = trim($matches[3]);
            $isSales = true;
        }
        // Pola B: "Kopi 5 terjual"
        elseif (preg_match('/(.+?)\s+(\d+)\s*(laku|jual|terjual|pcs|porsi|gelas|bks)/i', $messageRaw, $matches)) {
            $productName = trim($matches[1]);
            $qty = $matches[2];
            $isSales = true;
        }

        if ($isSales) {
            if (!$user) return response()->json(['success' => true, 'ai_response' => "Login dulu Bos."]);
            $productName = str_replace(['misal ', 'contoh ', 'tolong '], '', strtolower($productName));
            
            // Panggil Helper Penjualan
            return $this->handleSalesInput($user, $qty, $productName);
        }

        // 2. CEK KEUANGAN
        if (str_contains($messageLower, 'omzet') || str_contains($messageLower, 'pendapatan') || str_contains($messageLower, 'uang masuk')) {
             if (!$user) return response()->json(['success' => true, 'ai_response' => "Login dulu ya Bos."]);
             return $this->handleCheckFinance($user);
        }

        // 3. AI NORMAL (Saran/Tanya Jawab)
        if ($user && (str_contains($messageLower, 'saran') || str_contains($messageLower, 'analisa') || str_contains($messageLower, 'stok'))) {
            $businessContext = $this->getBusinessSummary($user);
            $messageRaw .= "\n\n[SYSTEM DATA: $businessContext]";
        }

        // AI PROCESS
        $sessionId = session('current_chat_session_id', (string) Str::uuid());
        session(['current_chat_session_id' => $sessionId]);
        
        $history = collect();
        if ($user) {
            $history = ChatSession::where('user_id', $user->id)->where('session_id', $sessionId)->oldest()->get();
        }

        $mode = $request->input('mode', 'regular'); 

        try {
            $aiReply = $this->aiService->processMessage($messageRaw, $history, $mode);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'AI Sedang Gangguan.']);
        }

        if ($user) {
            ChatSession::create([
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'message' => $request->message,
                'response' => $aiReply,
            ]);
        }

        return response()->json(['success' => true, 'user_message' => $request->message, 'ai_response' => $aiReply]);
    }

    // --- HELPER ID TOKO ---
    private function getUmkmId($user)
    {
        if (!empty($user->umkm_id)) return $user->umkm_id;
        $umkm = Umkm::where('user_id', $user->id)->first();
        return $umkm ? $umkm->id : null;
    }

    // --- HELPER TRANSAKSI DENGAN CEK STOK ---
    private function handleSalesInput($user, $qty, $productName)
    {
        $umkmId = $this->getUmkmId($user);
        if (!$umkmId) return response()->json(['success' => true, 'ai_response' => "⚠️ Akun Bos tidak terhubung ke Toko UMKM."]);
        
        $product = Product::where('umkm_id', $umkmId)
                    ->where('name', 'LIKE', "%{$productName}%")
                    ->with('ingredients') 
                    ->first();

        if (!$product) {
            return response()->json(['success' => true, 'ai_response' => "⚠️ Produk **'$productName'** tidak ditemukan."]);
        }

        // --- VALIDASI STOK (SATPAM) ---
        // Kita gunakan 'computed_stock' untuk mengecek stok nyata (termasuk bahan baku)
        // Refresh model agar data stok paling update
        $currentStock = $product->refresh()->computed_stock;

        if ($currentStock < $qty) {
            return response()->json([
                'success' => true, 
                'ai_response' => "⛔ **Transaksi Ditolak!**\n" .
                                 "Stok **$product->name** tidak cukup.\n" .
                                 "Tersedia: **$currentStock**\n" .
                                 "Diminta: **$qty**\n\n" .
                                 "Silakan belanja bahan baku atau update stok dulu Bos."
            ]);
        }

        // EKSEKUSI JIKA STOK CUKUP
        try {
            DB::transaction(function () use ($umkmId, $product, $qty) {
                
                // 1. Kurangi Stok
                if ($product->ingredients->count() > 0) {
                    foreach ($product->ingredients as $ingredient) {
                        $qtyNeeded = $ingredient->pivot->amount * $qty;
                        $ingredient->decrement('stock', $qtyNeeded);
                    }
                } else {
                    $product->decrement('stock', $qty);
                }

                // 2. Simpan Transaksi (Type OUT = Penjualan)
                $totalOmset = $product->price * $qty;
                $totalHPP = ($product->cost_price ?? 0) * $qty;

                Transaction::create([
                    'umkm_id'    => $umkmId,
                    'type'       => 'OUT',
                    'product_id' => $product->id,
                    'quantity'   => $qty,
                    'amount'     => $totalOmset,
                    'cost_amount'=> $totalHPP,
                    'date'       => now()->toDateString(),
                    'description'=> "AI Sales: $qty $product->name",
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                // 3. Update Saldo
                $umkm = Umkm::find($umkmId);
                if($umkm) $umkm->increment('balance', $totalOmset);
            });

        } catch (\Exception $e) {
            return response()->json(['success' => true, 'ai_response' => "❌ Error Database: " . $e->getMessage()]);
        }

        // Laporan Sukses
        $totalPrice = $product->price * $qty;
        $sisaStok = $product->refresh()->computed_stock; 

        $reply = "✅ **Penjualan Berhasil!**\n" .
                 "📦 Barang: **$product->name** (x$qty)\n" .
                 "💰 Omzet: **Rp " . number_format($totalPrice, 0, ',', '.') . "**\n" .
                 "📉 Sisa Stok: **$sisaStok**\n" .
                 "_(Data sudah masuk Laporan)_";

        $sessionId = session('current_chat_session_id', (string) Str::uuid());
        ChatSession::create([
            'user_id' => $user->id, 'session_id' => $sessionId,
            'message' => "Jual $qty $product->name", 'response' => $reply
        ]);

        return response()->json(['success' => true, 'ai_response' => $reply]);
    }

    private function handleCheckFinance($user)
    {
        $umkmId = $this->getUmkmId($user);
        if (!$umkmId) return response()->json(['success' => true, 'ai_response' => "Toko tidak ditemukan."]);

        $today = Transaction::where('umkm_id', $umkmId)->where('type', 'OUT')->whereDate('created_at', now())->sum('amount');
        $month = Transaction::where('umkm_id', $umkmId)->where('type', 'OUT')->whereMonth('created_at', now()->month)->sum('amount');

        $reply = "📊 **Info Keuangan:**\n🟢 Hari Ini: Rp " . number_format($today, 0, ',', '.') . "\n🔵 Bulan Ini: Rp " . number_format($month, 0, ',', '.');
        return response()->json(['success' => true, 'ai_response' => $reply]);
    }

    private function getBusinessSummary($user)
    {
        $umkmId = $this->getUmkmId($user);
        if (!$umkmId) return "";

        $topProducts = Transaction::where('umkm_id', $umkmId)
            ->where('type', 'OUT')
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(3)
            ->with('product:id,name')
            ->get()
            ->map(fn($t) => $t->product->name . "({$t->total_qty})")
            ->implode(', ');

        return "Data Toko: Terlaris [$topProducts].";
    }

    public function resetChat(Request $request)
    {
        $newSessionId = (string) Str::uuid();
        session(['current_chat_session_id' => $newSessionId]);
        return redirect()->route('dashboard', ['s' => $newSessionId]);
    }
}