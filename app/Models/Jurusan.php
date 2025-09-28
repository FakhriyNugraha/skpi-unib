<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_jurusan',
        'kode_jurusan',
        'deskripsi',
        'kaprodi',
        'nip_kaprodi',
        'status',
    ];

    /**
     * Get the users for the jurusan.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the skpi data for the jurusan.
     */
    public function skpiData()
    {
        return $this->hasMany(SkpiData::class);
    }

    /**
     * Scope a query to only include active jurusan.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}