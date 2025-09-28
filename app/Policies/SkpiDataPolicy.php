<?php

namespace App\Policies;

use App\Models\SkpiData;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SkpiDataPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SkpiData $skpiData): bool
    {
        // Mahasiswa hanya bisa melihat data mereka sendiri
        if ($user->role === 'user') {
            return $user->id === $skpiData->user_id;
        }
        
        // Admin hanya bisa melihat data dari jurusan mereka
        if ($user->role === 'admin') {
            return $user->jurusan_id === $skpiData->jurusan_id;
        }
        
        // SuperAdmin bisa melihat semua data
        if ($user->role === 'superadmin') {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'user';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SkpiData $skpiData): bool
    {
        // Hanya mahasiswa yang bisa update data mereka sendiri
        if ($user->role === 'user') {
            return $user->id === $skpiData->user_id && $skpiData->canBeEdited();
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SkpiData $skpiData): bool
    {
        // Hanya mahasiswa yang bisa delete data mereka sendiri dan hanya jika masih draft
        if ($user->role === 'user') {
            return $user->id === $skpiData->user_id && $skpiData->status === 'draft';
        }
        
        // SuperAdmin bisa delete
        if ($user->role === 'superadmin') {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SkpiData $skpiData): bool
    {
        return $user->role === 'superadmin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SkpiData $skpiData): bool
    {
        return $user->role === 'superadmin';
    }

    /**
     * Determine whether the user can review the model.
     */
    public function review(User $user, SkpiData $skpiData): bool
    {
        // Admin hanya bisa review data dari jurusan mereka
        if ($user->role === 'admin') {
            return $user->jurusan_id === $skpiData->jurusan_id && $skpiData->canBeApproved();
        }
        
        // SuperAdmin bisa review semua data
        if ($user->role === 'superadmin') {
            return $skpiData->canBeApproved();
        }
        
        return false;
    }
}