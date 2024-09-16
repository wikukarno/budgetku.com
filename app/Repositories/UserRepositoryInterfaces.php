<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function find($id);
    public function create(array $attributes): User;
}
