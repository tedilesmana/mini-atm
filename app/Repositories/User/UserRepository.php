<?php

namespace App\Repositories\User;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function find($id)
    {
        return User::findOrFail($id);
    }

    public function updateBalance($id, $amount)
    {
        $user = User::findOrFail($id);
        $user->balance += $amount;
        $user->save();

        return $user;
    }
}
