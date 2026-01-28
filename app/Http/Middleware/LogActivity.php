<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Pastikan import DB

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        // Catat log TANPA SYARAT login
        // Jika user login, simpan ID-nya. Jika tamu, isi NULL.
        
        try {
            DB::table('activity_logs')->insert([
                'user_id' => Auth::check() ? Auth::id() : null, // Null jika tamu
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Biarkan lewat jika error database, jangan ganggu user
        }

        return $next($request);
    }
}