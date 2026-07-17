<?php

namespace App\Livewire;

use App\Models\Menu;
use Livewire\Component;

class Cart extends Component
{
    public $cart = [];
    public $total = 0;
    public $itemCount = 0;

    protected $listeners = [
        'cartUpdated' => 'loadCart', 
        'addToCart' => 'addItem'
    ];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = session()->get('cart', []);
        $this->calculateTotals();
    }

    public function addItem($menuId)
    {
        $menu = Menu::with('restaurant')->findOrFail($menuId);
        $cart = session()->get('cart', []);

        // Éviter le mélange de restaurants dans un même panier (Sécurité Multi-Vendor)
        if (!empty($cart)) {
            $firstItem = reset($cart);
            $firstMenu = Menu::find($firstItem['id']);
            
            if ($firstMenu && $firstMenu->restaurant_id !== $menu->restaurant_id) {
                // Émettre un événement d'alerte à l'utilisateur (géré en JS ou via toast)
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => "Vous ne pouvez commander que dans un seul restaurant à la fois. Videz votre panier d'abord !"
                ]);
                return;
            }
        }

        if (isset($cart[$menuId])) {
            $cart[$menuId]['quantity']++;
        } else {
            $cart[$menuId] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'quantity' => 1,
                'image' => $menu->image,
                'restaurant_name' => $menu->restaurant->name,
                'options' => [],
            ];
        }

        session()->put('cart', $cart);
        $this->loadCart();
        
        $this->dispatch('cartUpdated', cart: $this->cart, total: $this->total);
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Plat raffiné ajouté au panier !'
        ]);
    }

    public function removeItem($menuId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$menuId]);
        session()->put('cart', $cart);
        $this->loadCart();
        $this->dispatch('cartUpdated', cart: $this->cart, total: $this->total);
    }

    public function updateQuantity($menuId, $quantity)
    {
        $cart = session()->get('cart', []);
        if ($quantity <= 0) {
            unset($cart[$menuId]);
        } else {
            $cart[$menuId]['quantity'] = $quantity;
        }
        session()->put('cart', $cart);
        $this->loadCart();
        $this->dispatch('cartUpdated', cart: $this->cart, total: $this->total);
    }

    public function clearCart()
    {
        session()->forget('cart');
        $this->loadCart();
        $this->dispatch('cartUpdated', cart: $this->cart, total: $this->total);
    }

    protected function calculateTotals()
    {
        $this->total = 0;
        $this->itemCount = 0;

        foreach ($this->cart as $item) {
            $this->total += $item['price'] * $item['quantity'];
            $this->itemCount += $item['quantity'];
        }
    }

    public function render()
    {
        return view('livewire.cart');
    }
}