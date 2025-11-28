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
use App\Helpers\PeriodHelper;

class SuperAdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // No period filtering for dashboard
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
        $currentUser = Auth::user();
        $query = User::with('jurusan')->latest();

        // Filter berdasarkan jurusan jika user adalah admin biasa (bukan superadmin)
        if ($currentUser && $currentUser->role === 'admin' && $currentUser->jurusan_id) {
            $query->where('jurusan_id', $currentUser->jurusan_id);
        }

        if ($request->filled('role'))    $query->where('role', $request->role);
        if ($request->filled('jurusan')) $query->where('jurusan_id', $request->jurusan);

        // Filter berdasarkan periode wisuda (menggunakan data dari skpi_data)
        if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
            $skpiUsers = SkpiData::where('periode_wisuda', $request->periode_wisuda)
                ->pluck('user_id')
                ->toArray();

            $query->whereIn('id', $skpiUsers);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('npm', 'like', "%{$s}%")
                  ->orWhere('nip', 'like', "%{$s}%");
            });
        }

        // Dapatkan periode wisuda yang tersedia untuk dropdown
        $periodQuery = SkpiData::select('periode_wisuda')
            ->whereNotNull('periode_wisuda');

        // Filter berdasarkan jurusan hanya jika user adalah admin biasa
        if ($currentUser && $currentUser->role === 'admin' && $currentUser->jurusan_id) {
            $periodQuery->where('jurusan_id', $currentUser->jurusan_id);
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

        $users = $query->paginate(15)->appends($request->query());
        return view('superadmin.users', compact('users', 'availablePeriods'));
    }

    public function createUser()
    {
        $jurusans = Jurusan::active()
            ->orderByRaw("CASE nama_jurusan
                WHEN 'Informatika' THEN 1
                WHEN 'Teknik Sipil' THEN 2
                WHEN 'Teknik Elektro' THEN 3
                WHEN 'Teknik Mesin' THEN 4
                WHEN 'Arsitektur' THEN 5
                WHEN 'Sistem Informasi' THEN 6
                ELSE 99
            END")->get();
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
        $jurusans = Jurusan::active()
            ->orderByRaw("CASE nama_jurusan
                WHEN 'Informatika' THEN 1
                WHEN 'Teknik Sipil' THEN 2
                WHEN 'Teknik Elektro' THEN 3
                WHEN 'Teknik Mesin' THEN 4
                WHEN 'Arsitektur' THEN 5
                WHEN 'Sistem Informasi' THEN 6
                ELSE 99
            END")->get();
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
        $jurusans = Jurusan::with(['users','skpiData'])
            ->orderByRaw("CASE nama_jurusan
                WHEN 'Informatika' THEN 1
                WHEN 'Teknik Sipil' THEN 2
                WHEN 'Teknik Elektro' THEN 3
                WHEN 'Teknik Mesin' THEN 4
                WHEN 'Arsitektur' THEN 5
                WHEN 'Sistem Informasi' THEN 6
                ELSE 99
            END")->get();
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
        try {
            $newStatus = request()->input('status');

            // If status parameter is provided, use it; otherwise toggle the status
            if ($newStatus && in_array($newStatus, ['active', 'inactive'])) {
                $jurusan->status = $newStatus;
            } else {
                // Toggle the status as before
                $jurusan->status = $jurusan->status === 'active' ? 'inactive' : 'active';
            }

            $jurusan->save();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status program studi berhasil diubah.',
                    'status' => $jurusan->status
                ]);
            }

            return back()->with('success', 'Status program studi berhasil diubah.');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah status program studi.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal mengubah status program studi.');
        }
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

        // Add period filtering
        if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
            $query->where('periode_wisuda', $request->periode_wisuda);
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
        $jurusans = Jurusan::orderByRaw("CASE nama_jurusan
                WHEN 'Informatika' THEN 1
                WHEN 'Teknik Sipil' THEN 2
                WHEN 'Teknik Elektro' THEN 3
                WHEN 'Teknik Mesin' THEN 4
                WHEN 'Arsitektur' THEN 5
                WHEN 'Sistem Informasi' THEN 6
                ELSE 99
            END")->get(['id','nama_jurusan']);


        // Get available periods for the dropdown
        $availablePeriods = SkpiData::select('periode_wisuda')
            ->whereNotNull('periode_wisuda')
            ->distinct()
            ->orderBy('periode_wisuda', 'desc')
            ->pluck('periode_wisuda')
            ->map(function ($period) {
                $range = PeriodHelper::getPeriodRange($period);
                return [
                    'number' => $period,
                    'title' => $range['title']
                ];
            })
            ->values();

        return view('superadmin.skpi-list', compact('skpiList','jurusans', 'availablePeriods'));
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

    public function printBulkForm(Request $request)
    {
        $query = SkpiData::with(['jurusan', 'approver'])
            ->where('status', 'approved');

        // Apply filters if they exist in the request
        if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
            $query->where('periode_wisuda', $request->periode_wisuda);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nama_lengkap','like',"%{$s}%")
                  ->orWhere('npm','like',"%{$s}%")
                  ->orWhere('program_studi','like',"%{$s}%");
            });
        }

        $skpis = $query->get();

        // Get available periods for the dropdown
        $availablePeriods = SkpiData::select('periode_wisuda')
            ->whereNotNull('periode_wisuda')
            ->distinct()
            ->orderBy('periode_wisuda', 'desc')
            ->pluck('periode_wisuda')
            ->map(function ($period) {
                $range = PeriodHelper::getPeriodRange($period);
                return [
                    'number' => $period,
                    'title' => $range['title']
                ];
            })
            ->values();

        return view('superadmin.print-bulk-form', compact('skpis', 'availablePeriods'));
    }

    public function printBulk(Request $request)
    {
        $request->validate([
            'skpi_ids' => 'required|array|min:1',
            'skpi_ids.*' => 'integer|exists:skpi_data,id'
        ]);

        $skpiIds = $request->skpi_ids;

        // Query all approved SKPIs (superadmin can access all)
        $skpis = SkpiData::with(['jurusan', 'approver'])
            ->where('status', 'approved')
            ->whereIn('id', $skpiIds)
            ->get();

        if ($skpis->isEmpty()) {
            return redirect()->route('superadmin.all-skpi')->with('error', 'Tidak ada SKPI yang valid ditemukan.');
        }

        return view('skpi.print-bulk', ['skpis' => $skpis]);
    }

    public function printBulkAll()
    {
        // Query all approved SKPIs (superadmin can access all)
        $skpis = SkpiData::with(['jurusan', 'approver'])
            ->where('status', 'approved')
            ->get();

        if ($skpis->isEmpty()) {
            return redirect()->route('superadmin.all-skpi')->with('error', 'Tidak ada SKPI approved yang ditemukan.');
        }

        return view('skpi.print-bulk', ['skpis' => $skpis]);
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

    public function reports(Request $request)
    {
        // Query builder untuk SKPI yang masuk perhitungan visual:
        // hanya status selain 'draft' agar persentase sesuai bar (approved + submitted + rejected)
        $baseSkpiQuery = SkpiData::query()
            ->where('status', '!=', 'draft');

        // Add period filtering
        if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
            $baseSkpiQuery->where('periode_wisuda', $request->periode_wisuda);
        }

        // Count users who have SKPI data in the selected period (if filter is applied) for total users
        $total_users_query = User::query();
        if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
            $total_users_query = $total_users_query->whereIn('id', function($query) use ($request) {
                $query->select('user_id')
                      ->from('skpi_data')
                      ->where('periode_wisuda', $request->periode_wisuda);
            });
        }
        $total_users = $total_users_query->count();

        // Count students who have SKPI data in the selected period (if filter is applied)
        $total_mahasiswa_query = User::where('role', 'user');
        if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
            $total_mahasiswa_query = $total_mahasiswa_query->whereIn('id', function($query) use ($request) {
                $query->select('user_id')
                      ->from('skpi_data')
                      ->where('periode_wisuda', $request->periode_wisuda);
            });
        }
        $total_mahasiswa = $total_mahasiswa_query->count();

        // Total admin count should remain the same (not affected by period filter)
        $total_admin = User::whereIn('role', ['admin', 'superadmin'])->count();

        // Hitung total dan per status (non-draft saja)
        $total_skpi     = (clone $baseSkpiQuery)->count();
        $approved_skpi  = (clone $baseSkpiQuery)->where('status', 'approved')->count();
        $pending_skpi   = (clone $baseSkpiQuery)->where('status', 'submitted')->count();
        $rejected_skpi  = (clone $baseSkpiQuery)->where('status', 'rejected')->count();

        // Persentase untuk visual bar
        $approved_percentage = $total_skpi > 0 ? round(($approved_skpi / $total_skpi) * 100, 1) : 0;
        $pending_percentage  = $total_skpi > 0 ? round(($pending_skpi  / $total_skpi) * 100, 1) : 0;
        $rejected_percentage = $total_skpi > 0 ? round(($rejected_skpi / $total_skpi) * 100, 1) : 0;

        $stats = [
            'total_users'          => $total_users,
            'total_mahasiswa'      => $total_mahasiswa,
            'total_admin'          => $total_admin,
            'total_skpi'           => $total_skpi,
            'approved_skpi'        => $approved_skpi,
            'pending_skpi'         => $pending_skpi,
            'rejected_skpi'        => $rejected_skpi,
            // KEY DIGANTI: sekarang 'approved_percentage'
            'approved_percentage'  => $approved_percentage,
            'pending_percentage'   => $pending_percentage,
            'rejected_percentage'  => $rejected_percentage,
            'total_jurusan'        => Jurusan::count(),
            'active_jurusan'       => Jurusan::where('status', 'active')->count(),
            'inactive_jurusan'     => Jurusan::where('status', 'inactive')->count(),
        ];

        // Statistik per jurusan - showing all SKPIs with verified/unverified breakdown
        $jurusanStats = Jurusan::withCount([
            'skpiData as jumlah_skpi' => function($q) use ($request) {
                if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
                    $q->where('periode_wisuda', $request->periode_wisuda);
                }
                $q->where('status', '!=', 'draft');
            },
            'skpiData as jumlah_skpi_approved' => function($q) use ($request) {
                if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
                    $q->where('periode_wisuda', $request->periode_wisuda);
                }
                $q->where('status', 'approved');
            },
            'skpiData as jumlah_skpi_unapproved' => function($q) use ($request) {
                if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
                    $q->where('periode_wisuda', $request->periode_wisuda);
                }
                $q->whereNotIn('status', ['approved', 'draft']);
            }
        ])
        ->where('status', 'active')
        ->orderBy('jumlah_skpi', 'desc')
        ->get()
        ->map(function ($jurusan) use ($total_skpi) {
            $jumlah_skpi       = $jurusan->jumlah_skpi;          // Count all non-draft SKPIs
            $jumlah_approved   = $jurusan->jumlah_skpi_approved; // Count approved SKPIs
            $jumlah_unapproved = $jurusan->jumlah_skpi_unapproved;
            $persentase        = $total_skpi > 0 ? round(($jumlah_skpi / $total_skpi) * 100, 1) : 0;

            return [
                'id'                => $jurusan->id,
                'nama_jurusan'      => $jurusan->nama_jurusan,
                'jumlah_skpi'       => $jumlah_skpi,
                'jumlah_approved'   => $jumlah_approved,
                'jumlah_unapproved' => $jumlah_unapproved,
                'persentase'        => $persentase,
            ];
        });

        // Detail statistik per jurusan
        $detailedStats = Jurusan::with([
            'users' => fn($q) => $q->where('role', 'user')
        ])
        ->withCount([
            'skpiData as total_skpi' => function($q) use ($request) {
                if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
                    $q->where('periode_wisuda', $request->periode_wisuda);
                }
                $q->where('status', '!=', 'draft');
            },
            'skpiData as pending' => function($q) use ($request) {
                if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
                    $q->where('periode_wisuda', $request->periode_wisuda);
                }
                $q->where('status', 'submitted');
            },
            'skpiData as approved' => function($q) use ($request) {
                if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
                    $q->where('periode_wisuda', $request->periode_wisuda);
                }
                $q->where('status', 'approved');
            },
            'skpiData as rejected' => function($q) use ($request) {
                if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
                    $q->where('periode_wisuda', $request->periode_wisuda);
                }
                $q->where('status', 'rejected');
            },
        ])
        ->orderByRaw("CASE nama_jurusan
            WHEN 'Informatika' THEN 1
            WHEN 'Teknik Sipil' THEN 2
            WHEN 'Teknik Elektro' THEN 3
            WHEN 'Teknik Mesin' THEN 4
            WHEN 'Arsitektur' THEN 5
            WHEN 'Sistem Informasi' THEN 6
            ELSE 99
        END")
        ->get()
        ->map(function ($jurusan) {
            $total         = $jurusan->total_skpi;
            $approved      = $jurusan->approved;
            $approval_rate = $total > 0 ? round(($approved / $total) * 100, 1) : 0;

            return [
                'nama_jurusan'   => $jurusan->nama_jurusan,
                'total_skpi'     => $total,
                'approved'       => $approved,
                'pending'        => $jurusan->pending,
                'rejected'       => $jurusan->rejected,
                'approval_rate'  => $approval_rate,
            ];
        });

        // Get available periods for the dropdown
        $availablePeriods = SkpiData::select('periode_wisuda')
            ->whereNotNull('periode_wisuda')
            ->distinct()
            ->orderBy('periode_wisuda', 'desc')
            ->pluck('periode_wisuda')
            ->map(function ($period) {
                $range = PeriodHelper::getPeriodRange($period);
                return [
                    'number' => $period,
                    'title'  => $range['title']
                ];
            })
            ->values();

        return view('superadmin.reports', compact('stats', 'jurusanStats', 'detailedStats', 'availablePeriods'));
    }
}
