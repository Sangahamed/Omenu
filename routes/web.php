<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RestaurantController;
use App\Livewire\RestaurantMap;
use App\Livewire\Checkout;

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



// ====================================================
// ROUTES D'AUTHENTIFICATION (Breeze)
// ====================================================

require __DIR__.'/auth.php';