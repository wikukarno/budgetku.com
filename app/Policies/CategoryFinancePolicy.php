<?php

namespace App\Policies;

use App\Models\CategoryFinance;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CategoryFinancePolicy
{
    use HandlesAuthorization;

    public function view(User $user, CategoryFinance $categoryFinance)
    {
        // Periksa apakah pengguna adalah pemilik category finance
        return $user->id === $categoryFinance->users_id
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses untuk melihat data ini');
    }

    public function updateOrCreate(User $user, CategoryFinance $categoryFinance = null)
    {
        // Periksa apakah pengguna adalah pemilik category finance untuk update
        return $user->id === $categoryFinance->users_id
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses untuk mengubah data ini');
    }

    public function delete(User $user, CategoryFinance $categoryFinance = null)
    {
        // Periksa apakah pengguna adalah pemilik category finance untuk delete
        return $user->id === $categoryFinance->users_id
            ? Response::allow()
            : Response::deny('Anda tidak memiliki akses untuk menghapus data ini');
    }
}
