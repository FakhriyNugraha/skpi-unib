<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jurusan;
use App\Models\SkpiData;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil jurusan + agregasi SKPI per status
        $jurusans = Jurusan::active()->with(['skpiData' => function ($query) {
            // Hindari DB::raw, pakai selectRaw agar simple
            $query->select('jurusan_id', 'status')
                  ->selectRaw('COUNT(*) as total')
                  ->groupBy('jurusan_id', 'status');
        }])->get();
        
        // Statistik umum
        $stats = [
            'total_jurusan'        => $jurusans->count(),
            'total_skpi'           => SkpiData::count(),
            // Hindari scope approved() yang belum tentu ada
            'total_skpi_approved'  => SkpiData::where('status', 'approved')->count(),
            'total_mahasiswa'      => User::where('role', 'user')->count(),
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

        // Data tren bulanan (6 bulan terakhir)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyTrend[] = [
                'month'     => $date->format('M Y'),
                'approved'  => SkpiData::where('status', 'approved')
                    ->whereYear('approved_at', $date->year)
                    ->whereMonth('approved_at', $date->month)
                    ->count(),
                'submitted' => SkpiData::where('status', 'submitted')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        return view('welcome', compact('jurusans', 'stats', 'chartData', 'pieData', 'monthlyTrend'));
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
