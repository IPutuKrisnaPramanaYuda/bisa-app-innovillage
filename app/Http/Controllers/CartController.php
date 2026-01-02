<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart; // Jangan lupa buat Model Cart nanti
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // Lihat Keranjang
    public function index()
    {
        $carts = Cart::where('user_id', auth()->id())->with('product')->get();
        return view('marketplace.cart', compact('carts'));
    }

    // Tambah ke Keranjang
    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        // Cek stok dulu
        if($product->stock < 1) return back()->with('error', 'Stok habis!');

        // Cek apakah produk sudah ada di keranjang user?
        $cart = Cart::where('user_id', auth()->id())
                    ->where('product_id', $id)
                    ->first();

        if($cart) {
            $cart->increment('quantity');
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $id,
                'quantity' => 1
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk masuk keranjang!');
    }

    // Hapus dari Keranjang
    public function remove($id)
    {
        Cart::where('id', $id)->delete();
        return back();
    }

    // CHECKOUT (Proses Pesanan)
    public function checkout(Request $request)
    {
        $carts = Cart::where('user_id', auth()->id())->get();

        if($carts->isEmpty()) return back()->with('error', 'Keranjang kosong!');

        // Gunakan Database Transaction biar aman (kalau gagal satu, gagal semua)
        DB::transaction(function () use ($carts) {
            foreach ($carts as $cart) {
                // Kurangi Stok Real
                $cart->product->decrement('stock', $cart->quantity);

                // Buat Transaksi (Status Pending)
                Transaction::create([
                    'buyer_id' => auth()->id(), // <--- TAMBAHAN PENTING
                    'umkm_id' => $cart->product->umkm_id,
                    'type' => 'OUT',
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,
                    'amount' => $cart->product->price * $cart->quantity,
                    'date' => now(),
                    'description' => 'Online Order',
                    'status' => 'pending'
                ]);
            }
            
            // Kosongkan Keranjang setelah checkout
            Cart::where('user_id', auth()->id())->delete();
        });

        // Arahkan ke Halaman Pembayaran
        return redirect()->route('payment.page')->with('success', 'Pesanan dibuat! Silakan lakukan pembayaran.');
    }
}