<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        // Simpan "next" ke sesi jika ada, agar setelah update bisa kembali ke tujuan
        if ($request->filled('next')) {
            session()->put('redirect_to', $request->get('next'));
        }

        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Jika sedang "dipaksa" isi phone (datang dari middleware atau query)
        $forcePhone = session('force_phone_fill') || $request->boolean('force_phone');

        // Validasi dasar profil
        $rules = [
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'  => [$forcePhone ? 'required' : 'nullable', 'string', 'max:14', 'regex:/^[0-9+\s\-\(\)]+$/'],
            'address'=> ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];

        // Role-based fields:
        if ($user->role === 'user') {
            // Ganti nim -> npm (unik di tabel users; sesuaikan kolom DB-mu)
            $rules['npm'] = [
                'required',
                'string',
                'max:9', // Changed to 9 characters as per requirement
                'regex:/^[A-Za-z0-9]+$/',
                Rule::unique('users', 'npm')->ignore($user->id),
            ];
        } elseif (in_array($user->role, ['admin', 'superadmin'])) {
            $rules['nip'] = [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9]+$/',
                Rule::unique('users', 'nip')->ignore($user->id),
            ];
        }

        $data = $request->validate($rules);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        // Jika user adalah mahasiswa dan mengupdate NPM, pastikan 3 karakter awal tidak berubah
        if ($user->role === 'user' && isset($data['npm'])) {
            $originalNpm = $user->npm;
            $newNpm = $data['npm'];

            // Jika NPM asli memiliki setidaknya 3 karakter, pastikan 3 karakter awal tidak berubah
            if (strlen($originalNpm) >= 3 && strlen($newNpm) >= 3) {
                $firstThreeOriginal = substr($originalNpm, 0, 3);
                $firstThreeNew = substr($newNpm, 0, 3);

                // Jika 3 karakter awal berbeda, ganti dengan 3 karakter awal asli
                if ($firstThreeOriginal !== $firstThreeNew) {
                    $data['npm'] = $firstThreeOriginal . substr($newNpm, 3);
                }
            }
        }

        $user->update($data);

        // Jika ini datang dari "paksa isi phone", balikkan ke tujuan
        if ($forcePhone && !empty($user->phone)) {
            $redirectTo = session('redirect_to', route('skpi.index'));
            session()->forget(['force_phone_fill','redirect_to']);

            return redirect($redirectTo)->with('success', 'Nomor telepon berhasil disimpan. Silakan lanjut.');
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        
        // Double-check: ensure user is deleting their own account
        if ($user->id !== Auth::id()) {
            abort(403, 'Unauthorized to delete this account.');
        }

        Auth::logout();

        // Hapus avatar bila ada (opsional)
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Akun berhasil dihapus.');
    }
}
