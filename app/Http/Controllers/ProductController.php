<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 1. Tampilkan Daftar Produk
    public function index()
    {
        // Ambil produk HANYA milik toko user yang sedang login
        $products = auth()->user()->umkm->products()->latest()->get();
        return view('products.index', compact('products'));
    }

    // 2. Tampilkan Formulir Tambah Produk
    public function create()
    {
        return view('products.create');
    }

    // 3. Simpan Produk ke Database
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        // Proses Upload Gambar (Jika ada)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Simpan Data
        // Simpan Data
auth()->user()->umkm->products()->create([
    'name' => $request->name,
    'description' => $request->description,
    'price' => $request->price,
    'stock' => $request->stock,
    'image' => $imagePath,
    'is_available' => true,
    // TIDAK ADA 'slug' => ... DISINI. BAGUS! ✅
]); 

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
    }
}