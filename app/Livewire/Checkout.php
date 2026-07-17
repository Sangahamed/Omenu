<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Component;
use Stripe\Stripe;
use Livewire\Attributes\Layout;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Support\Facades\Auth;

#[Layout('components.front.layouts.front')]
class Checkout extends Component
{
    public $cart = [];
    public $total = 0;
    public $subtotal = 0;
    public $delivery_fee = 0;
    public $tax = 0;
    public $restaurant_id;

    public $customer_name;
    public $customer_phone;
    public $delivery_address;
    public $delivery_instructions;
    public $payment_method = 'stripe';

    protected $rules = [
        'customer_name' => 'required|string|max:255',
        'customer_phone' => 'required|string|max:20',
        'delivery_address' => 'required|string|max:500',
        'delivery_instructions' => 'nullable|string|max:500',
        'payment_method' => 'required|in:stripe,orange_money,wave',
    ];

    public function mount()
    {
        $this->loadCart();
        
        if (Auth::check()) {
            $user = Auth::user();
            $this->customer_name = $user->name;
            $this->customer_phone = $user->phone ?? '';
            $this->delivery_address = $user->address ?? '';
        }
    }

    public function loadCart()
    {
        $this->cart = session()->get('cart', []);
        $this->calculateTotals();
        
        if (empty($this->cart)) {
            return redirect()->route('home')->with('error', 'Votre panier est vide');
        }
    }

    protected function calculateTotals()
    {
        $this->subtotal = 0;
        foreach ($this->cart as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }
        
        $this->delivery_fee = $this->subtotal > 5000 ? 0 : 1000;
        $this->tax = $this->subtotal * 0.18; // TVA 18%
        $this->total = $this->subtotal + $this->delivery_fee + $this->tax;
        
        // Récupérer le restaurant_id depuis le premier item
        if (!empty($this->cart)) {
            $firstItem = reset($this->cart);
            $menu = \App\Models\Menu::find($firstItem['id']);
            $this->restaurant_id = $menu?->restaurant_id;
        }
    }

    public function placeOrder()
{
    $this->validate();

    if (empty($this->cart)) {
        return $this->addError('cart', 'Votre panier est vide');
    }

    // 1. Grouper les articles du panier par ID de restaurant
    $groupedCart = [];
    foreach ($this->cart as $item) {
        $menu = Menu::find($item['id']);
        $restaurantId = $menu?->restaurant_id;
        $groupedCart[$restaurantId][] = $item;
    }

    $isMultiVendor = count($groupedCart) > 1;

    // 2. Créer la Commande Principale (Parent)
    $parentOrder = Order::create([
        'user_id' => Auth::id(),
        'restaurant_id' => !$isMultiVendor ? array_key_first($groupedCart) : null, // null si multi-restaurant
        'subtotal' => $this->subtotal,
        'delivery_fee' => $this->delivery_fee,
        'tax' => $this->tax,
        'discount' => 0,
        'total' => $this->total,
        'status' => 'pending',
        'payment_method' => $this->payment_method,
        'payment_status' => 'unpaid',
        'delivery_address' => $this->delivery_address,
        'delivery_instructions' => $this->delivery_instructions,
        'customer_name' => $this->customer_name,
        'customer_phone' => $this->customer_phone,
    ]);

    // 3. Si multi-restaurant, on crée des sous-commandes pour chaque établissement
    foreach ($groupedCart as $restaurantId => $items) {
        
        $subtotalSub = 0;
        foreach ($items as $item) {
            $subtotalSub += $item['price'] * $item['quantity'];
        }

        // Si multi-vendeur, on crée un ticket de commande par restaurant
        $currentOrder = $isMultiVendor ? Order::create([
            'parent_id' => $parentOrder->id,
            'user_id' => Auth::id(),
            'restaurant_id' => $restaurantId,
            'subtotal' => $subtotalSub,
            'delivery_fee' => 0, // Les frais de port globaux restent sur la commande parente
            'tax' => $subtotalSub * 0.18,
            'total' => $subtotalSub * 1.18,
            'status' => 'pending',
            'payment_method' => $this->payment_method,
            'payment_status' => 'unpaid',
            'delivery_address' => $this->delivery_address,
            'delivery_instructions' => $this->delivery_instructions,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
        ]) : $parentOrder;

        // 4. Associer les plats à la commande correspondante
        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $currentOrder->id,
                'menu_id' => $item['id'],
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
                'options' => $item['options'] ?? [],
            ]);
        }
    }

    // 5. Redirection vers le paiement Stripe de la commande parente (globale)
    if ($this->payment_method === 'stripe') {
        return $this->initiateStripePayment($parentOrder);
    }

    session()->forget('cart');
    return redirect()->route('order.confirmation', $parentOrder->id)
        ->with('success', 'Votre commande multi-restaurant a été enregistrée avec succès !');
}

    protected function initiateStripePayment($order)
    {
        Stripe::setApiKey(config('stripe.secret_key'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'xof',
                        'product_data' => [
                            'name' => 'Commande #' . $order->id,
                        ],
                        'unit_amount' => (int)($this->total * 100),
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => route('order.stripe.success', ['order' => $order->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout'),
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        $order->update(['payment_id' => $session->id]);

        return redirect($session->url);
    }

    public function render()
    {
        return view('livewire.checkout');
    }
}