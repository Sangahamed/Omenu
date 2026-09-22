<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\RestaurantController;

use App\Livewire\Checkout;
use App\Livewire\Contact;
use App\Livewire\OrderTracking;
use App\Livewire\Restaurant\Dashboard as RestaurantDashboard;
use App\Livewire\Delivery\Dashboard as DeliveryDashboard;
use App\Livewire\Delivery\Stats as DeliveryStats;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\ContactMessages as AdminContactMessages;
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

// Affichette QR imprimable menant a la fiche publique du restaurant
// (carte, commande, reservation), et le QR seul en SVG.
Route::get('/restaurants/{slug}/qr-code', [RestaurantController::class, 'qrCode'])->name('restaurants.qrcode');
Route::get('/restaurants/{slug}/qr-code.svg', [RestaurantController::class, 'qrCodeSvg'])->name('restaurants.qrcode.svg');

// Formulaire de contact public.
Route::get('/contact', Contact::class)->name('contact');

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
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::view('profile', 'profile')->name('profile');

    // Abonnements Web Push du navigateur (notifications systeme).
    Route::post('/push-subscriptions', [PushSubscriptionController::class, 'store'])
        ->name('push.subscribe');
    Route::delete('/push-subscriptions', [PushSubscriptionController::class, 'destroy'])
        ->name('push.unsubscribe');

    // Identifiant du restaurant du restaurateur connecte, utilise par le
    // listener Echo du front. La version /api/restaurant-id exige un jeton
    // Sanctum, inaccessible depuis une simple session navigateur.
    Route::get('/restaurant-id', fn () => response()->json([
        'restaurant_id' => auth()->user()->restaurant?->id,
    ]))->name('restaurant-id');

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

// Le tableau de bord n'est accessible qu'une fois la fiche validée par un
// administrateur ; sinon l'utilisateur est renvoyé vers l'écran d'attente.
Route::middleware(['auth', 'role:restaurant', 'restaurant.verified'])->prefix('restaurant')->group(function () {
    Route::get('/dashboard', RestaurantDashboard::class)->name('restaurant.dashboard');
});

// Accessible à tout utilisateur connecté : c'est ce formulaire qui fait
// passer un client au statut restaurateur (voir RestaurantController::store).
Route::middleware('auth')->prefix('restaurant')->group(function () {
    Route::get('/create', [RestaurantController::class, 'create'])->name('restaurants.create');
    Route::post('/', [RestaurantController::class, 'store'])->name('restaurants.store');
    Route::get('/en-attente', [RestaurantController::class, 'pending'])->name('restaurant.pending');
});

/*
|--------------------------------------------------------------------------
| Routes livreurs
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:delivery'])->prefix('delivery')->group(function () {
    Route::get('/dashboard', DeliveryDashboard::class)->name('delivery.dashboard');
    Route::get('/stats', DeliveryStats::class)->name('delivery.stats');
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
    Route::get('/messages', AdminContactMessages::class)->name('messages');
});

/*
|--------------------------------------------------------------------------
| Authentification (Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
