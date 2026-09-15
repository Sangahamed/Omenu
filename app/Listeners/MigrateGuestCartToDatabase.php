<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class MigrateGuestCartToDatabase
{
    /**
     * Gère la migration du panier invité après connexion.
     *
     * Le panier est géré en session — il persiste naturellement après
     * l'authentification. Ce listener garantit simplement que la session
     * est bien conservée et que les données du panier restent cohérentes.
     */
    public function handle(Login $event): void
    {
        // Le panier est stocké en session et persiste automatiquement
        // après la connexion. Aucune migration vers la base de données
        // n'est nécessaire puisque l'application utilise un panier session.
        $cart = session()->get('cart', []);

        if (!empty($cart)) {
            // Régénère la session pour éviter les fixations de session
            // tout en préservant les données du panier.
            session()->regenerate(true);
            session()->put('cart', $cart);
        }
    }
}