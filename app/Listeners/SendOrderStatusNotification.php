<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Facades\Log;

/**
 * Notifie le client a chaque changement de statut de sa commande.
 */
class SendOrderStatusNotification
{
    public function handle(OrderStatusUpdated $event): void
    {
        $customer = $event->order->user;

        if (! $customer) {
            return;
        }

        try {
            $customer->notify(new OrderStatusChanged($event->order));
        } catch (\Throwable $e) {
            Log::error('Notification changement de statut impossible : '.$e->getMessage());
        }
    }
}
