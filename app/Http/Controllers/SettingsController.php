<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Smalot\PdfParser\Parser; // <--- JANGAN LUPA INI

class SettingsController extends Controller
{
    // ... method index() & toggleContributor() biarkan saja ...
    public function index() { return view('settings.index'); }

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

    // UPDATE BAGIAN INI:
public function storeDataset(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        $file = $request->file('document');
        $extension = $file->getClientOriginalExtension();
        
        // 1. Simpan File Fisik
        $path = $file->store('datasets', 'public');

        // 2. EKSTRAK TEKS
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

        // ❌ JANGAN ADA dd($textContent) DISINI LAGI YAA ❌

        // 3. Simpan ke Database
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