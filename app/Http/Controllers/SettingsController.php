<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;

class SettingsController extends Controller
{
    // 1. TAMPILKAN HALAMAN SETTINGS
    public function index() 
    { 
        // Ambil data UMKM milik user yang login
        $umkm = Auth::user()->umkm;

        // Kirim variable $umkm ke view 'settings'
        return view('settings.index', compact('umkm')); 
    }

    // 2. UPDATE PROFIL TOKO (Baru Ditambahkan)
    public function updateShop(Request $request)
    {
        $umkm = Auth::user()->umkm;

        if (!$umkm) {
            return back()->with('error', 'Anda belum memiliki toko! Silakan buat toko terlebih dahulu.');
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone'       => 'nullable|string|max:20',
            'address'     => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        // Logic Update Foto
        if ($request->hasFile('image')) {
            // Hapus foto lama jika ada
            if ($umkm->image) {
                Storage::disk('public')->delete($umkm->image);
            }
            // Simpan foto baru
            $path = $request->file('image')->store('umkm_profiles', 'public');
            $umkm->image = $path;
        }

        // Update Data
        $umkm->name = $request->name;
        // Opsional: Update slug jika nama berubah
        if($umkm->isDirty('name')){
             $umkm->slug = Str::slug($request->name);
        }
        $umkm->description = $request->description;
        $umkm->phone = $request->phone;
        $umkm->address = $request->address;
        
        $umkm->save();

        return back()->with('success', 'Informasi Toko berhasil diperbarui!');
    }

    // --- FITUR KONTRIBUTOR (YANG LAMA TETAP ADA) ---

    public function toggleContributor(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $status = $request->has('is_contributor');
        $user->update(['is_contributor' => $status]);
        
        $message = $status 
            ? 'Mode Kontributor Diaktifkan! Terima kasih telah berkontribusi.' 
            : 'Mode Kontributor Dinonaktifkan.';
        return redirect()->back()->with('success', $message);
    }

    public function upload()
    {
        if (!auth()->user()->is_contributor) {
            return redirect()->route('settings.index')->with('error', 'Anda bukan kontributor!');
        }
        return view('settings.upload');
    }

    public function storeDataset(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        $file = $request->file('document');
        $extension = $file->getClientOriginalExtension();
        
        // Simpan File Fisik
        $path = $file->store('datasets', 'public');

        // Ekstrak Teks
        $textContent = "";
        try {
            if ($extension === 'pdf') {
                $parser = new Parser();
                $pdf = $parser->parseFile($file->getRealPath());
                $textContent = $pdf->getText();
            } 
            elseif ($extension === 'txt') {
                $textContent = file_get_contents($file->getRealPath());
            }
        } catch (\Exception $e) {
            $textContent = "Gagal membaca teks otomatis.";
        }

        // Simpan ke Database
        \App\Models\Dataset::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_type' => $extension,
            'status' => 'pending',
            'extracted_text' => utf8_encode($textContent), 
        ]);

        return redirect()->route('settings.index')->with('success', 'Jurnal berhasil diupload & dibaca oleh AI!');
    }
}