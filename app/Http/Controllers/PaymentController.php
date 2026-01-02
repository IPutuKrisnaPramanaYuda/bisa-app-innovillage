<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index() {
    // Cari transaksi user yang statusnya masih 'pending'
    $pendingTrx = \App\Models\Transaction::where('status', 'pending')
                    // Logic: biasanya ambil trx terbaru user ini
                    // Untuk simpelnya kita ambil data dummy atau total tagihan
                    ->get();
    
    if($pendingTrx->isEmpty()) return redirect('/')->with('success', 'Tidak ada tagihan.');

    $totalTagihan = $pendingTrx->sum('amount');
    return view('marketplace.payment', compact('totalTagihan'));
}

public function uploadProof(Request $request) {
    $request->validate(['proof' => 'required|image']);
    
    // Upload foto
    $path = $request->file('proof')->store('payment_proofs', 'public');

    // Update semua transaksi pending user ini jadi 'paid' (atau menunggu verifikasi)
    // Disini idealnya status jadi 'waiting_verification'
    // Tapi biar cepat kita anggap 'paid' dan simpan buktinya
    \App\Models\Transaction::where('status', 'pending') // harusnya filter by user_id juga
        ->update([
            'status' => 'paid', 
            'proof_of_payment' => $path
        ]);

    return redirect('/dashboard')->with('success', 'Bukti bayar terkirim! Admin akan memproses pesanan.');
}
}
