<?php

namespace App\Notifications;

use App\Models\Order;

/**
 * Previent le restaurateur qu'une commande vient d'arriver.
 */
class NewOrderReceived extends AppNotification
{
    public function __construct(public Order $order)
    {
    }

    public function payload(object $notifiable): array
    {
        return [
            'title' => 'Nouvelle commande #'.$this->order->id,
            'body' => $this->order->customer_name.' — '.number_format((float) $this->order->total, 0, ',', ' ').' FCFA',
            'url' => route('restaurant.dashboard'),
            'icon' => 'ri-shopping-bag-3-line',
            'level' => 'success',
            'order_id' => $this->order->id,
        ];
    }
}
