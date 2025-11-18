<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SkpiData;
use App\Models\Jurusan;
use App\Helpers\PeriodHelper;
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

        // Paksa isi phone (opsional kalau kamu sudah buat middleware, lewati ini)
        if (empty($user->phone)) {
            return redirect()->route('profile.edit')
                ->with('warning', 'Lengkapi nomor telepon Anda sebelum membuat SKPI.');
        }

        $existingSkpi = SkpiData::where('user_id', $user->id)->first();
        if ($existingSkpi && !$existingSkpi->canBeEdited()) {
            return redirect()->route('skpi.index')->with('error', 'Data SKPI Anda sudah disubmit dan tidak dapat diedit.');
        }

        $jurusans = Jurusan::active()->orderBy('nama_jurusan')->get();

        // kunci prodi & gelar berdasarkan user/jurusan (logika contoh)
        $lockedProgramStudi = optional($user->jurusan)->nama_jurusan ?? ($existingSkpi->program_studi ?? '');
        $lockedGelar = $this->tentukanGelar($lockedProgramStudi);

        return view('skpi.create', compact('jurusans', 'existingSkpi', 'lockedProgramStudi', 'lockedGelar'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'nama_lengkap'          => 'required|string|max:255',
            'npm'                   => 'required|string|max:50',
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
            'drive_link'            => ['required','url','regex:/^https?:\/\/(drive\.google\.com|docs\.google\.com)\/.+/i'],
        ], [
            'drive_link.regex'      => 'Link Google Drive tidak valid (harus drive.google.com / docs.google.com).',
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
            'drive_link',
        ];

        $payload = $request->only($allowed);
        $payload['user_id'] = $user->id;
        $payload['status']  = 'draft';

        // Calculate and set the period based on graduation date
        $payload['periode_wisuda'] = PeriodHelper::getPeriodeFromDate($request->tanggal_lulus);

        $skpiData = SkpiData::updateOrCreate(
            ['user_id' => $user->id],
            $payload
        );

        // Setelah simpan, langsung ke dashboard
        return redirect()->route('dashboard')->with('success', 'Data SKPI berhasil disimpan sebagai draft.');
    }

    public function show(SkpiData $skpi)
    {
        $this->authorize('view', $skpi);
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
        $existingSkpi = $skpi->load(['jurusan']);

        $lockedProgramStudi = optional(auth()->user()->jurusan)->nama_jurusan ?? ($existingSkpi->program_studi ?? '');
        $lockedGelar = $this->tentukanGelar($lockedProgramStudi);

        return view('skpi.create', compact('jurusans', 'existingSkpi', 'lockedProgramStudi', 'lockedGelar'));
    }

    public function update(Request $request, SkpiData $skpi)
    {
        $this->authorize('update', $skpi);

        if (!$skpi->canBeEdited()) {
            return redirect()->route('skpi.index')->with('error', 'Data SKPI tidak dapat diedit.');
        }

        $validator = Validator::make($request->all(), [
            'nama_lengkap'          => 'required|string|max:255',
            'npm'                   => 'required|string|max:50',
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
            'drive_link'            => ['required','url','regex:/^https?:\/\/(drive\.google\.com|docs\.google\.com)\/.+/i'],
        ], [
            'drive_link.regex'      => 'Link Google Drive tidak valid (harus drive.google.com / docs.google.com).',
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
            'drive_link',
        ];

        // Calculate and set the period based on graduation date
        $periode_wisuda = PeriodHelper::getPeriodeFromDate($request->tanggal_lulus);

        $skpi->update(array_merge(
            $request->only($allowed),
            ['periode_wisuda' => $periode_wisuda]
        ));

        // Setelah update, langsung ke dashboard
        return redirect()->route('dashboard')->with('success', 'Data SKPI berhasil diperbarui.');
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

        return redirect()->route('dashboard')->with('success', 'Data SKPI berhasil disubmit untuk review.');
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

    private function tentukanGelar(string $programStudi = null): string
    {
        $ps = strtolower(trim($programStudi ?? ''));
        if (in_array($ps, ['informatika', 'sistem informasi'])) {
            return 'S.Kom';
        }
        if (in_array($ps, ['teknik sipil', 'teknik mesin', 'teknik elektro'])) {
            return 'S.T';
        }
        if ($ps === 'arsitektur') {
            return 'S.Ars';
        }
        // default fallback (kalau belum dikenali)
        return 'Sarjana';
    }
}
