<?php

namespace App\Policies;

use App\Models\Fine;
use App\Models\User;

class FinePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, Fine $fine): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        $fine->load('loan');

        return (int) $fine->loan->user_id === (int) $user->getKey();
    }

    public function create(User $user): bool
    {
        return $user->canCirculate();
    }

    public function update(User $user, Fine $fine): bool
    {
        return $user->canCirculate();
    }

    public function delete(User $user, Fine $fine): bool
    {
        return $user->isAdmin();
    }
}
