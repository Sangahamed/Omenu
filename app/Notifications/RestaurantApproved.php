<?php

namespace App\Notifications;

use App\Models\Restaurant;

/**
 * Previent le restaurateur que sa fiche est validee et donc en ligne.
 */
class RestaurantApproved extends AppNotification
{
    public function __construct(public Restaurant $restaurant)
    {
    }

    public function payload(object $notifiable): array
    {
        return [
            'title' => 'Votre restaurant est validé',
            'body' => $this->restaurant->name.' est désormais visible par les clients.',
            'url' => route('restaurant.dashboard'),
            'icon' => 'ri-checkbox-circle-line',
            'level' => 'success',
            'restaurant_id' => $this->restaurant->id,
        ];
    }
}
