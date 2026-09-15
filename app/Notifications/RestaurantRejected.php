<?php

namespace App\Notifications;

use App\Models\Restaurant;

/**
 * Previent le restaurateur que sa fiche a ete suspendue ou refusee.
 */
class RestaurantRejected extends AppNotification
{
    public function __construct(public Restaurant $restaurant, public ?string $reason = null)
    {
    }

    public function payload(object $notifiable): array
    {
        return [
            'title' => 'Validation suspendue',
            'body' => $this->restaurant->name.' n\'est pas encore publie. '
                .($this->reason ?: 'Notre équipe revient vers vous rapidement.'),
            'url' => route('restaurant.pending'),
            'icon' => 'ri-error-warning-line',
            'level' => 'error',
            'restaurant_id' => $this->restaurant->id,
        ];
    }
}
