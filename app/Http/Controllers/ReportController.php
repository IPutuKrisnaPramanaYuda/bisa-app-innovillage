<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $umkm = auth()->user()->umkm;
        if (!$umkm) return redirect()->route('dashboard');

        // --- 1. PERHITUNGAN KEUANGAN (REAL TIME) ---
        
        // Filter Transaksi "OUT" (Penjualan)
        $transaksi = Transaction::where('umkm_id', $umkm->id)
                        ->where('type', 'OUT')
                        ->get();

        // Total Penjualan (Omset)
        $totalPenjualan = $transaksi->sum('amount');
        
        // Total Barang Terjual
        $totalItemTerjual = $transaksi->sum('quantity');

        // Simulasi Modal (Anggap Modal 70% dari penjualan)
        $totalModal = $totalPenjualan * 0.7; 
        
        // Laba Kotor (Sama dengan Omset dalam konteks sederhana)
        $labaKotor = $totalPenjualan;
        
        // Laba Bersih (Omset - Modal)
        $labaBersih = $totalPenjualan - $totalModal;

        // --- 2. STATISTIK PELANGGAN ---
        $totalPelanggan = Transaction::where('umkm_id', $umkm->id)
                            ->whereNotNull('buyer_id')
                            ->distinct('buyer_id')
                            ->count();
        
        // Pelanggan Baru (7 Hari Terakhir)
        $pelangganBaru = Transaction::where('umkm_id', $umkm->id)
                            ->whereNotNull('buyer_id')
                            ->where('created_at', '>=', Carbon::now()->subDays(7))
                            ->distinct('buyer_id')
                            ->count();

        // --- 3. LOGIKA REKOMENDASI AI (OTOMATIS) ---
        $aiRecommendations = [];

        // Logika 1: Cek Stok
        $stokSedikit = \App\Models\Product::where('umkm_id', $umkm->id)->where('stock', '<', 5)->count();
        if ($stokSedikit > 0) {
            $aiRecommendations[] = "⚠️ Perhatian: Ada $stokSedikit produk dengan stok menipis. Lakukan pengecekan inventori agar tidak terjadi kekurangan stok di minggu berikutnya.";
        }

        // Logika 2: Cek Penjualan
        if ($totalItemTerjual < 10) {
            $aiRecommendations[] = "📢 Penjualan masih rendah. Tingkatkan promosi di media sosial dan tawarkan diskon bundling agar bisa cepat balik modal.";
        } else {
            $aiRecommendations[] = "📈 Tren penjualan positif! Pertahankan kualitas layanan dan coba tawarkan produk baru ke pelanggan setia.";
        }

        // Logika 3: Cek Margin
        if ($labaBersih > 500000) {
            $aiRecommendations[] = "💰 Arus kas sehat. Pertimbangkan untuk menyisihkan 20% keuntungan sebagai modal ekspansi produk baru.";
        }

        return view('reports.index', compact(
            'totalPenjualan', 'totalItemTerjual', 'totalModal', 
            'labaKotor', 'labaBersih', 'totalPelanggan', 'pelangganBaru',
            'aiRecommendations'
        ));
    }
}