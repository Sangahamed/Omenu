<?php

use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Canaux privés du temps réel
|--------------------------------------------------------------------------
|
| OrderPlaced diffuse sur restaurant.{id}, OrderStatusUpdated et
| DeliveryLocationUpdated sur order.{id}. Sans ces autorisations, le point
| d'entrée /broadcasting/auth refuse chaque abonnement : les toasts « nouvelle
| commande » et le suivi GPS ne recevaient jamais rien, quelle que soit la
| configuration de Reverb.
|
*/

Broadcast::channel('restaurant.{restaurantId}', function ($user, $restaurantId) {
    // Le restaurateur propriétaire, et les administrateurs pour le support.
    return Restaurant::where('id', $restaurantId)
        ->where('user_id', $user->id)
        ->exists()
        || $user->hasRole(['super-admin', 'admin']);
});

Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    $order = Order::find($orderId);

    if (! $order) {
        return false;
    }

    // Le client qui a commandé, le restaurateur concerné, le livreur assigné
    // et les administrateurs.
    return (int) $order->user_id === (int) $user->id
        || (int) $order->delivery_person_id === (int) $user->id
        || (int) ($order->restaurant?->user_id) === (int) $user->id
        || $user->hasRole(['super-admin', 'admin']);
});
