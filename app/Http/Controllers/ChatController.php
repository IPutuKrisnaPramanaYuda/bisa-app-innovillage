<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ChatSession;
use App\Services\AiService; // Import Service baru

class ChatController extends Controller

{
    protected $aiService;

    // Inject Service
    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index(Request $request)
{
    $user = auth()->user();
    
    // Ambil Session ID dari URL (s) atau session, atau buat baru
    $currentSessionId = $request->get('s', session('current_chat_session_id', (string) Str::uuid()));
    session(['current_chat_session_id' => $currentSessionId]);
    
    // RIWAYAT: Ambil chat terakhir dari tiap session_id agar muncul di sidebar
    $history = collect();
    if ($user) {
        $history = ChatSession::where('user_id', $user->id)
            ->select('session_id', 'message', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('session_id'); // Biar list riwayat tidak duplikat
    }

    $currentChats = ChatSession::where('session_id', $currentSessionId)->oldest()->get();

    return view('dashboard_ai', compact('currentChats', 'history', 'currentSessionId'));
}

public function sendMessage(Request $request)
{
    $request->validate(['message' => 'required|string']);
    
    // 1. Ambil user (bisa null jika guest)
    $user = auth()->user();
    
    // 2. Gunakan Session ID untuk history (Guest tetap punya session sementara)
    $sessionId = session('current_chat_session_id', (string) \Illuminate\Support\Str::uuid());
    session(['current_chat_session_id' => $sessionId]);

    // 3. Ambil history hanya jika user sedang LOGIN
    // Jika guest, kirim history kosong [] agar tidak error
    $history = collect(); 
    if ($user) {
        $history = ChatSession::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->oldest()
            ->get();
    }
    
    // 4. Ambil mode dari form
    $mode = $request->input('mode', 'regular'); 

    // --- PANGGIL AI SERVICE ---
    try {
        // AI Service sekarang aman karena bisa menangani guest di dalamnya
        $aiReply = $this->aiService->processMessage($request->message, $history, $mode);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('AI Service Error: ' . $e->getMessage());
        return response()->json(['success' => false, 'error' => 'Koneksi AI Terputus']);
    }

    // 5. SIMPAN KE DATABASE HANYA JIKA LOGIN
    // 🔥 PENTING: Jangan simpan ke database jika Guest agar tidak Error 500 🔥
    if ($user) {
        $sessionId = session('current_chat_session_id', (string) \Illuminate\Support\Str::uuid());
        \App\Models\ChatSession::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'message' => $request->message,
            'response' => $aiReply,
        ]);
    }

    return response()->json([
        'success' => true,
        'user_message' => $request->message,
        'ai_response' => $aiReply
    ]);
}

    public function resetChat(Request $request)
    {
        // Generate session ID baru
        $newSessionId = (string) Str::uuid();
        session(['current_chat_session_id' => $newSessionId]);
        
        return redirect()->route('dashboard', ['s' => $newSessionId]);
    }
}