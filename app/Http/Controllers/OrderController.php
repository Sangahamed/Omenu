<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Affiche la page de confirmation d'une commande.
     * GET /order/confirmation/{order}
     */
    public function confirmation(Order $order)
    {
        $this->authorizeOwner($order);

        $order->load('restaurant', 'items');

        return view('order.confirmation', compact('order'));
    }

    /**
     * Retour de Stripe après paiement.
     * GET /order/stripe/success/{order}?session_id=...
     */
    public function stripeSuccess(Request $request, Order $order)
    {
        $this->authorizeOwner($order);

        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect()->route('checkout')
                ->with('error', 'Paiement introuvable, merci de réessayer.');
        }

        Stripe::setApiKey(config('stripe.secret_key'));

        try {
            $session = StripeSession::retrieve($sessionId);
        } catch (\Exception $e) {
            return redirect()->route('checkout')
                ->with('error', 'Impossible de vérifier votre paiement. Merci de réessayer.');
        }

        // Vérifie que la session appartient bien à cette commande et que le paiement est validé,
        // pour éviter qu'un client ne marque une commande comme payée en devinant l'URL.
        $sessionOrderId = $session->metadata->order_id ?? null;

        if ((string) $sessionOrderId !== (string) $order->id || $session->payment_status !== 'paid') {
            return redirect()->route('checkout')
                ->with('error', 'Le paiement n\'a pas pu être confirmé.');
        }

        if ($order->payment_status !== 'paid') {
            $order->update([
                'payment_status' => 'paid',
                'payment_id' => $session->id,
                'status' => $order->status === 'pending' ? 'accepted' : $order->status,
            ]);
        }

        session()->forget('cart');

        return redirect()->route('order.confirmation', $order->id)
            ->with('success', 'Paiement confirmé, votre commande est en cours de préparation !');
    }

    /**
     * Point d'entrée de secours pour créer une commande sans passer par le
     * composant Livewire Checkout (ex. formulaire classique / intégration externe).
     * POST /orders
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string|max:500',
            'delivery_instructions' => 'nullable|string|max:500',
            'payment_method' => 'required|in:stripe,orange_money,wave',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|integer|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1|max:99',
        ]);

        $menus = Menu::whereIn('id', collect($validated['items'])->pluck('menu_id'))
            ->get()
            ->keyBy('id');

        // Un seul restaurant par commande sur ce point d'entrée simplifié.
        $restaurantIds = $menus->pluck('restaurant_id')->unique();
        if ($restaurantIds->count() > 1) {
            return back()->withErrors([
                'items' => 'Cette commande contient des plats de plusieurs restaurants, ce qui n\'est pas pris en charge ici.',
            ]);
        }

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $menu = $menus->get($item['menu_id']);
            if (! $menu || ! $menu->is_available) {
                return back()->withErrors(['items' => 'Un des plats sélectionnés n\'est plus disponible.']);
            }
            $subtotal += $menu->price * $item['quantity'];
        }

        $deliveryFee = $subtotal > 5000 ? 0 : 1000;
        $tax = $subtotal * 0.18;
        $total = $subtotal + $deliveryFee + $tax;

        $order = Order::create([
            'user_id' => Auth::id(),
            'restaurant_id' => $restaurantIds->first(),
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'tax' => $tax,
            'discount' => 0,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'unpaid',
            'delivery_address' => $validated['delivery_address'],
            'delivery_instructions' => $validated['delivery_instructions'] ?? null,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
        ]);

        foreach ($validated['items'] as $item) {
            $menu = $menus->get($item['menu_id']);
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $menu->id,
                'name' => $menu->name,
                'quantity' => $item['quantity'],
                'unit_price' => $menu->price,
                'subtotal' => $menu->price * $item['quantity'],
            ]);
        }

        event(new \App\Events\OrderPlaced($order));

        if ($validated['payment_method'] === 'stripe') {
            return $this->redirectToStripe($order, $total);
        }

        return redirect()->route('order.confirmation', $order->id)
            ->with('success', 'Votre commande a été enregistrée avec succès !');
    }

    protected function redirectToStripe(Order $order, float $total)
    {
        Stripe::setApiKey(config('stripe.secret_key'));

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'xof',
                    'product_data' => ['name' => 'Commande #' . $order->id],
                    'unit_amount' => (int) ($total * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('order.stripe.success', ['order' => $order->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout'),
            'metadata' => ['order_id' => $order->id],
        ]);

        $order->update(['payment_id' => $session->id]);

        return redirect($session->url);
    }

    /**
     * Empêche un client d'accéder à la commande d'un autre en devinant l'ID dans l'URL.
     */
    protected function authorizeOwner(Order $order): void
    {
        if ($order->user_id !== Auth::id()) {
            abort(Response::HTTP_FORBIDDEN);
        }
    }
}
