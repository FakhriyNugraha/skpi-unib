<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminJurusanMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user sudah otentikasi
        if (!auth()->check()) {
            abort(403, 'Anda harus login untuk mengakses fitur ini.');
        }

        $user = Auth::user();

        // Cek apakah user adalah admin atau superadmin
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            abort(403, 'Anda tidak memiliki akses untuk fitur ini.');
        }

        // Untuk admin biasa, pastikan memiliki jurusan
        if ($user->role === 'admin' && !$user->jurusan_id) {
            abort(403, 'Anda tidak diizinkan mengakses fitur ini karena belum ditetapkan jurusan.');
        }

        return $next($request);
    }
}