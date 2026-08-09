<?php

namespace App\Livewire\Delivery;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $user;
    public $availableOrders = [];
    public $myOrders = [];
    public $status = 'available'; // available, on_delivery, offline

    protected $listeners = ['refreshDeliveries' => '$refresh', 'orderAssigned' => '$refresh'];

    public function mount()
    {
        $this->user = Auth::user();
        $this->loadOrders();
    }

    public function loadOrders()
    {
        // Commandes disponibles à proximité (à améliorer avec la géolocalisation)
        $this->availableOrders = Order::where('status', 'ready')
            ->whereNull('delivery_person_id')
            ->with('restaurant')
            ->latest()
            ->get();

        // Mes commandes en cours
        $this->myOrders = Order::where('delivery_person_id', $this->user->id)
            ->whereIn('status', ['picked_up', 'ready'])
            ->with('restaurant')
            ->get();
    }

    public function acceptOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        if ($order->delivery_person_id) {
            $this->dispatch('notify', message: 'Cette commande a déjà été prise.');
            return;
        }

        $order->update([
            'delivery_person_id' => $this->user->id,
            'status' => 'picked_up',
            'picked_up_at' => now(),
        ]);

        // Broadcast au client
        broadcast(new \App\Events\OrderStatusUpdated($order));

        $this->loadOrders();
        $this->dispatch('notify', message: 'Commande acceptée !');
    }

    public function markAsDelivered($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        broadcast(new \App\Events\OrderStatusUpdated($order));

        $this->loadOrders();
        $this->dispatch('notify', message: 'Livraison terminée !');
    }

    public function updateLocation($latitude, $longitude)
    {
        $this->user->update([
            'current_latitude' => $latitude,
            'current_longitude' => $longitude,
            'last_location_update' => now(),
        ]);

        // Broadcast à chaque client qui a une commande en cours avec ce livreur
        $myOrders = Order::where('delivery_person_id', $this->user->id)
            ->whereIn('status', ['picked_up', 'ready'])
            ->get();

        foreach ($myOrders as $order) {
            broadcast(new \App\Events\DeliveryLocationUpdated(
                $order->id,
                $latitude,
                $longitude,
                $this->user->name
            ));
        }
    }

    public function toggleStatus($status)
    {
        $this->status = $status;
        $this->user->update(['is_delivery_available' => $status === 'available']);
        $this->dispatch('notify', message: 'Statut mis à jour : ' . ucfirst($status));
    }

    public function render()
    {
        return view('livewire.delivery.dashboard', [
            'availableOrders' => $this->availableOrders,
            'myOrders' => $this->myOrders,
        ])->layout('components.front.layouts.front');
    }
}