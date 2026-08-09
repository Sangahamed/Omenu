<?php

namespace App\Livewire\Restaurant;

use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    public $restaurant;
    public $selectedStatus = null;

    protected $listeners = ['orderStatusUpdated' => '$refresh', 'newOrder' => '$refresh'];

    public function mount()
    {
        $this->restaurant = Restaurant::where('user_id', Auth::id())->firstOrFail();
    }

    public function updateStatus($orderId, $status)
    {
        $order = Order::where('restaurant_id', $this->restaurant->id)->findOrFail($orderId);
        $order->status = $status;
        $order->save();

        // Mettre à jour le timestamp correspondant
        $timestampField = $status . '_at';
        if (in_array($status, ['accepted', 'preparing', 'ready', 'picked_up', 'delivered', 'cancelled'])) {
            $order->$timestampField = now();
            $order->save();
        }

        // Broadcast (pour le client, pour le livreur plus tard)
        broadcast(new \App\Events\OrderStatusUpdated($order));

        $this->dispatch('notify', message: 'Statut mis à jour !');
    }

    public function getOrdersByStatus($status)
    {
        return Order::where('restaurant_id', $this->restaurant->id)
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getStatisticsProperty()
    {
        $today = now()->startOfDay();
        $orders = Order::where('restaurant_id', $this->restaurant->id);

        return [
            'total_orders' => $orders->count(),
            'today_orders' => $orders->where('created_at', '>=', $today)->count(),
            'today_revenue' => $orders->where('created_at', '>=', $today)->sum('total'),
            'pending' => $orders->where('status', 'pending')->count(),
            'in_progress' => $orders->whereIn('status', ['accepted', 'preparing', 'ready'])->count(),
        ];
    }

    public function getPopularMenusProperty()
    {
        return \DB::table('order_items')
            ->select('menu_id', 'name', \DB::raw('sum(quantity) as total_quantity'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.restaurant_id', $this->restaurant->id)
            ->where('orders.status', 'delivered')
            ->groupBy('menu_id', 'name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        $statuses = ['pending', 'accepted', 'preparing', 'ready', 'picked_up', 'delivered', 'cancelled'];
        $ordersByStatus = [];

        foreach ($statuses as $status) {
            $ordersByStatus[$status] = $this->getOrdersByStatus($status);
        }

        return view('livewire.restaurant.dashboard', [
            'ordersByStatus' => $ordersByStatus,
            'statistics' => $this->statistics,
            'popularMenus' => $this->popularMenus,
            'statuses' => $statuses,
        ])->layout('components.front.layouts.front');
    }
}