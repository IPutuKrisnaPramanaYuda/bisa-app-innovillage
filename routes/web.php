<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\UmkmController;

// =============================================================
// 1. AKSES PUBLIK (BISA DIAKSES GUEST / BELUM LOGIN)
// =============================================================
// Goals: Orang buka web langsung ketemu AI untuk tanya jawab umum.
Route::get('/', [ChatController::class, 'index'])->name('landing');
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send.public');



// =============================================================
// 2. AKSES TERPROTEKSI (WAJIB LOGIN)
// =============================================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // --- DASHBOARD AI (USER LOGGED IN) ---
    Route::get('/dashboard', [ChatController::class, 'index'])->name('dashboard');
    Route::post('/chat', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/new', [ChatController::class, 'resetChat'])->name('chat.reset'); 

    // --- MANAJEMEN TOKO (UMKM) ---
    Route::prefix('toko')->name('umkm.')->group(function () {
        Route::get('/', [UmkmController::class, 'index'])->name('dashboard');
        Route::get('/buat', [UmkmController::class, 'create'])->name('create');
        Route::post('/buat', [UmkmController::class, 'store'])->name('store');
        
        // Produk
        Route::get('/produk', [UmkmController::class, 'products'])->name('products');
        Route::get('/produk/tambah', [UmkmController::class, 'createProduct'])->name('products.create');
        Route::post('/produk/simpan', [UmkmController::class, 'storeProduct'])->name('products.store');
        Route::get('/produk/{id}/edit', [UmkmController::class, 'editProduct'])->name('products.edit');
        Route::put('/produk/{id}', [UmkmController::class, 'updateProduct'])->name('products.update');
        Route::delete('/produk/{id}', [UmkmController::class, 'destroyProduct'])->name('products.destroy');

        // Penjualan
        Route::get('/penjualan', [UmkmController::class, 'sales'])->name('sales');
        Route::post('/penjualan/simpan', [UmkmController::class, 'storeTransaction'])->name('sales.store');

        // Inventori / Stok
        Route::get('/stok', [UmkmController::class, 'inventory'])->name('inventory');
        Route::post('/stok/simpan', [UmkmController::class, 'storeInventory'])->name('inventory.store');
        Route::get('/stok/{id}/edit', [UmkmController::class, 'editInventory'])->name('inventory.edit');
        Route::put('/stok/{id}', [UmkmController::class, 'updateInventory'])->name('inventory.update');
        Route::delete('/stok/{id}', [UmkmController::class, 'destroyInventory'])->name('inventory.destroy');

        // Laporan
        Route::get('/laporan', [UmkmController::class, 'reports'])->name('reports');
        Route::post('/laporan/saldo', [UmkmController::class, 'updateBalance'])->name('reports.balance');
    });

    // --- SETTINGS & KONTRIBUTOR ---
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/contributor', [SettingsController::class, 'toggleContributor'])->name('settings.toggle-contributor');
        Route::get('/contributor/upload', [SettingsController::class, 'upload'])->name('contributor.upload');
        Route::post('/contributor/store', [SettingsController::class, 'storeDataset'])->name('contributor.store');
    });

    // --- PROFILE ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- GOOGLE LOGIN ---
Route::get('/auth/google', [SocialiteController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback'])->name('google.callback');

// --- DEBUGGING (Hapus jika sudah produksi) ---
Route::get('/cek-data', function () {
    return \App\Models\Dataset::all(['id', 'title', 'extracted_text']);
})->middleware('auth');

require __DIR__.'/auth.php';