<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Jurusan;
use App\Models\SkpiData;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users'     => User::count(),
            'total_mahasiswa' => User::where('role', 'user')->count(),
            'total_admin'     => User::whereIn('role', ['admin', 'superadmin'])->count(),
            'total_skpi'      => SkpiData::count(),
            'pending_skpi'    => SkpiData::where('status', 'submitted')->count(),
            'approved_skpi'   => SkpiData::where('status', 'approved')->count(),
            'rejected_skpi'   => SkpiData::where('status', 'rejected')->count(),
        ];

        $recentActivity = SkpiData::with(['user', 'jurusan', 'reviewer'])
            ->latest('updated_at')
            ->limit(10)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'recentActivity'));
    }

    /* Users */
    public function users(Request $request)
    {
        $query = User::with('jurusan')->latest();

        if ($request->filled('role'))    $query->where('role', $request->role);
        if ($request->filled('jurusan')) $query->where('jurusan_id', $request->jurusan);
        if ($request->filled('status'))  $query->where('status', $request->status);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('npm', 'like', "%{$s}%")
                  ->orWhere('nip', 'like', "%{$s}%");
            });
        }

        $users = $query->paginate(15)->appends($request->query());
        return view('superadmin.users', compact('users'));
    }

    public function createUser()
    {
        $jurusans = Jurusan::active()->orderBy('nama_jurusan')->get();
        return view('superadmin.create-user', compact('jurusans'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'       => ['required','string','max:255'],
            'email'      => ['required','string','email','max:255','unique:users,email'],
            'password'   => ['required','string','min:8','confirmed'],
            'role'       => ['required', Rule::in(['user','admin','superadmin'])],
            'status'     => ['required', Rule::in(['active','inactive'])],
            'jurusan_id' => ['nullable','exists:jurusans,id'],
            'phone'      => ['nullable','string','max:15'],
            'address'    => ['nullable','string'],
            'npm'        => [Rule::requiredIf(fn() => $request->role === 'user'),
                             'nullable','string','max:9','regex:/^[A-Za-z0-9]+$/','unique:users,npm'],
            'nip'        => [Rule::requiredIf(fn() => in_array($request->role, ['admin','superadmin'])),
                             'nullable','string','max:20','unique:users,nip'],
        ]);

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'status'     => $request->status,
            'jurusan_id' => $request->jurusan_id,
            'phone'      => $request->phone,
            'address'    => $request->address,
            'npm'        => $request->npm,
            'nip'        => $request->nip,
        ]);

        return redirect()->route('superadmin.users')->with('success', 'User berhasil dibuat.');
    }

    public function editUser(User $user)
    {
        $jurusans = Jurusan::active()->orderBy('nama_jurusan')->get();
        return view('superadmin.edit-user', compact('user','jurusans'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'       => ['required','string','max:255'],
            'email'      => ['required','string','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'role'       => ['required', Rule::in(['user','admin','superadmin'])],
            'status'     => ['required', Rule::in(['active','inactive'])],
            'jurusan_id' => ['nullable','exists:jurusans,id'],
            'phone'      => ['nullable','string','max:15'],
            'address'    => ['nullable','string'],
            'password'   => ['nullable','string','min:8','confirmed'],
            'npm'        => [Rule::requiredIf(fn() => $request->role === 'user'),
                             'nullable','string','max:9','regex:/^[A-Za-z0-9]+$/',
                             Rule::unique('users','npm')->ignore($user->id)],
            'nip'        => [Rule::requiredIf(fn() => in_array($request->role, ['admin','superadmin'])),
                             'nullable','string','max:20',
                             Rule::unique('users','nip')->ignore($user->id)],
        ]);

        $data = $request->except(['password','password_confirmation']);
        if ($request->filled('password')) $data['password'] = Hash::make($request->password);

        $user->update($data);
        return redirect()->route('superadmin.users')->with('success', 'User berhasil diupdate.');
    }

    public function deleteUser(User $user)
    {
        if ((int)$user->id === (int)Auth::id()) {
            return redirect()->route('superadmin.users')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('superadmin.users')->with('success', 'User berhasil dihapus.');
    }

    /* Jurusans */
    public function jurusans()
    {
        $jurusans = Jurusan::with(['users','skpiData'])->orderBy('nama_jurusan')->get();
        return view('superadmin.jurusans', compact('jurusans'));
    }

    public function createJurusan()
    {
        return view('superadmin.create-jurusan');
    }

    public function storeJurusan(Request $request)
    {
        $request->validate([
            'nama_jurusan' => ['required','string','max:255'],
            'kode_jurusan' => ['required','string','max:10','unique:jurusans,kode_jurusan'],
            'deskripsi'    => ['nullable','string'],
            'kaprodi'      => ['nullable','string','max:255'],
            'nip_kaprodi'  => ['nullable','string','max:20'],
            'status'       => ['nullable', Rule::in(['active','inactive'])],
        ]);

        Jurusan::create([
            'nama_jurusan' => $request->nama_jurusan,
            'kode_jurusan' => $request->kode_jurusan,
            'deskripsi'    => $request->deskripsi,
            'kaprodi'      => $request->kaprodi,
            'nip_kaprodi'  => $request->nip_kaprodi,
            'status'       => $request->status ?? 'active',
        ]);

        return redirect()->route('superadmin.jurusans')->with('success', 'Program studi berhasil dibuat.');
    }

    public function editJurusan(Jurusan $jurusan)
    {
        return view('superadmin.edit-jurusan', compact('jurusan'));
    }

    public function updateJurusan(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'nama_jurusan' => ['required','string','max:255'],
            'kode_jurusan' => ['required','string','max:10', Rule::unique('jurusans','kode_jurusan')->ignore($jurusan->id)],
            'deskripsi'    => ['nullable','string'],
            'kaprodi'      => ['nullable','string','max:255'],
            'nip_kaprodi'  => ['nullable','string','max:20'],
            'status'       => ['required', Rule::in(['active','inactive'])],
        ]);

        $jurusan->update($request->only([
            'nama_jurusan','kode_jurusan','deskripsi','kaprodi','nip_kaprodi','status'
        ]));

        return redirect()->route('superadmin.jurusans')->with('success', 'Program studi berhasil diupdate.');
    }

    public function toggleJurusanStatus(Jurusan $jurusan)
    {
        $jurusan->status = $jurusan->status === 'active' ? 'inactive' : 'active';
        $jurusan->save();

        return back()->with('success', 'Status program studi berhasil diubah.');
    }

    public function destroyJurusan(Jurusan $jurusan)
    {
        $jurusan->delete();
        return redirect()->route('superadmin.jurusans')->with('success', 'Program studi berhasil dihapus.');
    }

    public function jurusanDetail(Jurusan $jurusan)
    {
        $jurusan->loadCount([
            'users as mahasiswa_count'        => fn($q) => $q->where('role','user'),
            'skpiData as skpi_approved_count' => fn($q) => $q->where('status','approved'),
            'skpiData as skpi_submitted_count'=> fn($q) => $q->where('status','submitted'),
            'skpiData as skpi_pending_count'  => fn($q) => $q->where('status','pending'),
        ]);

        $html = view('superadmin.partials.jurusan-detail', compact('jurusan'))->render();

        return response()->json(['ok' => true, 'html' => $html]);
    }

    /* SKPI list & print */
    public function allSkpi(Request $request)
    {
        $query = SkpiData::with(['user','jurusan','reviewer','approver'])->latest('updated_at');

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('jurusan'))  $query->where('jurusan_id', $request->jurusan);
        if ($request->filled('reviewer')) {
            $rev = $request->reviewer;
            $query->whereHas('reviewer', fn($q) => $q->where('name','like',"%{$rev}%"));
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_lengkap','like',"%{$s}%")
                  ->orWhere('npm','like',"%{$s}%")
                  ->orWhere('program_studi','like',"%{$s}%")
                  ->orWhereHas('user', fn($u) => $u->where('name','like',"%{$s}%")
                                                   ->orWhere('email','like',"%{$s}%"))
                  ->orWhereHas('jurusan', fn($j) => $j->where('nama_jurusan','like',"%{$s}%"));
            });
        }

        $skpiList = $query->paginate(15)->appends($request->query());
        $jurusans = Jurusan::orderBy('nama_jurusan')->get(['id','nama_jurusan']);

        return view('superadmin.skpi-list', compact('skpiList','jurusans'));
    }

    public function showSkpi(SkpiData $skpi)
    {
        $skpi->load(['user','jurusan','reviewer','approver']);
        return view('superadmin.skpi-show', compact('skpi'));
    }

    public function printSkpi(SkpiData $skpi)
    {
        if ($skpi->status !== 'approved') {
            return redirect()->route('superadmin.skpi.show', $skpi)
                ->with('error', 'Hanya SKPI berstatus approved yang dapat dicetak.');
        }

        $skpi->load(['user','jurusan','approver']);
        return view('skpi.print', compact('skpi'));
    }

    public function approveSkpi(SkpiData $skpi)
    {
        if ($skpi->status !== 'submitted') {
            return back()->with('error', 'Hanya SKPI berstatus submitted yang dapat di-approve.');
        }

        $skpi->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'reviewed_by' => $skpi->reviewed_by ?? auth()->id(),
            'reviewed_at' => $skpi->reviewed_at ?? now(),
        ]);

        return back()->with('success', 'SKPI berhasil di-approve.');
    }

    public function rejectSkpi(Request $request, SkpiData $skpi)
    {
        $request->validate([
            'catatan_reviewer' => ['nullable','string','max:1000'],
        ]);

        if (!in_array($skpi->status, ['submitted','approved','draft'])) {
            return back()->with('error', 'Status SKPI tidak valid untuk ditolak.');
        }

        $skpi->update([
            'status'           => 'rejected',
            'catatan_reviewer' => $request->catatan_reviewer,
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
            'approved_by'      => null,
            'approved_at'      => null,
        ]);

        return back()->with('success', 'SKPI berhasil ditolak.');
    }

    public function reports()
    {
        // Statistik utama
        $total_users = User::count();
        $total_mahasiswa = User::where('role', 'user')->count();
        $total_admin = User::whereIn('role', ['admin', 'superadmin'])->count();
        
        $total_skpi = SkpiData::count();
        $approved_skpi = SkpiData::where('status', 'approved')->count();
        $pending_skpi = SkpiData::where('status', 'submitted')->count();
        $rejected_skpi = SkpiData::where('status', 'rejected')->count();
        
        $approved_percentage = $total_skpi > 0 ? round(($approved_skpi / $total_skpi) * 100, 1) : 0;
        $pending_percentage = $total_skpi > 0 ? round(($pending_skpi / $total_skpi) * 100, 1) : 0;
        $rejected_percentage = $total_skpi > 0 ? round(($rejected_skpi / $total_skpi) * 100, 1) : 0;
        
        $stats = [
            'total_users' => $total_users,
            'total_mahasiswa' => $total_mahasiswa,
            'total_admin' => $total_admin,
            'total_skpi' => $total_skpi,
            'approved_skpi' => $approved_skpi,
            'pending_skpi' => $pending_skpi,
            'rejected_skpi' => $rejected_skpi,
            'approval_percentage' => $approved_percentage,
            'pending_percentage' => $pending_percentage,
            'rejected_percentage' => $rejected_percentage,
            'total_jurusan' => Jurusan::count(),
            'active_jurusan' => Jurusan::where('status', 'active')->count(),
            'inactive_jurusan' => Jurusan::where('status', 'inactive')->count(),
        ];

        // Statistik per jurusan
        $jurusanStats = Jurusan::withCount([
            'skpiData as jumlah_skpi' => fn($q) => $q->where('status', '!=', 'draft'),
        ])
        ->where('status', 'active')
        ->orderBy('jumlah_skpi', 'desc')
        ->get()
        ->map(function ($jurusan) use ($total_skpi) {
            $jumlah_skpi = $jurusan->jumlah_skpi;
            $persentase = $total_skpi > 0 ? round(($jumlah_skpi / $total_skpi) * 100, 1) : 0;
            
            return [
                'id' => $jurusan->id,
                'nama_jurusan' => $jurusan->nama_jurusan,
                'jumlah_skpi' => $jumlah_skpi,
                'persentase' => $persentase,
            ];
        });

        // Detail statistik per jurusan
        $detailedStats = Jurusan::with([
            'users' => fn($q) => $q->where('role', 'user')
        ])
        ->withCount([
            'skpiData as total_skpi' => fn($q) => $q->where('status', '!=', 'draft'),
            'skpiData as pending' => fn($q) => $q->where('status', 'submitted'),
            'skpiData as approved' => fn($q) => $q->where('status', 'approved'),
            'skpiData as rejected' => fn($q) => $q->where('status', 'rejected'),
        ])
        ->get()
        ->map(function ($jurusan) {
            $total = $jurusan->total_skpi;
            $approved = $jurusan->approved;
            $approval_rate = $total > 0 ? round(($approved / $total) * 100, 1) : 0;
            
            return [
                'nama_jurusan' => $jurusan->nama_jurusan,
                'total_skpi' => $total,
                'approved' => $approved,
                'pending' => $jurusan->pending,
                'rejected' => $jurusan->rejected,
                'approval_rate' => $approval_rate,
            ];
        });

        return view('superadmin.reports', compact('stats', 'jurusanStats', 'detailedStats'));
    }
}
