<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function create(array $userData): User
    {
        return User::create($userData);
    }
}
