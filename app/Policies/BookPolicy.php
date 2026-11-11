<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;

class BookPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, Book $book): bool
    {
        return $user->isStaff() || $user->isMember();
    }

    public function create(User $user): bool
    {
        return $user->canManageBooks();
    }

    public function update(User $user, Book $book): bool
    {
        return $user->canManageBooks();
    }

    public function delete(User $user, Book $book): bool
    {
        return $user->canManageBooks();
    }
}
