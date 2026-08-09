<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RestaurantController;
use App\Livewire\RestaurantMap;
use App\Livewire\Checkout;
use App\Livewire\Restaurant\Dashboard;
use App\Livewire\Delivery\Dashboard as DeliveryDashboard;
use App\Livewire\OrderTracking;

// ====================================================
// ROUTES PUBLIQUES
// ====================================================

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{slug}', [RestaurantController::class, 'show'])->name('restaurants.show');

// ====================================================
// ROUTES POUR UTILISATEURS AUTHENTIFIÉS (client)
// ====================================================

Route::middleware(['auth'])->group(function () {

    // Profil et tableau de bord client
    Route::view('dashboard', 'dashboard')
        ->middleware(['verified'])
        ->name('dashboard');

    Route::view('profile', 'profile')->name('profile');

    // Panier et commande (Livewire)
    Route::get('/checkout', Checkout::class)->name('checkout');

    // Confirmation et retour Stripe
    Route::get('/order/confirmation/{order}', [OrderController::class, 'confirmation'])
        ->name('order.confirmation');

    Route::get('/order/stripe/success/{order}', [OrderController::class, 'stripeSuccess'])
        ->name('order.stripe.success');

    // Création de commande (fallback si besoin)
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
});

// ====================================================
// ROUTES POUR RESTAURATEURS
// ====================================================

Route::middleware(['auth', 'role:restaurant'])->prefix('restaurant')->group(function () {

    Route::get('/dashboard', [RestaurantController::class, 'dashboard'])
        ->name('restaurant.dashboard');

    Route::get('/create', [RestaurantController::class, 'create'])
        ->name('restaurants.create');

    Route::post('/', [RestaurantController::class, 'store'])
        ->name('restaurants.store');
        
    Route::get('/restaurant/dashboard', Dashboard::class)->name('restaurant.dashboard');
});

// ====================================================
// ROUTES POUR ADMINISTRATEURS (super-admin & admin)
// ====================================================

Route::middleware(['auth', 'role:super-admin|admin'])->prefix('admin')->group(function () {

    // Gestion des restaurants (CRUD complet via contrôleur)
    Route::resource('restaurants', RestaurantController::class)
        ->except(['show']); // ou conserver show selon besoin

    // Gestion via Livewire (tableau de bord)
    Route::get('/restaurants', \App\Livewire\Admin\RestaurantManager::class)
        ->name('admin.restaurants');
});

Route::middleware(['auth', 'role:delivery'])->prefix('delivery')->group(function () {
    Route::get('/dashboard', DeliveryDashboard::class)->name('delivery.dashboard');
});

// Suivi commande (client)
Route::middleware(['auth'])->group(function () {
    Route::get('/order/tracking/{orderId}', OrderTracking::class)->name('order.tracking');
});

// ====================================================
// ROUTES D'AUTHENTIFICATION (Breeze)
// ====================================================
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\RestaurantManager as AdminRestaurantManager;
use App\Livewire\Admin\UserManager;

Route::middleware(['auth', 'role:super-admin|admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('admin.dashboard');
    Route::get('/restaurants', AdminRestaurantManager::class)->name('admin.restaurants');
    Route::get('/users', UserManager::class)->name('admin.users');
});

// Recherche publique (API)
Route::get('/api/search', function (\Illuminate\Http\Request $request) {
    $query = $request->get('q');
    $restaurants = \App\Models\Restaurant::search($query)
        ->take(10)
        ->get();
    $menus = \App\Models\Menu::search($query)
        ->take(10)
        ->get();

    return response()->json([
        'restaurants' => $restaurants,
        'menus' => $menus,
    ]);
})->name('api.search');
require __DIR__.'/auth.php';