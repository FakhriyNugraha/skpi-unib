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
        // Ambil periode yang dipilih dari request
        $selectedPeriod = $request->input('periode_wisuda');
        $hasPeriodFilter = $selectedPeriod && $selectedPeriod !== 'all';

        $periodRange = null;
        $selectedPeriodTitle = null;

        if ($hasPeriodFilter) {
            // PeriodHelper mengembalikan ['start' => Carbon, 'end' => Carbon, 'title' => string]
            $periodRange = PeriodHelper::getPeriodRange($selectedPeriod);
            $selectedPeriodTitle = $periodRange['title'];
        }

        // Query builder SKPI (akan dipakai berkali-kali)
        $skpiQuery = SkpiData::query();

        if ($hasPeriodFilter) {
            $skpiQuery->where('periode_wisuda', $selectedPeriod);
        }

        // Ambil jurusan aktif
        $jurusans = Jurusan::active()->get();

        // Data agregat SKPI per jurusan & status (untuk bar chart, dll)
        $jurusanSkpiData = SkpiData::selectRaw('jurusan_id, status, COUNT(*) as total')
            ->when($hasPeriodFilter, function ($query) use ($selectedPeriod) {
                return $query->where('periode_wisuda', $selectedPeriod);
            })
            ->groupBy('jurusan_id', 'status')
            ->get()
            ->groupBy('jurusan_id');

        // Statistik umum
        $totalMahasiswa = User::where('role', 'user');

        if ($hasPeriodFilter) {
            // Hanya mahasiswa yang punya SKPI di periode terpilih
            $totalMahasiswa->whereIn('id', function ($query) use ($selectedPeriod) {
                $query->select('user_id')
                    ->from('skpi_data')
                    ->where('periode_wisuda', $selectedPeriod);
            });
        }

        $totalMahasiswaCount = $totalMahasiswa->count();

        $stats = [
            'total_jurusan'       => $jurusans->count(),
            'total_skpi'          => (clone $skpiQuery)->count(),
            'total_skpi_approved' => (clone $skpiQuery)->where('status', 'approved')->count(),
            'total_mahasiswa'     => $totalMahasiswaCount,
        ];

        // DATA BAR CHART (SKPI per Prodi)
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

        // Palet warna PIE
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

        // DATA PIE (Distribusi Mahasiswa per Jurusan)
        $pieData = [
            'labels'   => [],
            'datasets' => [
                [
                    'data'            => [],
                    'backgroundColor' => [],
                ],
            ],
        ];

        // Isi dataset bar & pie
        if ($jurusans->count() > 0) {
            $i = 0;
            foreach ($jurusans as $jurusan) {
                $chartData['labels'][] = $jurusan->kode_jurusan;

                // Agregat SKPI per status untuk jurusan ini (sudah terfilter periode di $jurusanSkpiData)
                $jurusanData = $jurusanSkpiData->get($jurusan->id, collect());

                $draft     = $jurusanData->where('status', 'draft')->sum('total');
                $submitted = $jurusanData->where('status', 'submitted')->sum('total');
                $approved  = $jurusanData->where('status', 'approved')->sum('total');
                $rejected  = $jurusanData->where('status', 'rejected')->sum('total');

                $chartData['datasets'][0]['data'][] = (int) $draft;
                $chartData['datasets'][1]['data'][] = (int) $submitted;
                $chartData['datasets'][2]['data'][] = (int) $approved;
                $chartData['datasets'][3]['data'][] = (int) $rejected;

                // Pie chart: jumlah mahasiswa per jurusan yang punya SKPI di periode yg dipilih
                $mahasiswaCount = User::where('role', 'user')
                    ->where('jurusan_id', $jurusan->id)
                    ->when($hasPeriodFilter, function ($query) use ($selectedPeriod) {
                        return $query->whereIn('id', function ($subQuery) use ($selectedPeriod) {
                            $subQuery->select('user_id')
                                ->from('skpi_data')
                                ->where('periode_wisuda', $selectedPeriod);
                        });
                    })
                    ->count();

                $pieData['labels'][] = $jurusan->nama_jurusan;
                $pieData['datasets'][0]['data'][] = (int) $mahasiswaCount;
                $pieData['datasets'][0]['backgroundColor'][] = $palette[$i % count($palette)];

                $i++;
            }
        } else {
            // Tidak ada jurusan
            $chartData['labels'] = [];
            foreach ($chartData['datasets'] as &$dataset) {
                $dataset['data'] = [];
            }
            $pieData['labels'] = [];
            $pieData['datasets'][0]['data'] = [];
            $pieData['datasets'][0]['backgroundColor'] = [];
        }

        // LINE CHART - Tren Bulanan
        $monthlyTrend = [];

        if ($hasPeriodFilter && $periodRange) {
            // Jika periode dipilih → gunakan range periode (mis: Jan–Mar 2026 → 3 bulan saja)
            $currentDate = $periodRange['start']->copy()->startOfMonth();
            $endOfMonth  = $periodRange['end']->copy()->endOfMonth();

            while ($currentDate->lte($endOfMonth)) {
                $year  = $currentDate->year;
                $month = $currentDate->month;

                $approvedCount = SkpiData::where('status', 'approved')
                    ->where('periode_wisuda', $selectedPeriod)
                    ->whereYear('approved_at', $year)
                    ->whereMonth('approved_at', $month)
                    ->count();

                $submittedCount = SkpiData::where('status', 'submitted')
                    ->where('periode_wisuda', $selectedPeriod)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count();

                $monthlyTrend[] = [
                    'month'     => $currentDate->format('M Y'),
                    'approved'  => (int) $approvedCount,
                    'submitted' => (int) $submittedCount,
                ];

                $currentDate->addMonth();
            }
        } else {
            // Tidak ada periode dipilih → default 6 bulan terakhir (global)
            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);

                $approvedCount = SkpiData::where('status', 'approved')
                    ->whereYear('approved_at', $date->year)
                    ->whereMonth('approved_at', $date->month)
                    ->count();

                $submittedCount = SkpiData::where('status', 'submitted')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $monthlyTrend[] = [
                    'month'     => $date->format('M Y'),
                    'approved'  => (int) $approvedCount,
                    'submitted' => (int) $submittedCount,
                ];
            }
        }

        // Daftar periode untuk dropdown
        $availablePeriods = SkpiData::select('periode_wisuda')
            ->whereNotNull('periode_wisuda')
            ->distinct()
            ->orderBy('periode_wisuda', 'desc')
            ->pluck('periode_wisuda')
            ->map(function ($period) {
                $range = PeriodHelper::getPeriodRange($period);
                return [
                    'number' => $period,
                    'title'  => $range['title'],
                ];
            })
            ->values();

        return view('welcome', [
            'jurusans'            => $jurusans,
            'stats'               => $stats,
            'chartData'           => $chartData,
            'pieData'             => $pieData,
            'monthlyTrend'        => $monthlyTrend,
            'availablePeriods'    => $availablePeriods,
            'selectedPeriod'      => $selectedPeriod,
            'selectedPeriodTitle' => $selectedPeriodTitle,
        ]);
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
