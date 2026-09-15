<?php

namespace App\Livewire;

use App\Models\Favorite;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Bouton « ajouter aux favoris » d'une fiche restaurant.
 *
 * La table `favorites` et le drapeau `is_favorited` existaient déjà, mais
 * aucune interface ne permettait d'en créer : le compteur du tableau de bord
 * ne pouvait afficher que zéro.
 */
class FavoriteToggle extends Component
{
    public int $restaurantId;

    /** Affichage compact (icône seule) ou avec le libellé. */
    public bool $compact = false;

    public bool $isFavorite = false;

    public function mount(int $restaurantId, bool $compact = false): void
    {
        $this->restaurantId = $restaurantId;
        $this->compact = $compact;
        $this->isFavorite = $this->currentlyFavorite();
    }

    public function toggle(): void
    {
        if (! Auth::check()) {
            $this->redirectRoute('login');

            return;
        }

        $restaurant = Restaurant::find($this->restaurantId);

        if (! $restaurant) {
            return;
        }

        if ($this->currentlyFavorite()) {
            Favorite::where('user_id', Auth::id())
                ->where('restaurant_id', $this->restaurantId)
                ->delete();

            $this->isFavorite = false;
            $message = "{$restaurant->name} retiré de vos favoris.";
        } else {
            Favorite::firstOrCreate([
                'user_id' => Auth::id(),
                'restaurant_id' => $this->restaurantId,
            ]);

            $this->isFavorite = true;
            $message = "{$restaurant->name} ajouté à vos favoris.";
        }

        $this->dispatch('favoritesUpdated');
        $this->dispatch('notify', type: 'success', message: $message);
    }

    protected function currentlyFavorite(): bool
    {
        return Auth::check()
            && Favorite::where('user_id', Auth::id())
                ->where('restaurant_id', $this->restaurantId)
                ->exists();
    }

    public function render()
    {
        return view('livewire.favorite-toggle');
    }
}
