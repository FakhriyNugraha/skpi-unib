<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\SkpiData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserManagementController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Pastikan hanya admin yang bisa mengakses fitur ini (middleware admin.jurusan sudah handle)
        if (!$user || $user->role !== 'admin' || !$user->jurusan_id) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
        }

        $query = User::with('jurusan')->where('role', 'user'); // hanya menampilkan user (mahasiswa)

        // Filter berdasarkan jurusan admin
        if ($user->jurusan_id) {
            $query->where('jurusan_id', $user->jurusan_id);
        }

        // Filter berdasarkan npm
        if ($request->filled('npm')) {
            $query->where('npm', 'like', '%' . $request->npm . '%');
        }

        // Filter berdasarkan periode wisuda (menggunakan data dari skpi_data)
        if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
            $skpiUsers = SkpiData::where('periode_wisuda', $request->periode_wisuda)
                ->pluck('user_id')
                ->toArray();

            $query->whereIn('id', $skpiUsers);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pencarian umum
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('npm', 'like', "%{$s}%");
            });
        }

        $users = $query->paginate(15)->appends($request->query());

        // Dapatkan periode wisuda yang tersedia untuk dropdown
        $periodQuery = SkpiData::select('periode_wisuda')
            ->whereNotNull('periode_wisuda');

        // Filter berdasarkan jurusan hanya jika user adalah admin biasa
        if ($user->role === 'admin' && $user->jurusan_id) {
            $periodQuery->where('jurusan_id', $user->jurusan_id);
        }

        $periodNumbers = $periodQuery
            ->distinct()
            ->orderBy('periode_wisuda', 'desc')
            ->pluck('periode_wisuda')
            ->values();

        // Format periods with their date ranges
        $availablePeriods = collect();
        foreach ($periodNumbers as $periodNumber) {
            $periodInfo = \App\Helpers\PeriodHelper::getPeriodRange($periodNumber);
            $availablePeriods->push([
                'number' => $periodNumber,
                'title' => $periodInfo['title']
            ]);
        }

        // Hitung jumlah mahasiswa
        $totalMahasiswa = $query->clone()->count();

        return view('admin.users-index', compact('users', 'availablePeriods', 'totalMahasiswa'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $user = Auth::user();

        // Pastikan hanya admin yang bisa membuat user (middleware admin.jurusan sudah handle)
        if (!$user || $user->role !== 'admin' || !$user->jurusan_id) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
        }

        // Hanya tampilkan jurusan milik admin
        $jurusans = collect([$user->jurusan]);

        return view('admin.users-create', compact('jurusans'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin' || !$user->jurusan_id) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
        }

        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'phone'      => ['nullable', 'string', 'max:15'],
            'address'    => ['nullable', 'string'],
            'npm'        => ['required', 'string', 'max:9', 'regex:/^[A-Za-z0-9]+$/', 'unique:users,npm'],
        ]);

        // Validasi tambahan agar hanya bisa membuat user di jurusan sendiri
        if ($request->jurusan_id != $user->jurusan_id) {
            abort(403, 'Anda tidak dapat membuat user untuk jurusan lain.');
        }

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'user', // selalu user untuk mahasiswa
            'status'     => 'active',
            'jurusan_id' => $request->jurusan_id,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'npm'        => $request->npm,
        ]);

        return redirect()->route('admin.users-jurusan.index')->with('success', 'Mahasiswa berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $admin = Auth::user();

        if (!$admin || $admin->role !== 'admin' || !$admin->jurusan_id) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
        }

        // Pastikan user hanya bisa mengedit user dari jurusan yang sama
        if ($user->jurusan_id != $admin->jurusan_id) {
            abort(403, 'Anda tidak dapat mengedit user dari jurusan lain.');
        }

        // Hanya tampilkan jurusan milik admin
        $jurusans = collect([$admin->jurusan]);

        return view('admin.users-edit', compact('user', 'jurusans'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $admin = Auth::user();

        if (!$admin || $admin->role !== 'admin' || !$admin->jurusan_id) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
        }

        // Cek apakah user hanya bisa mengupdate user dari jurusan yang sama
        if ($user->jurusan_id != $admin->jurusan_id) {
            abort(403, 'Anda tidak dapat mengupdate user dari jurusan lain.');
        }

        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'phone'      => ['nullable', 'string', 'max:15'],
            'address'    => ['nullable', 'string'],
            'status'     => ['required', Rule::in(['active', 'inactive'])],
            'password'   => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                function ($attribute, $value, $fail) use ($user) {
                    if (Hash::check($value, $user->password)) {
                        $fail('Password baru tidak boleh sama dengan password sebelumnya.');
                    }
                },
            ],
            'npm'        => ['required', 'string', 'max:9', 'regex:/^[A-Za-z0-9]+$/', Rule::unique('users', 'npm')->ignore($user->id)],
        ]);

        // Validasi tambahan agar hanya bisa update user di jurusan sendiri
        if ($request->jurusan_id != $admin->jurusan_id) {
            abort(403, 'Anda tidak dapat mengupdate user untuk jurusan lain.');
        }

        $data = $request->except(['password', 'password_confirmation']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users-jurusan.index')->with('success', 'Mahasiswa berhasil diperbarui.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $admin = Auth::user();

        if (!$admin || $admin->role !== 'admin' || !$admin->jurusan_id) {
            abort(403, 'Anda tidak memiliki akses untuk halaman ini.');
        }

        // Tidak bisa menghapus diri sendiri
        if ($user->id == $admin->id) {
            return redirect()->route('admin.users-jurusan.index')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Cek apakah user hanya bisa menghapus user dari jurusan yang sama
        if ($user->jurusan_id != $admin->jurusan_id) {
            abort(403, 'Anda tidak dapat menghapus user dari jurusan lain.');
        }

        $user->delete();

        return redirect()->route('admin.users-jurusan.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }
}