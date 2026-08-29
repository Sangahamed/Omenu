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
        // Tout utilisateur connecté peut soumettre son restaurant : c'est
        // précisément ce qui lui fait obtenir le rôle "restaurant" (voir
        // RestaurantController::store). Le restreindre aux détenteurs du
        // rôle créerait une impasse : impossible de le devenir.
        return true;
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
