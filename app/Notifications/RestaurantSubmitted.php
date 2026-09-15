<?php

namespace App\Notifications;

use App\Models\Restaurant;

/**
 * Previent les administrateurs qu'un restaurant attend leur validation.
 */
class RestaurantSubmitted extends AppNotification
{
    public function __construct(public Restaurant $restaurant)
    {
    }

    public function payload(object $notifiable): array
    {
        return [
            'title' => 'Restaurant à valider',
            'body' => $this->restaurant->name.' ('.$this->restaurant->city.') attend une validation.',
            'url' => route('admin.restaurants', ['filter' => 'pending']),
            'icon' => 'ri-shield-check-line',
            'level' => 'warning',
            'restaurant_id' => $this->restaurant->id,
        ];
    }
}
