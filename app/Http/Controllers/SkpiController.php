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
        'nama_lengkap'          => 'nullable|string|max:255', // akan diisi dari user->name
        'npm'                   => 'required|string|max:20',
        'tempat_lahir'          => 'required|string|max:255',
        'tanggal_lahir'         => 'required|date',
        'nomor_ijazah'          => 'required|string|max:255',
        'tanggal_lulus'         => 'required|date',
        'gelar'                 => 'required|string|max:50',
        'jurusan_id'            => 'required|exists:jurusans,id',
        'ipk'                   => 'required|numeric|min:0|max:4',
        'prestasi_akademik'     => 'nullable|string',
        'prestasi_non_akademik' => 'nullable|string',
        'organisasi'            => 'nullable|string',
        'pengalaman_kerja'      => 'nullable|string',
        'sertifikat_kompetensi' => 'nullable|string',
        'catatan_khusus'        => 'nullable|string',

        // 1 link Google Drive
        'drive_link'            => [
            'required',
            'url',
            'max:2048',
            function ($attr, $value, $fail) {
                // cek https + domain drive.google.com + pola path umum
                $parts = parse_url($value);
                if (!$parts || !isset($parts['scheme'], $parts['host'])) {
                    return $fail('Link Google Drive tidak valid.');
                }
                if (strtolower($parts['scheme']) !== 'https') {
                    return $fail('Link harus menggunakan HTTPS.');
                }
                $host = strtolower($parts['host']);
                if ($host !== 'drive.google.com') {
                    return $fail('Link harus dari domain drive.google.com.');
                }
                $path = $parts['path'] ?? '';
                // pola yang umum: /file/d/{id}/..., /open, /uc, /drive/folders/{id}
                $ok = preg_match('#^/file/d/[^/]+#', $path)
                   || preg_match('#^/drive/folders/[^/]+#', $path)
                   || $path === '/open'
                   || $path === '/uc'
                   || $path === '/drive/u/0/folders' // beberapa variasi UI
                   ;
                if (!$ok) {
                    return $fail('Format path Google Drive tidak dikenali. Gunakan tautan "Bagikan" dari Google Drive.');
                }
            },
        ],
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    $jurusan = \App\Models\Jurusan::findOrFail($request->jurusan_id);

    $allowed = [
        'tempat_lahir',
        'tanggal_lahir',
        'nomor_ijazah',
        'tanggal_lulus',
        'gelar',
        'jurusan_id',
        'ipk',
        'prestasi_akademik',
        'prestasi_non_akademik',
        'organisasi',
        'pengalaman_kerja',
        'sertifikat_kompetensi',
        'catatan_khusus',
        'drive_link', // simpan link drive
    ];

    $payload = $request->only($allowed);
    $payload['user_id'] = $user->id;
    $payload['status']  = 'draft';
    $payload['nama_lengkap'] = $user->name;
    $payload['program_studi'] = $jurusan->nama_jurusan;
    $payload['nim'] = $request->input('npm');

    $skpiData = SkpiData::updateOrCreate(
        ['user_id' => $user->id],
        $payload
    );

    return redirect()->route('skpi.show', $skpiData)->with('success', 'Data SKPI berhasil disimpan sebagai draft.');
}

public function update(Request $request, SkpiData $skpi)
{
    $this->authorize('update', $skpi);

    if (!$skpi->canBeEdited()) {
        return redirect()->route('skpi.index')->with('error', 'Data SKPI tidak dapat diedit.');
    }

    $validator = Validator::make($request->all(), [
        'nama_lengkap'          => 'nullable|string|max:255',
        'npm'                   => 'required|string|max:20',
        'tempat_lahir'          => 'required|string|max:255',
        'tanggal_lahir'         => 'required|date',
        'nomor_ijazah'          => 'required|string|max:255',
        'tanggal_lulus'         => 'required|date',
        'gelar'                 => 'required|string|max:50',
        'jurusan_id'            => 'required|exists:jurusans,id',
        'ipk'                   => 'required|numeric|min:0|max:4',
        'prestasi_akademik'     => 'nullable|string',
        'prestasi_non_akademik' => 'nullable|string',
        'organisasi'            => 'nullable|string',
        'pengalaman_kerja'      => 'nullable|string',
        'sertifikat_kompetensi' => 'nullable|string',
        'catatan_khusus'        => 'nullable|string',
        'drive_link'            => [
            'required',
            'url',
            'max:2048',
            function ($attr, $value, $fail) {
                $parts = parse_url($value);
                if (!$parts || !isset($parts['scheme'], $parts['host'])) {
                    return $fail('Link Google Drive tidak valid.');
                }
                if (strtolower($parts['scheme']) !== 'https') {
                    return $fail('Link harus menggunakan HTTPS.');
                }
                $host = strtolower($parts['host']);
                if ($host !== 'drive.google.com') {
                    return $fail('Link harus dari domain drive.google.com.');
                }
                $path = $parts['path'] ?? '';
                $ok = preg_match('#^/file/d/[^/]+#', $path)
                   || preg_match('#^/drive/folders/[^/]+#', $path)
                   || $path === '/open'
                   || $path === '/uc'
                   || $path === '/drive/u/0/folders';
                if (!$ok) {
                    return $fail('Format path Google Drive tidak dikenali. Gunakan tautan "Bagikan" dari Google Drive.');
                }
            },
        ],
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    $jurusan = \App\Models\Jurusan::findOrFail($request->jurusan_id);

    $allowed = [
        'tempat_lahir',
        'tanggal_lahir',
        'nomor_ijazah',
        'tanggal_lulus',
        'gelar',
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

    $update = $request->only($allowed);
    $update['nama_lengkap'] = Auth::user()->name;
    $update['program_studi'] = $jurusan->nama_jurusan;
    $update['nim'] = $request->input('npm');

    $skpi->update($update);

    return redirect()->route('skpi.show', $skpi)->with('success', 'Data SKPI berhasil diupdate.');
}
}
