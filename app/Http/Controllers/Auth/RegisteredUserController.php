<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Tampilkan halaman registrasi.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Proses permintaan pendaftaran akun baru.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'name'       => ['required', 'string', 'max:255'],
                'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'npm'        => ['required', 'string', 'max:9', 'unique:' . User::class, 'regex:/^[A-Za-z0-9]+$/'],
                'jurusan_id' => ['required', 'exists:jurusans,id'],
                'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            ],
            [
                'name.required'        => 'Nama wajib diisi.',
                'name.string'          => 'Nama harus berupa teks.',
                'name.max'             => 'Nama maksimal 255 karakter.',

                'email.required'       => 'Email wajib diisi.',
                'email.string'         => 'Email tidak valid.',
                'email.lowercase'      => 'Email harus huruf kecil.',
                'email.email'          => 'Format email tidak valid.',
                'email.max'            => 'Email maksimal 255 karakter.',
                'email.unique'         => 'Email ini sudah terdaftar.',

                'npm.required'         => 'NPM wajib diisi.',
                'npm.string'           => 'NPM harus berupa teks/angka.',
                'npm.max'              => 'NPM maksimal 9 karakter.',
                'npm.unique'           => 'NPM ini sudah terdaftar.',
                'npm.regex'            => 'NPM hanya boleh huruf dan angka (tanpa spasi/simbol).',

                'jurusan_id.required'  => 'Program studi wajib dipilih.',
                'jurusan_id.exists'    => 'Program studi tidak ditemukan.',

                'password.required'    => 'Password wajib diisi.',
                'password.confirmed'   => 'Konfirmasi password tidak cocok.',
            ]
        );

        // Normalisasi data
        $email = strtolower($validated['email']);
        $npm   = strtoupper($validated['npm']);

        // Buat user
        $user = User::create([
            'name'       => $validated['name'],
            'email'      => $email,
            'npm'        => $npm,
            'jurusan_id' => $validated['jurusan_id'],
            'password'   => Hash::make($validated['password']),
        ]);

        // Trigger event (untuk email verification, listener, dll.)
        event(new Registered($user));

        // Login otomatis
        Auth::login($user);

        // Tentukan redirect yang aman:
        $target = Route::has('dashboard')
            ? route('dashboard')
            : (Route::has('home') ? route('home') : '/');

        return redirect()->intended($target);
    }
}
