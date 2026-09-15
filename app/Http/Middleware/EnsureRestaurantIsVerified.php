<?php

namespace App\Http\Middleware;

use App\Models\Restaurant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Empeche l'acces a l'espace restaurateur tant que la fiche n'est pas validee.
 *
 * Le role « restaurant » est attribue des la soumission du formulaire ; sans
 * ce garde-fou, l'utilisateur atterrissait sur un tableau de bord inutilisable
 * alors qu'il attend encore la confirmation d'un administrateur.
 */
class EnsureRestaurantIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $restaurant = Restaurant::where('user_id', $request->user()?->id)->latest()->first();

        if (! $restaurant) {
            return redirect()->route('restaurants.create')
                ->with('error', 'Enregistrez d\'abord votre établissement.');
        }

        if (! $restaurant->is_verified) {
            return redirect()->route('restaurant.pending');
        }

        return $next($request);
    }
}
