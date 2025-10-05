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
        
        // Preserve query parameters in pagination
        $skpiList->appends($request->query());
        
        return view('admin.skpi-list', compact('skpiList'));
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
}