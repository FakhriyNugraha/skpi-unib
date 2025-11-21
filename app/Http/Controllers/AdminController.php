<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SkpiData;
use App\Models\User;
use App\Models\Jurusan;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $user = Auth::user();
        
        $query = SkpiData::query();
        
        // Admin hanya bisa melihat data dari jurusan mereka
        if ($user->role === 'admin' && $user->jurusan_id) {
            $query->where('jurusan_id', $user->jurusan_id);
        }
        
        $stats = [
            'pending' => $query->clone()->where('status', 'submitted')->count(),
            'approved' => $query->clone()->where('status', 'approved')->count(),
            'rejected' => $query->clone()->where('status', 'rejected')->count(),
            'total' => $query->clone()->count(),
        ];
        
        $recentSubmissions = $query->clone()
            ->where('status', 'submitted')
            ->with(['user', 'jurusan'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact('stats', 'recentSubmissions'));
    }

    public function skpiList(Request $request)
    {
        $user = Auth::user();

        $query = SkpiData::with(['user', 'jurusan', 'reviewer', 'approver']);

        // Admin hanya bisa melihat data dari jurusan mereka
        if ($user->role === 'admin' && $user->jurusan_id) {
            $query->where('jurusan_id', $user->jurusan_id);
        }

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Add period filter
        if ($request->filled('periode_wisuda') && $request->periode_wisuda !== 'all') {
            $query->where('periode_wisuda', $request->periode_wisuda);
        }

        if ($request->filled('jurusan') && $user->role === 'superadmin') {
            $query->where('jurusan_id', $request->jurusan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', '%' . $search . '%')
                  ->orWhere('npm', 'LIKE', '%' . $search . '%');
            });
        }

        $skpiList = $query->orderBy('updated_at', 'desc')->paginate(15);

        // Get available periods for the dropdown (only for the admin's jurisdiction)
        $availablePeriodsQuery = SkpiData::select('periode_wisuda')
            ->whereNotNull('periode_wisuda');

        if ($user->role === 'admin' && $user->jurusan_id) {
            $availablePeriodsQuery->where('jurusan_id', $user->jurusan_id);
        }

        $availablePeriods = $availablePeriodsQuery
            ->distinct()
            ->orderBy('periode_wisuda', 'desc')
            ->pluck('periode_wisuda')
            ->map(function ($period) {
                $range = \App\Helpers\PeriodHelper::getPeriodRange($period);
                return [
                    'number' => $period,
                    'title' => $range['title']
                ];
            })
            ->values();

        // Preserve query parameters in pagination
        $skpiList->appends($request->query());

        return view('admin.skpi-list', compact('skpiList', 'availablePeriods'));
    }

    public function reviewSkpi(SkpiData $skpi)
    {
        $user = Auth::user();
        
        // Admin hanya bisa review data dari jurusan mereka
        if ($user->role === 'admin' && $user->jurusan_id && $skpi->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak dapat mengakses data dari jurusan lain.');
        }
        
        return view('admin.review-skpi', compact('skpi'));
    }

    public function approveSkpi(Request $request, SkpiData $skpi)
    {
        $user = Auth::user();
        
        // Admin hanya bisa approve data dari jurusan mereka
        if ($user->role === 'admin' && $user->jurusan_id && $skpi->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak dapat mengakses data dari jurusan lain.');
        }
        
        if (!$skpi->canBeApproved()) {
            return redirect()->route('admin.skpi-list')->with('error', 'Data SKPI tidak dapat disetujui.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'catatan_reviewer' => 'nullable|string',
        ]);

        $status = $request->action === 'approve' ? 'approved' : 'rejected';
        
        $skpi->update([
            'status' => $status,
            'catatan_reviewer' => $request->catatan_reviewer,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'approved_by' => $request->action === 'approve' ? $user->id : null,
            'approved_at' => $request->action === 'approve' ? now() : null,
        ]);

        $message = $request->action === 'approve' ? 'Data SKPI berhasil disetujui.' : 'Data SKPI ditolak.';
        
        return redirect()->route('admin.skpi-list')->with('success', $message);
    }

    public function printSkpi(SkpiData $skpi)
    {
        $user = Auth::user();

        // Admin hanya bisa print data dari jurusan mereka
        if ($user->role === 'admin' && $user->jurusan_id && $skpi->jurusan_id !== $user->jurusan_id) {
            abort(403, 'Anda tidak dapat mengakses data dari jurusan lain.');
        }

        if ($skpi->status !== 'approved') {
            return redirect()->route('admin.skpi-list')->with('error', 'Hanya SKPI yang sudah disetujui yang dapat dicetak.');
        }

        return view('skpi.print', compact('skpi'));
    }

    public function printBulkForm()
    {
        $user = Auth::user();

        $query = SkpiData::with(['jurusan', 'approver'])->where('status', 'approved');

        // Admin hanya bisa melihat data dari jurusan mereka
        if ($user->role === 'admin' && $user->jurusan_id) {
            $query->where('jurusan_id', $user->jurusan_id);
        }

        $skpis = $query->orderBy('nama_lengkap')->get();

        // Get available periods for filtering
        $periodQuery = SkpiData::select('periode_wisuda')
            ->where('status', 'approved')
            ->whereNotNull('periode_wisuda');

        if ($user->role === 'admin' && $user->jurusan_id) {
            $periodQuery->where('jurusan_id', $user->jurusan_id);
        }

        $availablePeriods = $periodQuery
            ->distinct()
            ->orderBy('periode_wisuda', 'desc')
            ->get()
            ->map(function ($period) {
                $range = \App\Helpers\PeriodHelper::getPeriodRange($period->periode_wisuda);
                return [
                    'number' => $period->periode_wisuda,
                    'title' => $range['title']
                ];
            })
            ->values();

        return view('admin.print-bulk-form', compact('skpis', 'availablePeriods'));
    }

    public function printBulk(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'skpi_ids' => 'required|array|min:1',
            'skpi_ids.*' => 'integer|exists:skpi_data,id'
        ]);

        $skpiIds = $request->skpi_ids;

        // Query SKPI yang approved dan hanya dari jurusan yang sesuai jika admin
        $query = SkpiData::with(['jurusan', 'approver'])
            ->where('status', 'approved')
            ->whereIn('id', $skpiIds);

        if ($user->role === 'admin' && $user->jurusan_id) {
            $query->where('jurusan_id', $user->jurusan_id);
        }

        $skpis = $query->get();

        if ($skpis->isEmpty()) {
            return redirect()->route('admin.skpi-list')->with('error', 'Tidak ada SKPI yang valid ditemukan atau Anda tidak memiliki akses ke SKPI tersebut.');
        }

        return view('skpi.print-bulk', ['skpis' => $skpis]);
    }

    public function printBulkAll()
    {
        $user = Auth::user();

        // Query semua SKPI yang approved
        $query = SkpiData::with(['jurusan', 'approver'])->where('status', 'approved');

        if ($user->role === 'admin' && $user->jurusan_id) {
            $query->where('jurusan_id', $user->jurusan_id);
        }

        $skpis = $query->get();

        if ($skpis->isEmpty()) {
            return redirect()->route('admin.skpi-list')->with('error', 'Tidak ada SKPI approved yang ditemukan.');
        }

        return view('skpi.print-bulk', ['skpis' => $skpis]);
    }

}