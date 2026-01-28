<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use App\Models\Umkm;
use App\Models\ChatSession;
use App\Models\Transaction; // <--- PASTIKAN INI ADA
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{
    // ... method login & authenticate biarkan saja ...

    public function login()
    {
        if (session()->has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            session(['admin_id' => $admin->id]); 
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Username atau Password salah!');
    }

    // 👇 BAGIAN INI YANG KITA PERBAIKI 👇
    public function dashboard()
{
    // 1. STATISTIK DASAR
    $totalUsers = User::count();
    $totalUmkm = Umkm::count();
    $totalPrompts = ChatSession::count();
    $totalOmzet = Transaction::where('type', 'OUT')->sum('amount');
    
    // Ambil data admin yang sedang login
    $admin = Admin::find(session('admin_id'));

    // 2. USER RAJIN (TOP AI USERS)
    // Menghitung siapa yang paling sering chat sama AI
    $topAiUsers = ChatSession::select('user_id', DB::raw('count(*) as total'))
        ->groupBy('user_id')
        ->orderByDesc('total')
        ->take(5)
        ->with('user')
        ->get();

    // 3. LIVE MONITORING (ACTIVITY LOGS)
    // Mengecek apakah tabel activity_logs sudah ada atau belum
    $recentActivities = [];
    $chartData = [];

    try {
        // Ambil 10 aktivitas terakhir
        $recentActivities = DB::table('activity_logs')
            ->join('users', 'activity_logs.user_id', '=', 'users.id')
            ->select('users.name', 'activity_logs.url', 'activity_logs.created_at', 'activity_logs.ip')
            ->orderBy('activity_logs.created_at', 'desc')
            ->limit(10)
            ->get();

        // Data Grafik 7 Hari Terakhir
        $chartData = DB::table('activity_logs')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->get();
            
    } catch (\Exception $e) {
        // Kalau tabel belum dibuat, biarkan kosong dulu biar gak error
    }

    return view('admin.dashboard', compact(
        'totalUsers', 
        'totalUmkm', 
        'totalPrompts', 
        'admin', 
        'totalOmzet',
        'topAiUsers',      // <--- Variable ini yang dicari
        'recentActivities',// <--- Variable ini yang dicari
        'chartData'        // <--- Variable ini yang dicari
    ));
}

    public function updateSettings(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:admins,username,' . session('admin_id'),
            'password' => 'nullable|min:6'
        ]);

        $admin = Admin::find(session('admin_id'));
        $admin->username = $request->username;
        
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        
        $admin->save();

        return back()->with('success', 'Akun Admin berhasil diperbarui!');
    }

    public function logout()
    {
        session()->forget('admin_id');
        return redirect()->route('admin.login');
    }
}