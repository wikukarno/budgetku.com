<?php

namespace App\Policies;

use App\Models\CategoryIncome;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CategoryIncomePolicy
{
    use HandlesAuthorization;

    public function view(User $user, CategoryIncome $categoryIncome)
    {
        // Periksa apakah pengguna adalah pemilik category finance
        return $user->id === $categoryIncome->users_id
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses untuk melihat data ini');
    }

    public function updateOrCreate(User $user, CategoryIncome $categoryIncome = null)
    {
        // Periksa apakah pengguna adalah pemilik category finance untuk update
        return $user->id === $categoryIncome->users_id
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses untuk mengubah data ini');
    }

    public function delete(User $user, CategoryIncome $categoryIncome = null)
    {
        // Periksa apakah pengguna adalah pemilik category finance untuk delete
        return $user->id === $categoryIncome->users_id
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses untuk menghapus data ini');
    }
}
