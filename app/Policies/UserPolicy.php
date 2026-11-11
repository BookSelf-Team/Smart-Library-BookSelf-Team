<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageUsers();
    }

    public function view(User $user, User $model): bool
    {
        return $user->canManageUsers() || $user->is($model);
    }

    public function create(User $user): bool
    {
        return $user->canManageUsers();
    }

    public function update(User $user, User $model): bool
    {
        if (! $user->canManageUsers()) {
            return false;
        }

        if ($user->is($model)) {
            if ($model->roleEnum() === Role::Admin && $this->isLastAdmin($model)) {
                return false;
            }

            return true;
        }

        if ($model->roleEnum() === Role::Admin && $this->isLastAdmin($model)) {
            return false;
        }

        return true;
    }

    public function delete(User $user, User $model): bool
    {
        if (! $user->canManageUsers()) {
            return false;
        }

        if ($user->is($model)) {
            return false;
        }

        if ($model->roleEnum() === Role::Admin && $this->isLastAdmin($model)) {
            return false;
        }

        return true;
    }

    private function isLastAdmin(User $user): bool
    {
        return ! User::query()
            ->where('role', 'admin')
            ->whereKeyNot($user->getKey())
            ->exists();
    }
}
