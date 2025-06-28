<?php

namespace App\Repositories;

use App\Models\User;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function find($id)
    {
        return User::find($id);
    }

    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    public function update($id, array $data)
    {
        return User::findOrFail($id)->update($data);
    }
}