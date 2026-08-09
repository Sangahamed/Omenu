<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public $period = 'week'; // week, month, year
    public $chartData = [];

    public function mount()
    {
        $this->updateStats();
    }

    public function updateStats()
    {
        $this->chartData = $this->getChartData();
    }

    public function getStatsProperty()
    {
        return [
            'total_users' => User::count(),
            'total_restaurants' => Restaurant::count(),
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'active_restaurants' => Restaurant::where('is_active', true)->count(),
        ];
    }

    public function getChartData()
    {
        $labels = [];
        $revenues = [];
        $orders = [];

        switch ($this->period) {
            case 'week':
                $start = Carbon::now()->startOfWeek();
                for ($i = 0; $i < 7; $i++) {
                    $date = $start->copy()->addDays($i);
                    $labels[] = $date->format('D');
                    $revenues[] = Order::whereDate('created_at', $date)
                        ->where('status', 'delivered')
                        ->sum('total');
                    $orders[] = Order::whereDate('created_at', $date)->count();
                }
                break;
            case 'month':
                $start = Carbon::now()->startOfMonth();
                for ($i = 0; $i < $start->daysInMonth; $i++) {
                    $date = $start->copy()->addDays($i);
                    $labels[] = $date->format('d M');
                    $revenues[] = Order::whereDate('created_at', $date)
                        ->where('status', 'delivered')
                        ->sum('total');
                    $orders[] = Order::whereDate('created_at', $date)->count();
                }
                break;
            case 'year':
                for ($i = 1; $i <= 12; $i++) {
                    $date = Carbon::create(null, $i, 1);
                    $labels[] = $date->format('M');
                    $revenues[] = Order::whereMonth('created_at', $i)
                        ->whereYear('created_at', now()->year)
                        ->where('status', 'delivered')
                        ->sum('total');
                    $orders[] = Order::whereMonth('created_at', $i)
                        ->whereYear('created_at', now()->year)
                        ->count();
                }
                break;
        }

        return [
            'labels' => $labels,
            'revenues' => $revenues,
            'orders' => $orders,
        ];
    }

    public function exportCsv()
    {
        // Logique d'export CSV
        $orders = Order::with('user', 'restaurant')->get();
        $filename = 'orders_export_' . now()->format('Y-m-d') . '.csv';
        $handle = fopen('php://memory', 'w');
        fputcsv($handle, ['ID', 'Client', 'Restaurant', 'Total', 'Statut', 'Date']);

        foreach ($orders as $order) {
            fputcsv($handle, [
                $order->id,
                $order->customer_name,
                $order->restaurant->name,
                $order->total,
                $order->status_label,
                $order->created_at->format('d/m/Y H:i'),
            ]);
        }

        fseek($handle, 0);
        return response()->stream(function () use ($handle) {
            fpassthru($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'stats' => $this->stats,
            'chartData' => $this->chartData,
        ])->layout('layouts.app');
    }
}