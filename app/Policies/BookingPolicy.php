<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin()
            || $booking->tenant_id === $user->id
            || $booking->owner_id === $user->id;
    }

    public function update(User $user, Booking $booking): bool
    {
        return $this->view($user, $booking);
    }

    public function manageAsOwner(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || ($user->isOwner() && $booking->owner_id === $user->id);
    }
}
