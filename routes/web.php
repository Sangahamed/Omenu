<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OrderController;
use App\Http\Controllers\RestaurantController;

use App\Livewire\Checkout;
use App\Livewire\OrderTracking;
use App\Livewire\Restaurant\Dashboard as RestaurantDashboard;
use App\Livewire\Delivery\Dashboard as DeliveryDashboard;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\RestaurantManager as AdminRestaurantManager;
use App\Livewire\Admin\UserManager as AdminUserManager;

use App\Models\Menu;
use App\Models\Restaurant;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => view('home'))->name('home');

Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{slug}', [RestaurantController::class, 'show'])->name('restaurants.show');

Route::get('/api/search', function (Request $request) {
    $query = $request->get('q');

    return response()->json([
        'restaurants' => Restaurant::search($query)->take(10)->get(),
        'menus' => Menu::search($query)->take(10)->get(),
    ]);
})->name('api.search');

/*
|--------------------------------------------------------------------------
| Routes clients authentifiés
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::view('dashboard', 'dashboard')
        ->middleware('verified')
        ->name('dashboard');

    Route::view('profile', 'profile')->name('profile');

    // "Mon Espace" — redirige vers le bon tableau de bord selon le rôle
    // de l'utilisateur connecté (client, restaurateur, livreur, admin).
    Route::get('/mon-espace', function () {
        $user = auth()->user();

        return match (true) {
            $user->hasRole(['super-admin', 'admin']) => redirect()->route('admin.dashboard'),
            $user->hasRole('restaurant') => redirect()->route('restaurant.dashboard'),
            $user->hasRole('delivery') => redirect()->route('delivery.dashboard'),
            default => redirect()->route('dashboard'),
        };
    })->name('space');

    // Panier, commande et suivi (Livewire)
    Route::get('/checkout', Checkout::class)->name('checkout');
    Route::get('/order/tracking/{orderId}', OrderTracking::class)->name('order.tracking');

    // Confirmation et retour de paiement
    Route::get('/order/confirmation/{order}', [OrderController::class, 'confirmation'])
        ->name('order.confirmation');
    Route::get('/order/stripe/success/{order}', [OrderController::class, 'stripeSuccess'])
        ->name('order.stripe.success');
    Route::post('/orders', [OrderController::class, 'store'])
        ->name('orders.store');
});

/*
|--------------------------------------------------------------------------
| Routes restaurateurs
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:restaurant'])->prefix('restaurant')->group(function () {
    Route::get('/dashboard', RestaurantDashboard::class)->name('restaurant.dashboard');
});

// Accessible à tout utilisateur connecté : c'est ce formulaire qui fait
// passer un client au statut restaurateur (voir RestaurantController::store).
Route::middleware('auth')->prefix('restaurant')->group(function () {
    Route::get('/create', [RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('/', [RestaurantController::class, 'store'])->name('restaurants.store');
});

/*
|--------------------------------------------------------------------------
| Routes livreurs
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:delivery'])->prefix('delivery')->group(function () {
    Route::get('/dashboard', DeliveryDashboard::class)->name('delivery.dashboard');
});

/*
|--------------------------------------------------------------------------
| Routes administrateurs (super-admin & admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:super-admin|admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/restaurants', AdminRestaurantManager::class)->name('restaurants');
    Route::get('/users', AdminUserManager::class)->name('users');
});

/*
|--------------------------------------------------------------------------
| Authentification (Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
