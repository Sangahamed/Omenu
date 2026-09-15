<?php

namespace App\Livewire;

use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Cart extends Component
{
    /**
     * Panier affiché par le composant.
     */
    public array $cart = [];

    /**
     * Montant total du panier.
     */
    public float $total = 0;

    /**
     * Nombre total d'articles.
     */
    public int $itemCount = 0;

    /**
     * Restaurant actuellement présent dans le panier.
     */
    public ?int $restaurantId = null;

    /**
     * Nom du restaurant actuellement présent dans le panier.
     */
    public ?string $restaurantName = null;

    /**
     * Initialisation du composant.
     */
    public function mount(): void
    {
        $this->loadCart();
    }

    /**
     * Écoute l'événement addToCart envoyé par les autres composants Livewire.
     */
    #[On('addToCart')]
    public function addItem($menuId): void
    {
        $menu = Menu::with('restaurant')->find($menuId);

        if (!$menu) {
            $this->notify(
                'error',
                'Le plat demandé est introuvable.'
            );

            return;
        }

        /*
         * -------------------------------------------------------------
         * Vérification du restaurant
         * -------------------------------------------------------------
         *
         * Un panier ne peut contenir que des plats provenant
         * du même restaurant.
         */
        if (!$this->canAddFromRestaurant($menu)) {
            $this->notify(
                'error',
                'Vous ne pouvez commander que dans un seul restaurant à la fois. '
                . 'Videz votre panier avant de commander dans un autre restaurant.'
            );

            return;
        }

        /*
         * -------------------------------------------------------------
         * AJOUT AU PANIER (SESSION)
         * -------------------------------------------------------------
         */
        $this->addToSession($menu);

        $this->loadCart();

        $this->dispatch(
            'cartUpdated',
            cart: $this->cart,
            total: $this->total,
            itemCount: $this->itemCount
        );

        $this->notify(
            'success',
            "{$menu->name} ajouté au panier !"
        );
    }

    /**
     * Ajoute un article au panier session.
     */
    protected function addToSession(Menu $menu): void
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$menu->id])) {
            $cart[$menu->id]['quantity']++;
        } else {
            $cart[$menu->id] = [
                'id' => $menu->id,
                'menu_id' => $menu->id,
                'name' => $menu->name,
                'price' => (float) $menu->price,
                'image' => $menu->image,
                'quantity' => 1,
                'restaurant_id' => $menu->restaurant_id,
                'restaurant_name' => $menu->restaurant?->name ?? 'Établissement',
                'options' => [],
            ];
        }

        session()->put('cart', $cart);
    }    /**
     * Vérifie qu'un plat appartient au même restaurant
     * que les articles déjà présents dans le panier.
     */
    protected function canAddFromRestaurant(Menu $menu): bool
    {
        /*
         * Panier vide : tout restaurant est autorisé.
         */
        if ($this->itemCount === 0) {
            return true;
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return true;
        }

        $firstItem = reset($cart);

        if (!empty($firstItem['restaurant_id'])) {
            return (int) $firstItem['restaurant_id']
                === (int) $menu->restaurant_id;
        }

        if (!empty($firstItem['id'])) {
            $firstMenu = Menu::find($firstItem['id']);

            if (!$firstMenu) {
                return true;
            }

            return (int) $firstMenu->restaurant_id
                === (int) $menu->restaurant_id;
        }

        return true;
    }

    /**
     * Supprime complètement un article du panier.
     */
    public function removeItem($menuId): void
    {
        $cart = session()->get('cart', []);
        unset($cart[$menuId]);
        session()->put('cart', $cart);

        $this->refreshCart();
    }

    /**
     * Met à jour la quantité d'un article.
     */
    public function updateQuantity($menuId, $quantity): void
    {
        $quantity = (int) $quantity;

        if ($quantity <= 0) {
            $this->removeItem($menuId);
            return;
        }

        $quantity = min($quantity, 99);

        $cart = session()->get('cart', []);

        if (!isset($cart[$menuId])) {
            $this->loadCart();
            return;
        }

        $cart[$menuId]['quantity'] = $quantity;
        session()->put('cart', $cart);

        $this->refreshCart();
    }

    /**
     * Vide complètement le panier.
     */
    public function clearCart(): void
    {
        session()->forget('cart');

        $this->loadCart();

        $this->dispatch(
            'cartUpdated',
            cart: $this->cart,
            total: $this->total,
            itemCount: $this->itemCount
        );

        $this->notify(
            'success',
            'Votre panier a été vidé.'
        );
    }

    /**
     * Recharge le panier depuis la session.
     */
    #[On('cartUpdated')]
    public function loadCart(): void
    {
        $this->loadSessionCart();
        $this->calculateTotals();
        $this->updateRestaurantInformation();
    }

    /**
     * Charge le panier depuis la session.
     */
    protected function loadSessionCart(): void
    {
        $this->cart = session()->get('cart', []);

        /*
         * Normalisation des anciennes structures de panier.
         */
        foreach ($this->cart as $menuId => &$item) {
            $item['id'] = $item['id'] ?? $item['menu_id'] ?? $menuId;
            $item['menu_id'] = $item['menu_id'] ?? $item['id'];
            $item['quantity'] = max(1, (int) ($item['quantity'] ?? 1));
            $item['price'] = (float) ($item['price'] ?? 0);
            $item['options'] = $item['options'] ?? [];
        }

        unset($item);

        session()->put('cart', $this->cart);
    }

    /**
     * Recalcule le nombre d'articles et le total.
     */
    protected function calculateTotals(): void
    {
        $this->total = 0;
        $this->itemCount = 0;

        foreach ($this->cart as $item) {
            $quantity = max(0, (int) ($item['quantity'] ?? 0));
            $price = (float) ($item['price'] ?? 0);

            $this->itemCount += $quantity;
            $this->total += $price * $quantity;
        }

        /*
         * Normalisation monétaire à deux décimales.
         */
        $this->total = round($this->total, 2);
    }

    /**
     * Met à jour les informations du restaurant.
     */
    protected function updateRestaurantInformation(): void
    {
        $this->restaurantId = null;
        $this->restaurantName = null;

        if (empty($this->cart)) {
            return;
        }

        $firstItem = reset($this->cart);

        $this->restaurantId = isset($firstItem['restaurant_id'])
            ? (int) $firstItem['restaurant_id']
            : null;

        $this->restaurantName = $firstItem['restaurant_name'] ?? null;
    }

    /**
     * Recharge le panier et informe les autres composants.
     */
    protected function refreshCart(): void
    {
        $this->loadCart();

        $this->dispatch(
            'cartUpdated',
            cart: $this->cart,
            total: $this->total,
            itemCount: $this->itemCount
        );
    }

    /**
     * Notification frontend.
     */
    protected function notify(string $type, string $message): void
    {
        $this->dispatch(
            'notify',
            type: $type,
            message: $message
        );
    }

    /**
     * Rendu du composant.
     */
    public function render()
    {
        return view('livewire.cart');
    }
}
