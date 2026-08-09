<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderId;
    public $latitude;
    public $longitude;
    public $deliveryPersonName;

    public function __construct($orderId, $latitude, $longitude, $deliveryPersonName = null)
    {
        $this->orderId = $orderId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->deliveryPersonName = $deliveryPersonName;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('order.' . $this->orderId);
    }

    public function broadcastWith()
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'delivery_person' => $this->deliveryPersonName,
        ];
    }
}