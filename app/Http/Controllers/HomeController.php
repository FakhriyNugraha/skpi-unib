<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurusan;
use App\Models\SkpiData;
use App\Models\User;
use App\Helpers\PeriodHelper;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Query builder for SKPI data with optional period filter
        $skpiQuery = SkpiData::query();

        // Add period filtering
        if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
            $skpiQuery->where('periode_wisuda', $request->periode_wisuda);
        }

        // Ambil jurusan + agregasi SKPI per status with period filtering
        $jurusans = Jurusan::active()->with(['skpiData' => function ($query) use ($request) {
            // Apply period filter if specified
            if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
                $query->where('periode_wisuda', $request->periode_wisuda);
            }
            // Hindari DB::raw, pakai selectRaw agar simple
            $query->select('jurusan_id', 'status')
                  ->selectRaw('COUNT(*) as total')
                  ->groupBy('jurusan_id', 'status');
        }])->get();

        // Statistik umum with period filtering
        $totalMahasiswa = User::where('role', 'user');
        if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
            // Count students who have SKPI data in the selected period
            $totalMahasiswa = $totalMahasiswa->whereIn('id', function($query) use ($request) {
                $query->select('user_id')
                      ->from('skpi_data')
                      ->where('periode_wisuda', $request->periode_wisuda);
            });
        }
        $totalMahasiswaCount = $totalMahasiswa->count();

        $stats = [
            'total_jurusan'        => $jurusans->count(),
            'total_skpi'           => $skpiQuery->count(),
            // Hindari scope approved() yang belum tentu ada
            'total_skpi_approved'  => $skpiQuery->where('status', 'approved')->count(),
            'total_mahasiswa'      => $totalMahasiswaCount,
        ];

        // Data untuk grafik batang per jurusan
        $chartData = [
            'labels'   => [],
            'datasets' => [
                [
                    'label'           => 'Draft',
                    'data'            => [],
                    'backgroundColor' => 'rgba(255, 193, 7, 0.8)',
                    'borderColor'     => 'rgba(255, 193, 7, 1)',
                    'borderWidth'     => 2,
                ],
                [
                    'label'           => 'Submitted',
                    'data'            => [],
                    'backgroundColor' => 'rgba(13, 110, 253, 0.8)',
                    'borderColor'     => 'rgba(13, 110, 253, 1)',
                    'borderWidth'     => 2,
                ],
                [
                    'label'           => 'Approved',
                    'data'            => [],
                    'backgroundColor' => 'rgba(25, 135, 84, 0.8)',
                    'borderColor'     => 'rgba(25, 135, 84, 1)',
                    'borderWidth'     => 2,
                ],
                [
                    'label'           => 'Rejected',
                    'data'            => [],
                    'backgroundColor' => 'rgba(220, 53, 69, 0.8)',
                    'borderColor'     => 'rgba(220, 53, 69, 1)',
                    'borderWidth'     => 2,
                ],
            ],
        ];

        // Palet warna untuk pie (akan diulang jika jurusan > jumlah warna)
        $palette = [
            'rgba(30, 58, 138, 0.8)',   // unib-blue-800
            'rgba(249, 115, 22, 0.8)',  // teknik-orange-500
            'rgba(59, 130, 246, 0.8)',  // blue-500
            'rgba(16, 185, 129, 0.8)',  // emerald-500
            'rgba(234, 179, 8, 0.8)',   // yellow-500
            'rgba(168, 85, 247, 0.8)',  // purple-500
            'rgba(236, 72, 153, 0.8)',  // pink-500
            'rgba(14, 165, 233, 0.8)',  // sky-500
        ];

        // Data untuk pie chart (distribusi mahasiswa per jurusan) — format Chart.js yang benar
        $pieData = [
            'labels'   => [],
            'datasets' => [
                [
                    'data'            => [],
                    'backgroundColor' => [], // diisi per-slice agar tidak kosong
                ],
            ],
        ];

        // Isi data chart
        $i = 0;
        foreach ($jurusans as $jurusan) {
            $chartData['labels'][] = $jurusan->kode_jurusan;

            // Hitung agregasi status per jurusan (sudah di-preload melalui with('skpiData'))
            $draft     = $jurusan->skpiData->where('status', 'draft')->sum('total') ?? 0;
            $submitted = $jurusan->skpiData->where('status', 'submitted')->sum('total') ?? 0;
            $approved  = $jurusan->skpiData->where('status', 'approved')->sum('total') ?? 0;
            $rejected  = $jurusan->skpiData->where('status', 'rejected')->sum('total') ?? 0;

            $chartData['datasets'][0]['data'][] = $draft;
            $chartData['datasets'][1]['data'][] = $submitted;
            $chartData['datasets'][2]['data'][] = $approved;
            $chartData['datasets'][3]['data'][] = $rejected;

            // Data pie: jumlah mahasiswa per jurusan
            $mahasiswaCount = User::where('role', 'user')
                                  ->where('jurusan_id', $jurusan->id)
                                  ->count();

            $pieData['labels'][]                   = $jurusan->nama_jurusan;
            $pieData['datasets'][0]['data'][]      = $mahasiswaCount;
            // Pastikan ada warna untuk setiap slice
            $pieData['datasets'][0]['backgroundColor'][] = $palette[$i % count($palette)];

            $i++;
        }

        // Data tren bulanan (6 bulan terakhir) with period filtering
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            // Count approved SKPIs for the specific month/year
            $approvedCount = SkpiData::where('status', 'approved')
                ->when($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all', function ($query) use ($request) {
                    return $query->where('periode_wisuda', $request->periode_wisuda);
                })
                ->whereYear('approved_at', $date->year)
                ->whereMonth('approved_at', $date->month)
                ->count();

            // Count submitted SKPIs for the specific month/year
            $submittedCount = SkpiData::where('status', 'submitted')
                ->when($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all', function ($query) use ($request) {
                    return $query->where('periode_wisuda', $request->periode_wisuda);
                })
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $monthlyTrend[] = [
                'month'     => $date->format('M Y'),
                'approved'  => $approvedCount,
                'submitted' => $submittedCount,
            ];
        }

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

        return view('welcome', compact('jurusans', 'stats', 'chartData', 'pieData', 'monthlyTrend', 'availablePeriods'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        switch ($user->role) {
            case 'superadmin':
                return redirect()->route('superadmin.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'user':
                return redirect()->route('skpi.index');
            default:
                return redirect()->route('home');
        }
    }
}
