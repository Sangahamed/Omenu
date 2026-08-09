<?php

namespace App\Livewire;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderTracking extends Component
{
    public $order;
    public $deliveryLocation = null;
    public $deliveryPersonName = null;

    protected $listeners = ['locationUpdated' => 'updateLocation', 'orderStatusUpdated' => '$refresh'];

    public function mount($orderId)
    {
        $this->order = Order::with('restaurant')->findOrFail($orderId);
        
        // Vérifier que le client est bien le propriétaire de la commande
        if ($this->order->user_id !== Auth::id()) {
            abort(403);
        }

        // Charger la position initiale du livreur
        if ($this->order->delivery_person_id) {
            $deliveryPerson = \App\Models\User::find($this->order->delivery_person_id);
            if ($deliveryPerson) {
                $this->deliveryLocation = [
                    'lat' => $deliveryPerson->current_latitude,
                    'lng' => $deliveryPerson->current_longitude,
                ];
                $this->deliveryPersonName = $deliveryPerson->name;
            }
        }
    }

    public function updateLocation($data)
    {
        $this->deliveryLocation = [
            'lat' => $data['latitude'],
            'lng' => $data['longitude'],
        ];
        $this->deliveryPersonName = $data['delivery_person'] ?? $this->deliveryPersonName;
    }

    public function render()
    {
        return view('livewire.order-tracking')->layout('components.front.layouts.front');
    }
}