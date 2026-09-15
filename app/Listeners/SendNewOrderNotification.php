<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Notifications\NewOrderReceived;
use Illuminate\Support\Facades\Log;

/**
 * Notifie le restaurateur (cloche + Web Push) a chaque nouvelle commande.
 */
class SendNewOrderNotification
{
    public function handle(OrderPlaced $event): void
    {
        $owner = $event->order->restaurant?->user;

        if (! $owner) {
            return;
        }

        try {
            $owner->notify(new NewOrderReceived($event->order));
        } catch (\Throwable $e) {
            // Une notification ne doit jamais faire echouer la prise de commande.
            Log::error('Notification nouvelle commande impossible : '.$e->getMessage());
        }
    }
}
