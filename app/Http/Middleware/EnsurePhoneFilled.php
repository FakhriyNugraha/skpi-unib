<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePhoneFilled
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && empty($user->phone)) {
            // Simpan flag dan tujuan agar setelah isi phone bisa kembali
            session()->put('force_phone_fill', true);
            session()->put('redirect_to', url()->full());

            return redirect()
                ->route('profile.edit', ['force_phone' => 1])
                ->with('error', 'Lengkapi nomor telepon terlebih dahulu untuk melanjutkan.');
        }

        return $next($request);
    }
}
