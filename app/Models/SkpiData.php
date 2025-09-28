<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SkpiData extends Model
{
    use HasFactory;

    // Pastikan nama tabel sesuai migrasi
    protected $table = 'skpi_data';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'npm',                 // pakai npm (bukan nim)
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
        'status',
        'catatan_reviewer',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_lulus' => 'date',
        'reviewed_at'   => 'datetime',
        'approved_at'   => 'datetime',
        'ipk'           => 'decimal:2',
    ];

    /**
     * Relasi ke pemilik data (User).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Jurusan.
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    /**
     * Relasi ke dokumen-dokumen (untuk fitur upload).
     * Ini yang sebelumnya hilang/undefined dan menyebabkan error.
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'skpi_data_id');
    }

    /**
     * User yang me-review.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * User yang meng-approve.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Business rule: bisa diedit saat draft/rejected (silakan sesuaikan kebijakanmu).
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'rejected'], true);
    }

    /**
     * Business rule: bisa disubmit ketika draft atau setelah perbaikan (rejected).
     */
    public function canBeSubmitted(): bool
    {
        return in_array($this->status, ['draft', 'rejected'], true);
    }
}
