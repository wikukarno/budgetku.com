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
        return $user->uuid === $categoryIncome->users_uuid
            ? Response::allow()
            : Response::deny('You do not own this category income');
    }

    public function updateOrCreate(User $user, CategoryIncome $categoryIncome)
    {
        // Periksa apakah pengguna adalah pemilik category finance untuk update
        return $user->uuid === $categoryIncome->users_uuid
            ? Response::allow()
            : Response::deny('You do not have access to update this data');
    }

    public function delete(User $user, CategoryIncome $categoryIncome)
    {
        // Periksa apakah pengguna adalah pemilik category finance untuk delete
        return $user->uuid === $categoryIncome->users_uuid
            ? Response::allow()
            : Response::deny('You do not have access to delete this data');
    }
}
