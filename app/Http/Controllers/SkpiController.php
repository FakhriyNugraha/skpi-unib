<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SkpiData;
use App\Models\Jurusan;
// use App\Models\Document; // opsional: tak perlu jika upload dihapus total
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SkpiController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();
        $skpiData = SkpiData::with(['jurusan'])
            ->where('user_id', $user->id)
            ->first();

        return view('skpi.index', compact('skpiData'));
    }

    public function create()
    {
        $user = Auth::user();
        $existingSkpi = SkpiData::where('user_id', $user->id)->first();

        if ($existingSkpi && !$existingSkpi->canBeEdited()) {
            return redirect()->route('skpi.index')->with('error', 'Data SKPI Anda sudah disubmit dan tidak dapat diedit.');
        }

        $jurusans = Jurusan::active()->orderBy('nama_jurusan')->get();

        return view('skpi.create', compact('jurusans', 'existingSkpi'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nama_lengkap'          => 'required|string|max:255',
            'npm'                   => 'required|string|max:20', // konsisten pakai npm
            'tempat_lahir'          => 'required|string|max:255',
            'tanggal_lahir'         => 'required|date',
            'nomor_ijazah'          => 'required|string|max:255',
            'tanggal_lulus'         => 'required|date',
            'gelar'                 => 'required|string|max:50',
            'program_studi'         => 'required|string|max:255',
            'jurusan_id'            => 'required|exists:jurusans,id',
            'ipk'                   => 'required|numeric|min:0|max:4',
            'prestasi_akademik'     => 'nullable|string',
            'prestasi_non_akademik' => 'nullable|string',
            'organisasi'            => 'nullable|string',
            'pengalaman_kerja'      => 'nullable|string',
            'sertifikat_kompetensi' => 'nullable|string',
            'catatan_khusus'        => 'nullable|string',

            // HAPUS rule upload karena fitur upload ditiadakan
            // 'sertifikat_files.*'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            // 'dokumen_pendukung.*'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $allowed = [
            'nama_lengkap',
            'npm',
            'tempat_lahir',
            'tanggal_lahir',
            'nomor_ijazah',
            'tanggal_lulus',
            'gelar',
            'program_studi',
            'jurusan_id',
            'ipk',
            'prestasi_akademik',
            'prestasi_non_akademik',
            'organisasi',
            'pengalaman_kerja',
            'sertifikat_kompetensi',
            'catatan_khusus',
        ];

        $payload = $request->only($allowed);
        $payload['user_id'] = $user->id;
        $payload['status']  = 'draft';

        $skpiData = SkpiData::updateOrCreate(
            ['user_id' => $user->id],
            $payload
        );

        // Upload DIHAPUS: jangan panggil handleFileUploads

        return redirect()->route('skpi.show', $skpiData)->with('success', 'Data SKPI berhasil disimpan sebagai draft.');
    }

    public function show(SkpiData $skpi)
    {
        $this->authorize('view', $skpi);

        // jika upload dihapus, relasi documents bisa tetap diload (kosong) atau dihapus dari sini
        $skpi->load(['jurusan', 'documents', 'reviewer', 'approver']);

        return view('skpi.show', compact('skpi'));
    }

    public function edit(SkpiData $skpi)
    {
        $this->authorize('update', $skpi);

        if (!$skpi->canBeEdited()) {
            return redirect()->route('skpi.index')->with('error', 'Data SKPI tidak dapat diedit.');
        }

        $jurusans = Jurusan::active()->orderBy('nama_jurusan')->get();
        $existingSkpi = $skpi->load(['jurusan', 'documents']);

        return view('skpi.create', compact('jurusans', 'existingSkpi'));
    }

    public function update(Request $request, SkpiData $skpi)
    {
        $this->authorize('update', $skpi);

        if (!$skpi->canBeEdited()) {
            return redirect()->route('skpi.index')->with('error', 'Data SKPI tidak dapat diedit.');
        }

        $validator = Validator::make($request->all(), [
            'nama_lengkap'          => 'required|string|max:255',
            'npm'                   => 'required|string|max:20', // konsisten
            'tempat_lahir'          => 'required|string|max:255',
            'tanggal_lahir'         => 'required|date',
            'nomor_ijazah'          => 'required|string|max:255',
            'tanggal_lulus'         => 'required|date',
            'gelar'                 => 'required|string|max:50',
            'program_studi'         => 'required|string|max:255',
            'jurusan_id'            => 'required|exists:jurusans,id',
            'ipk'                   => 'required|numeric|min:0|max:4',
            'prestasi_akademik'     => 'nullable|string',
            'prestasi_non_akademik' => 'nullable|string',
            'organisasi'            => 'nullable|string',
            'pengalaman_kerja'      => 'nullable|string',
            'sertifikat_kompetensi' => 'nullable|string',
            'catatan_khusus'        => 'nullable|string',

            // rule upload DIHAPUS
            // 'sertifikat_files.*'    => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            // 'dokumen_pendukung.*'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $allowed = [
            'nama_lengkap',
            'npm',
            'tempat_lahir',
            'tanggal_lahir',
            'nomor_ijazah',
            'tanggal_lulus',
            'gelar',
            'program_studi',
            'jurusan_id',
            'ipk',
            'prestasi_akademik',
            'prestasi_non_akademik',
            'organisasi',
            'pengalaman_kerja',
            'sertifikat_kompetensi',
            'catatan_khusus',
        ];

        $skpi->update($request->only($allowed));

        // Upload DIHAPUS: jangan panggil handleFileUploads

        return redirect()->route('skpi.show', $skpi)->with('success', 'Data SKPI berhasil diupdate.');
    }

    public function submit(SkpiData $skpi)
    {
        $this->authorize('update', $skpi);

        if (!$skpi->canBeSubmitted()) {
            return redirect()->route('skpi.index')->with('error', 'Data SKPI tidak dapat disubmit.');
        }

        $skpi->update([
            'status'      => 'submitted',
            'reviewed_at' => null,
            'approved_at' => null,
        ]);

        return redirect()->route('skpi.index')->with('success', 'Data SKPI berhasil disubmit untuk review.');
    }

    public function print(SkpiData $skpi)
    {
        $this->authorize('view', $skpi);

        if ($skpi->status !== 'approved') {
            return redirect()->route('skpi.index')->with('error', 'Hanya SKPI yang sudah disetujui yang dapat dicetak.');
        }

        $skpi->load(['jurusan', 'approver']);

        return view('skpi.print', compact('skpi'));
    }

    // NOTE: handleFileUploads DIHAPUS kalau upload tidak dipakai lagi
}
