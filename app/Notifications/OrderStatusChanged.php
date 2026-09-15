<?php

namespace App\Notifications;

use App\Models\Order;

/**
 * Previent le client de l'avancement de sa commande.
 */
class OrderStatusChanged extends AppNotification
{
    public function __construct(public Order $order)
    {
    }

    public function payload(object $notifiable): array
    {
        return [
            'title' => 'Commande #'.$this->order->id.' : '.$this->order->status_label,
            'body' => $this->messageForStatus(),
            'url' => route('order.tracking', $this->order->id),
            'icon' => $this->order->status === 'cancelled' ? 'ri-close-circle-line' : 'ri-truck-line',
            'level' => $this->order->status === 'cancelled' ? 'error' : 'info',
            'order_id' => $this->order->id,
            'status' => $this->order->status,
        ];
    }

    protected function messageForStatus(): string
    {
        return match ($this->order->status) {
            'accepted' => 'Le restaurant a accepté votre commande.',
            'preparing' => 'Votre commande est en cours de préparation.',
            'ready' => 'Votre commande est prête.',
            'picked_up' => 'Votre commande est en route.',
            'delivered' => 'Votre commande a été livrée. Bon appétit !',
            'cancelled' => 'Votre commande a été annulée.',
            default => 'Le statut de votre commande a changé.',
        };
    }
}
