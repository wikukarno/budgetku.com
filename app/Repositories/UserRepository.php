<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function update($id, array $data)
    {
        return User::findOrFail($id)->update($data);
    }

    public function find($id)
    {
        return User::findOrFail($id);
    }

    public function create(array $attributes): User
    {
        return User::create($attributes);
    }
}
