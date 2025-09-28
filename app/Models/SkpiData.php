<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkpiData extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'npm', // <- perbaiki: sebelumnya 'nim'
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
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'ipk' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    public function canBeSubmitted()
    {
        return $this->status === 'draft';
    }

    public function canBeApproved()
    {
        return $this->status === 'submitted';
    }
}
