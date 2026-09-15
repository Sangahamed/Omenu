<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tableau de bord client.
     * GET /dashboard
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Chaque rôle a son propre espace : on redirige avant de calculer quoi que ce soit.
        if ($user->hasRole(['super-admin', 'admin'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('restaurant')) {
            return redirect()->route('restaurant.dashboard');
        }

        if ($user->hasRole('delivery')) {
            return redirect()->route('delivery.dashboard');
        }

        $recentOrders = Order::where('user_id', $user->id)
            ->with(['restaurant', 'items'])
            ->latest()
            ->take(10)
            ->get();

        $activeOrders = Order::where('user_id', $user->id)
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->with('restaurant')
            ->latest()
            ->get();

        /*
         * Les compteurs se calculent en base, pas sur les 10 dernières
         * commandes chargées : le total dépensé et le nombre de commandes
         * étaient sous-évalués dès la 11e commande.
         */
        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'active_orders' => $activeOrders->count(),
            'total_spent' => (float) Order::where('user_id', $user->id)
                ->where('status', 'delivered')
                ->sum('total'),
            'delivered_orders' => Order::where('user_id', $user->id)
                ->where('status', 'delivered')
                ->count(),
            'favorites' => Favorite::where('user_id', $user->id)->count(),
        ];

        $favorites = Favorite::where('user_id', $user->id)
            ->with('restaurant')
            ->latest()
            ->get()
            ->pluck('restaurant')
            ->filter();

        return view('dashboard', [
            'user' => $user,
            'orders' => $recentOrders,
            'activeOrders' => $activeOrders,
            'stats' => $stats,
            'favorites' => $favorites,
            'restaurantsCount' => Restaurant::published()->count(),
        ]);
    }
}
