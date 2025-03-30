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
            : Response::deny('You do not own this category finance');
    }

    public function updateOrCreate(User $user, CategoryFinance $categoryFinance)
    {
        // Periksa apakah pengguna adalah pemilik category finance untuk update
        return $user->id === $categoryFinance->users_id
            ? Response::allow()
            : Response::deny('You do not have access to update this data');
    }

    public function delete(User $user, CategoryFinance $categoryFinance)
    {
        // Periksa apakah pengguna adalah pemilik category finance untuk delete
        return $user->id === $categoryFinance->users_id
            ? Response::allow()
            : Response::deny('You do not have access to delete this data');
    }
}
