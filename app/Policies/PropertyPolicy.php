<?php

namespace App\Policies;

use App\Enums\PropertyStatus;
use App\Models\Property;
use App\Models\User;

class PropertyPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Property $property): bool
    {
        if ($property->status === PropertyStatus::Approved) {
            return true;
        }

        if (! $user) {
            return false;
        }

        return $user->isAdmin() || $property->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isOwner() && $user->is_active;
    }

    public function update(User $user, Property $property): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isOwner() && $property->owner_id === $user->id;
    }

    public function delete(User $user, Property $property): bool
    {
        return $this->update($user, $property);
    }

    public function verify(User $user, Property $property): bool
    {
        return $user->isAdmin();
    }
}
