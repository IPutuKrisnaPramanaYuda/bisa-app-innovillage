<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        // 1. Siapkan Query (Ambil data produk beserta info tokonya)
        $query = Product::with('umkm')->where('is_available', true);

        // 2. Jika ada pencarian (User ketik di search bar)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('umkm', function($subQ) use ($search) {
                      $subQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // 3. Ambil datanya (Urutkan dari yang terbaru)
        $products = $query->latest()->paginate(12); // Tampilkan 12 per halaman

        return view('marketplace.index', compact('products'));
    }
}