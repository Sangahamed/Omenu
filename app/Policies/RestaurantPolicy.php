<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RestaurantPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasRole(['super-admin', 'admin']);
    }

    public function view(User $user, Restaurant $restaurant)
    {
        return $user->hasRole(['super-admin', 'admin']) || $user->id === $restaurant->user_id;
    }

    public function create(User $user)
    {
        return $user->hasRole(['super-admin', 'admin', 'restaurant']);
    }

    public function update(User $user, Restaurant $restaurant)
    {
        return $user->hasRole(['super-admin', 'admin']) || $user->id === $restaurant->user_id;
    }

    public function delete(User $user, Restaurant $restaurant)
    {
        return $user->hasRole(['super-admin', 'admin']);
    }
}
