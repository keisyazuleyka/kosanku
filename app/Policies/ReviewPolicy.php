<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function moderate(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->isAdmin();
    }
}
