<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStaff() || $user->isMember();
    }

    public function view(User $user, Loan $loan): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        return (int) $loan->user_id === (int) $user->getKey();
    }

    public function create(User $user): bool
    {
        return $user->isMember() || $user->canCirculate();
    }

    public function returnBook(User $user, Loan $loan): bool
    {
        return $user->canCirculate();
    }

    public function extend(User $user, Loan $loan): bool
    {
        if (! $user->isMember()) {
            return false;
        }

        if ((int) $loan->user_id !== (int) $user->getKey()) {
            return false;
        }

        if ($loan->tanggal_dikembalikan !== null) {
            return false;
        }

        return true;
    }

    public function delete(User $user, Loan $loan): bool
    {
        return $user->isAdmin();
    }
}
