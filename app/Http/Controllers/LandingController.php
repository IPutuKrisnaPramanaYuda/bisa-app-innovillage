<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    // Tampilkan Halaman Landing
    public function index()
    {
        // Ambil Produk (Limit 4-8 biar gak berat)
        $products = Product::with('umkm')->latest()->take(8)->get();
        
        // Ambil Komentar Terbaru
        $testimonials = Testimonial::latest()->get();

        return view('landing', compact('products', 'testimonials'));
    }

    // Simpan Komentar
    public function storeComment(Request $request)
    {
        // 1. Cek apakah user Login atau Guest
        if (Auth::check()) {
            // JIKA LOGIN: Validasi pesan saja
            $request->validate(['message' => 'required|string|max:500']);
            
            $data = [
                'user_id' => Auth::id(),
                'name'    => Auth::user()->name,
                'email'   => Auth::user()->email,
                'role'    => 'Warga Lokal', // Atau ambil dari role user
                'message' => $request->message,
            ];
        } else {
            // JIKA GUEST: Validasi nama & email juga
            $request->validate([
                'name'    => 'required|string|max:50',
                'email'   => 'required|email|max:100',
                'message' => 'required|string|max:500',
            ]);

            $data = [
                'user_id' => null,
                'name'    => $request->name,
                'email'   => $request->email,
                'role'    => 'Pengunjung',
                'message' => $request->message,
            ];
        }

        Testimonial::create($data);

        return redirect()->back()->with('success', 'Terima kasih atas masukan Anda!'); // Ganti 'success' biar muncul alert
    }

    public function shop()
{
    $products = Product::with('umkm')->latest()->get();
    return view('shop', compact('products'));
}
}