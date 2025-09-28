<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'npm',
        'nip',
        'role',
        'status',
        'jurusan_id',
        'phone',
        'address',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the jurusan that owns the user.
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    /**
     * Get the skpi data for the user.
     */
    public function skpiData()
    {
        return $this->hasMany(SkpiData::class);
    }

    /**
     * Get the reviewed skpi data by this user.
     */
    public function reviewedSkpiData()
    {
        return $this->hasMany(SkpiData::class, 'reviewed_by');
    }

    /**
     * Get the approved skpi data by this user.
     */
    public function approvedSkpiData()
    {
        return $this->hasMany(SkpiData::class, 'approved_by');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is superadmin
     */
    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if user is regular user (mahasiswa)
     */
    public function isUser()
    {
        return $this->role === 'user';
    }

    /**
     * Check if user can verify documents
     */
    public function canVerify()
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }
}