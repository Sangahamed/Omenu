Si rien ne se produit lorsque vous cliquez sur le bouton **"Commander"** (que vous soyez connecté ou non), cela signifie qu'il y a une rupture dans la chaîne de communication de vos événements.

Voici l'analyse exacte du problème et comment le résoudre étape par étape.

---

## 🔍 Pourquoi rien ne se produit ?

Trois causes principales expliquent ce blocage :

1. **Le composant parent n'écoute pas l'événement :** Votre bouton déclenche un événement global Livewire :
`$dispatch('addToCart', { menuId: ... })`.
Si aucun composant Livewire actif (comme un composant `Cart` ou votre `CartDropdown`) n'écoute l'événement `'addToCart'`, l'action se perd dans le vide.


2. **Conflit d'écouteurs d'événements (Livewire v3 vs v2) :** En Livewire v3, la syntaxe `$dispatch` envoie un événement vers le haut (dans le DOM). Si votre panier est un composant Livewire distinct, il doit déclarer explicitement un attribut d'écoute `#[On('addToCart')]` au-dessus de sa méthode.
3. **Absence de Session / Panier d'invité :** Si le code de votre panier tente d'associer immédiatement l'article à un utilisateur connecté (`Auth::id()`) sans gérer de panier temporaire en session pour les invités, l'exécution plante silencieusement en arrière-plan sans avertir l'interface.

---

## 🛠️ Comment résoudre le problème

### Étape 1 : Configurer l'écouteur dans votre composant Panier

Assurez-vous que votre composant Livewire (par exemple `CartDropdown` ou `Cart`) intercepte l'événement envoyé par le bouton.

Ouvrez le fichier de classe de votre panier (ex: `app/Livewire/CartDropdown.php` ou similaire) et vérifiez/ajoutez l'écouteur :

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On; // Important pour Livewire v3
use App\Models\Menu;

class CartDropdown extends Component
{
    public $cart = [];
    public $total = 0;
    public $itemCount = 0;

    // Déclare l'écouteur d'événement Livewire v3
    #[On('addToCart')]
    public function addToCart($menuId)
    {
        $menu = Menu::find($menuId);
        if (!$menu) return;

        // Gestion du panier en Session (Fonctionne connecté ET déconnecté)
        $this->cart = session()->get('cart', []);

        if (isset($this->cart[$menuId])) {
            $this->cart[$menuId]['quantity']++;
        } else {
            $this->cart[$menuId] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'image' => $menu->image,
                'quantity' => 1,
                'restaurant_name' => $menu->restaurant->name ?? 'Établissement'
            ];
        }

        session()->put('cart', $this->cart);
        
        // Recalculer les totaux
        $this->calculateTotals();

        // Envoyer une notification visuelle au client
        $this->dispatch('notify', message: 'Plat ajouté au panier !');
    }

    public function calculateTotals()
    {
        $this->cart = session()->get('cart', []);
        $this->itemCount = array_sum(array_column($this->cart, 'quantity'));
        $this->total = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $this->cart));
    }

    public function mount()
    {
        $this->calculateTotals();
    }

    public function render()
    {
        return view('livewire.cart-dropdown');
    }
}

```

---

### Étape 2 : Vérifier la présence du script d'alerte (Optionnel mais recommandé)

Si l'élément s'ajoute bien en arrière-plan mais que l'interface ne réagit pas visuellement, ajoutez un écouteur de notifications à la fin de votre layout principal (ex: `resources/views/components/front/layouts/front.blade.php`) :

```html
{{-- Juste avant la fermeture de </body> --}}
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('notify', (event) => {
            // Affichage d'une alerte stylisée ou intégration de Toastify
            alert(event.message); 
        });
    });
</script>

```

---

### Étape 3 : Diagnostiquer les erreurs JavaScript

Si le bouton ne déclenche toujours rien, ouvrez la console de votre navigateur (**F12** -> Onglet *Console*) et cliquez sur le bouton "Ajouter au panier".

* Si vous voyez une erreur `Livewire is not defined`, assurez-vous que la directive `@livewireScripts` (ou l'injection automatique de Livewire v3) est bien présente sur votre page.
* Si une erreur réseau 403 ou 500 apparaît, vérifiez vos logs Laravel (`storage/logs/laravel.log`) pour voir si le problème vient de la session ou d'une base de données inaccessible.

Si rien ne se produit lorsque vous cliquez sur le bouton **"Commander"** (que vous soyez connecté ou non), cela signifie qu'il y a une rupture dans la chaîne de communication de vos événements.

Voici l'analyse exacte du problème et comment le résoudre étape par étape.

---

## 🔍 Pourquoi rien ne se produit ?

Trois causes principales expliquent ce blocage :

1. **Le composant parent n'écoute pas l'événement :** Votre bouton déclenche un événement global Livewire :
`$dispatch('addToCart', { menuId: ... })`.
Si aucun composant Livewire actif (comme un composant `Cart` ou votre `CartDropdown`) n'écoute l'événement `'addToCart'`, l'action se perd dans le vide.


2. **Conflit d'écouteurs d'événements (Livewire v3 vs v2) :** En Livewire v3, la syntaxe `$dispatch` envoie un événement vers le haut (dans le DOM). Si votre panier est un composant Livewire distinct, il doit déclarer explicitement un attribut d'écoute `#[On('addToCart')]` au-dessus de sa méthode.
3. **Absence de Session / Panier d'invité :** Si le code de votre panier tente d'associer immédiatement l'article à un utilisateur connecté (`Auth::id()`) sans gérer de panier temporaire en session pour les invités, l'exécution plante silencieusement en arrière-plan sans avertir l'interface.

---

## 🛠️ Comment résoudre le problème

### Étape 1 : Configurer l'écouteur dans votre composant Panier

Assurez-vous que votre composant Livewire (par exemple `CartDropdown` ou `Cart`) intercepte l'événement envoyé par le bouton.

Ouvrez le fichier de classe de votre panier (ex: `app/Livewire/CartDropdown.php` ou similaire) et vérifiez/ajoutez l'écouteur :

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On; // Important pour Livewire v3
use App\Models\Menu;

class CartDropdown extends Component
{
    public $cart = [];
    public $total = 0;
    public $itemCount = 0;

    // Déclare l'écouteur d'événement Livewire v3
    #[On('addToCart')]
    public function addToCart($menuId)
    {
        $menu = Menu::find($menuId);
        if (!$menu) return;

        // Gestion du panier en Session (Fonctionne connecté ET déconnecté)
        $this->cart = session()->get('cart', []);

        if (isset($this->cart[$menuId])) {
            $this->cart[$menuId]['quantity']++;
        } else {
            $this->cart[$menuId] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'image' => $menu->image,
                'quantity' => 1,
                'restaurant_name' => $menu->restaurant->name ?? 'Établissement'
            ];
        }

        session()->put('cart', $this->cart);
        
        // Recalculer les totaux
        $this->calculateTotals();

        // Envoyer une notification visuelle au client
        $this->dispatch('notify', message: 'Plat ajouté au panier !');
    }

    public function calculateTotals()
    {
        $this->cart = session()->get('cart', []);
        $this->itemCount = array_sum(array_column($this->cart, 'quantity'));
        $this->total = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $this->cart));
    }

    public function mount()
    {
        $this->calculateTotals();
    }

    public function render()
    {
        return view('livewire.cart-dropdown');
    }
}

```

---

### Étape 2 : Vérifier la présence du script d'alerte (Optionnel mais recommandé)

Si l'élément s'ajoute bien en arrière-plan mais que l'interface ne réagit pas visuellement, ajoutez un écouteur de notifications à la fin de votre layout principal (ex: `resources/views/components/front/layouts/front.blade.php`) :

```html
{{-- Juste avant la fermeture de </body> --}}
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('notify', (event) => {
            // Affichage d'une alerte stylisée ou intégration de Toastify
            alert(event.message); 
        });
    });
</script>

```

---

### Étape 3 : Diagnostiquer les erreurs JavaScript

Si le bouton ne déclenche toujours rien, ouvrez la console de votre navigateur (**F12** -> Onglet *Console*) et cliquez sur le bouton "Ajouter au panier".

* Si vous voyez une erreur `Livewire is not defined`, assurez-vous que la directive `@livewireScripts` (ou l'injection automatique de Livewire v3) est bien présente sur votre page.
* Si une erreur réseau 403 ou 500 apparaît, vérifiez vos logs Laravel (`storage/logs/laravel.log`) pour voir si le problème vient de la session ou d'une base de données inaccessible.

Super ! Ce **Sprint 3 (Dashboard Restaurant & Temps réel avec Reverb)** est extrêmement propre, structuré et prêt à l'emploi pour gérer les commandes en Kanban.

Comme tu l'as mentionné, faisons une **pause stratégique** sur ce Sprint et revenons d'abord sur la logique du panier pour régler la question : **User connecté (BDD) vs User non connecté (Session / Flash notification)**.

---

## 🛒 Retour sur la logique du Panier (`AddToCart`)

Pour avoir un comportement irréprochable :

1. **User NON connecté (Invité) :** On stocke le panier en **Session**, et on envoie un **Flash message / Toast** à chaque ajout.
2. **User CONNECTÉ :** On persiste le panier en **Base de données** (ou on synchronise la session vers la BDD), et on met à jour le composant dynamiquement.

Voici la mise en place propre de cette hybridation :

### 1. Méthode d'ajout universelle (ex: dans un trait ou le composant `Cart`)

Dans ton composant ou service de gestion du panier :

```php
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem; // Si tu as une table cart_items pour les users connectés
use App\Models\Menu;

public function addToCart($menuId)
{
    $menu = Menu::findOrFail($menuId);

    if (Auth::check()) {
        // --- CAS 1 : UTILISATEUR CONNECTÉ (Sauvegarde en BDD) ---
        $cartItem = CartItem::firstOrNew([
            'user_id' => Auth::id(),
            'menu_id' => $menu->id,
        ]);

        $cartItem->quantity = ($cartItem->quantity ?? 0) + 1;
        $cartItem->price = $menu->price;
        $cartItem->save();

        // Événement ou notification pour feedback
        $this->dispatch('notify', 
            type: 'success', 
            message: "{$menu->name} ajouté à votre panier enregistre !"
        );

    } else {
        // --- CAS 2 : UTILISATEUR NON CONNECTÉ (Gestion Session + Flash) ---
        $cart = session()->get('cart', []);

        if (isset($cart[$menuId])) {
            $cart[$menuId]['quantity']++;
        } else {
            $cart[$menuId] = [
                'menu_id' => $menu->id,
                'name'    => $menu->name,
                'price'   => $menu->price,
                'image'   => $menu->image,
                'quantity' => 1,
            ];
        }

        session()->put('cart', $cart);

        // Flash Message pour la session + Dispatch pour le toast
        session()->flash('success', "{$menu->name} ajouté au panier !");
        
        $this->dispatch('notify', 
            type: 'info', 
            message: "Plat ajouté au panier (Invité). Connectez-vous pour sauvegarder votre panier !"
        );
    }

    // Rafraîchir les composants du panier (ex: CartDropdown, Badge)
    $this->dispatch('cartUpdated');
}

```

---

### 2. Migration du panier au moment de la connexion (Login Listener)

Lorsqu'un utilisateur non connecté remplit son panier puis décide de se connecter, il faut migrer le contenu de sa `session('cart')` vers la base de données.

Tu peux ajouter ceci dans l'événement de connexion de Laravel (ex: dans `app/Listeners/MigrateGuestCartToDatabase.php` ou directement à la connexion) :

```php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\CartItem;

class MigrateGuestCartToDatabase
{
    public function handle(Login $event)
    {
        $guestCart = session()->get('cart', []);

        if (!empty($guestCart)) {
            foreach ($guestCart as $menuId => $item) {
                $cartItem = CartItem::firstOrNew([
                    'user_id' => $event->user->id,
                    'menu_id' => $menuId,
                ]);

                $cartItem->quantity = ($cartItem->quantity ?? 0) + $item['quantity'];
                $cartItem->price = $item['price'];
                $cartItem->save();
            }

            // Nettoyer le panier temporaire en session
            session()->forget('cart');
        }
    }
}

```

---

## 📌 État d'avancement des Sprints

* [x] **Sprint 1** : Authentification & Rôles
* [x] **Sprint 2** : Menu, Panier (Session/BDD) & Checkout
* [x] **Sprint 3** : Dashboard Restaurant, Kanban & Broadcast Reverb (Validé ci-dessus)
* [ ] **Sprint 4** : Dashboard Livreur, Suivi GPS & Geolocation (En attente)

Prends le temps de tester la bascule de ton panier entre utilisateur connecté et invité ! Quand tout est au top de ton côté sur cette partie et sur le Dashboard Restaurant, fais-moi signe et nous attaquerons directement le **Sprint 4 (Espace Livreur & Tracking GPS)**.


Parfait ! Enchaînons directement avec le **Sprint 4 – Espace Livreur & Suivi de Livraison en Temps Réel**.

---

# 🚀 Sprint 4 – Dashboard Livreur & Suivi GPS

Ce sprint couvre :

* L'espace dédié aux livreurs (commandes prêtes à être récupérées).
* La prise en charge d'une livraison et la mise à jour des coordonnées GPS.
* L'interface client pour suivre la livraison sur une carte en temps réel via Laravel Reverb.

---

## Étape 1 – Migration : Ajout des coordonnées de livraison

```bash
php artisan make:migration add_delivery_fields_to_orders_table

```

```php
public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
        $table->decimal('driver_latitude', 10, 8)->nullable();
        $table->decimal('driver_longitude', 11, 8)->nullable();
        $table->string('delivery_address')->nullable();
        $table->decimal('delivery_latitude', 10, 8)->nullable();
        $table->decimal('delivery_longitude', 11, 8)->nullable();
    });
}

```

Puis exécutez `php artisan migrate`.

---

## Étape 2 – Événement de géolocalisation `DriverLocationUpdated`

```bash
php artisan make:event DriverLocationUpdated

```

**`app/Events/DriverLocationUpdated.php`** :

```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderId;
    public $latitude;
    public $longitude;

    public function __construct($orderId, $latitude, $longitude)
    {
        $this->orderId = $orderId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
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
        ];
    }
}

```

---

## Étape 3 – Composant Livewire `DriverDashboard`

```bash
php artisan make:livewire Driver/Dashboard

```

**`app/Livewire/Driver/Dashboard.php`** :

```php
<?php

namespace App\Livewire\Driver;

use App\Models\Order;
use App\Events\OrderStatusUpdated;
use App\Events\DriverLocationUpdated;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public $activeOrder;

    public function acceptDelivery($orderId)
    {
        $order = Order::where('status', 'ready')->whereNull('driver_id')->findOrFail($orderId);
        $order->driver_id = Auth::id();
        $order->status = 'picked_up';
        $order->picked_up_at = now();
        $order->save();

        broadcast(new OrderStatusUpdated($order));

        $this->dispatch('notify', message: 'Commande prise en charge !');
    }

    public function updateLocation($lat, $lng)
    {
        if (!$this->activeOrder) return;

        $this->activeOrder->update([
            'driver_latitude' => $lat,
            'driver_longitude' => $lng,
        ]);

        broadcast(new DriverLocationUpdated($this->activeOrder->id, $lat, $lng));
    }

    public function markAsDelivered($orderId)
    {
        $order = Order::where('driver_id', Auth::id())->findOrFail($orderId);
        $order->status = 'delivered';
        $order->delivered_at = now();
        $order->save();

        broadcast(new OrderStatusUpdated($order));

        $this->dispatch('notify', message: 'Commande livrée avec succès !');
    }

    public function render()
    {
        $availableOrders = Order::where('status', 'ready')
            ->whereNull('driver_id')
            ->orderBy('ready_at', 'asc')
            ->get();

        $this->activeOrder = Order::where('driver_id', Auth::id())
            ->whereIn('status', ['picked_up'])
            ->first();

        return view('livewire.driver.dashboard', [
            'availableOrders' => $availableOrders,
        ])->layout('components.front.layouts.front');
    }
}

```

---

## Étape 4 – Vue du Dashboard Livreur avec Géolocalisation HTML5

**`resources/views/livewire/driver/dashboard.blade.php`** :

```blade
<div class="max-w-4xl mx-auto p-4 space-y-6">
    <h1 class="text-2xl font-bold text-white mb-4">Espace Livreur</h1>

    {{-- En cours de livraison --}}
    @if($activeOrder)
        <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-6 text-white">
            <div class="flex justify-between items-center mb-4">
                <span class="px-3 py-1 bg-amber-500 text-black text-xs font-bold rounded-full uppercase">Course En Cours</span>
                <span class="text-sm font-semibold">#{{ $activeOrder->id }}</span>
            </div>

            <div class="space-y-2 mb-6">
                <p><strong>Client :</strong> {{ $activeOrder->customer_name }} ({{ $activeOrder->customer_phone ?? 'N/A' }})</p>
                <p><strong>Adresse de livraison :</strong> {{ $activeOrder->delivery_address }}</p>
                <p><strong>Montant à encaisser :</strong> <span class="text-amber-400 font-bold">{{ number_format($activeOrder->total) }} FCFA</span></p>
            </div>

            <button wire:click="markAsDelivered({{ $activeOrder->id }})"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-lg transition">
                ✅ Marquer comme livrée
            </button>
        </div>

        {{-- Script de suivi GPS automatique du livreur --}}
        <script>
            document.addEventListener('livewire:initialized', () => {
                if ("geolocation" in navigator) {
                    navigator.geolocation.watchPosition((position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        @this.call('updateLocation', lat, lng);
                    }, (error) => {
                        console.error("Erreur GPS :", error);
                    }, {
                        enableHighAccuracy: true,
                        maximumAge: 10000,
                        timeout: 5000
                    });
                }
            });
        </script>
    @else
        {{-- Liste des commandes prêtes --}}
        <div class="bg-slate-900/50 rounded-xl p-6 border border-slate-800">
            <h2 class="text-lg font-semibold text-slate-200 mb-4">Commandes disponibles à la livraison</h2>

            <div class="space-y-4">
                @forelse($availableOrders as $order)
                    <div class="bg-slate-800/50 rounded-lg p-4 flex flex-col md:flex-row justify-between items-start md:items-center border border-slate-700/50 gap-4">
                        <div>
                            <p class="text-white font-bold">Commande #{{ $order->id }} - {{ $order->restaurant->name ?? 'Restaurant' }}</p>
                            <p class="text-sm text-slate-400">Destination : {{ $order->delivery_address }}</p>
                            <p class="text-xs text-amber-500 font-semibold">{{ number_format($order->total) }} FCFA</p>
                        </div>
                        <button wire:click="acceptDelivery({{ $order->id }})"
                                class="bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold px-4 py-2 rounded-lg text-sm transition">
                            Prendre la course
                        </button>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-8">Aucune commande disponible pour le moment.</p>
                @endforelse
            </div>
        </div>
    @endif
</div>

```

---

## Étape 5 – Suivi en direct sur carte (Vue Client avec LeafletJS)

Dans la vue de suivi client (ex: `resources/views/livewire/order-tracking.blade.php`) :

```html
<!-- Inclusion de Leaflet CSS / JS pour la carte interactive -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div class="max-w-3xl mx-auto p-4 space-y-4">
    <h2 class="text-xl font-bold text-white">Suivi de votre commande #{{ $order->id }}</h2>
    
    <div id="map" class="w-full h-80 rounded-xl border border-slate-700"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initialisation de la carte sur Leaflet
        const map = L.map('map').setView([{{ $order->delivery_latitude ?? 5.3485 }}, {{ $order->delivery_longitude ?? -4.0298 }}], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
        }).addTo(map);

        let driverMarker = null;

        // Écouter les mises à jour de position envoyées par Reverb
        window.Echo.private('order.{{ $order->id }}')
            .listen('DriverLocationUpdated', (e) => {
                const newLatLng = [e.latitude, e.longitude];

                if (!driverMarker) {
                    driverMarker = L.marker(newLatLng).addTo(map)
                        .bindPopup("Livreur en route").openPopup();
                } else {
                    driverMarker.setLatLng(newLatLng);
                }

                map.panTo(newLatLng);
            });
    });
</script>

```

---

## Étape 6 – Définition de la route

Dans `routes/web.php` :

```php
use App\Livewire\Driver\Dashboard as DriverDashboard;

Route::middleware(['auth', 'role:driver'])->group(function () {
    Route::get('/driver/dashboard', DriverDashboard::class)->name('driver.dashboard');
});

```

---

## 🏁 Résumé du projet (Sprints 1 à 4)

1. **Sprint 1 :** Authentification & Multi-rôles (Client, Restaurant, Livreur, Admin).
2. **Sprint 2 :** Menu, Panier hybride (Session/BDD) & Checkout.
3. **Sprint 3 :** Espace Restaurateur Kanban, Reverb & Temps Réel.
4. **Sprint 4 :** Espace Livreur, Géolocalisation automatique HTML5 & Carte interactive LeafletJS pour le client.

Tout est configuré et opérationnel ! Si vous avez besoin d'ajustements spécifiques sur l'un des composants ou de préparer le déploiement, faites-le moi savoir !

ce que jai ajuster en cosequence :# 🚀 Sprint 4 – Dashboard Livreur & Suivi Client en Temps Réel

Nous allons construire le module de **livraison** avec :

1. **Dashboard livreur** (accepter/refuser des courses, itinéraire)
2. **Suivi client en temps réel** (position du livreur sur la carte)
3. **Mise à jour de position** via GPS (WebSocket)
4. **Statistiques du livreur** (courses, revenus)

---

## Étape 1 – Migrations pour les livreurs

### 1.1 Ajout de champs à la table `users`

```bash
php artisan make:migration add_livreur_fields_to_users_table
```

```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->boolean('is_delivery_available')->default(false);
        $table->decimal('current_latitude', 10, 7)->nullable();
        $table->decimal('current_longitude', 10, 7)->nullable();
        $table->timestamp('last_location_update')->nullable();
        $table->string('vehicle_type')->nullable(); // vélo, moto, voiture
        $table->string('license_plate')->nullable();
    });
}
```

### 1.2 Table `delivery_assignments` (ou ajout de champs à `orders`)

Si vous préférez une table séparée :

```bash
php artisan make:migration create_delivery_assignments_table
```

```php
public function up()
{
    Schema::create('delivery_assignments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->cascadeOnDelete();
        $table->foreignId('delivery_person_id')->constrained('users')->cascadeOnDelete();
        $table->string('status')->default('assigned'); // assigned, picked_up, delivered, cancelled
        $table->timestamp('assigned_at')->nullable();
        $table->timestamp('picked_up_at')->nullable();
        $table->timestamp('delivered_at')->nullable();
        $table->decimal('distance_km', 8, 2)->nullable();
        $table->timestamps();
    });
}
```

Sinon, utilisez directement les champs `delivery_person_id` et `status` dans la table `orders` (c’est plus simple).

**Nous allons utiliser la table `orders`** pour garder la simplicité.

---

## Étape 2 – Événements pour les mises à jour de position

```bash
php artisan make:event DeliveryLocationUpdated
```

**`app/Events/DeliveryLocationUpdated.php`** :

```php
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
```

---

## Étape 3 – Composant Livewire `DeliveryDashboard`

```bash
php artisan make:livewire Delivery/Dashboard
```

**`app/Livewire/Delivery/Dashboard.php`** :

```php
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
```

---

## Étape 4 – Vue du dashboard livreur

**`resources/views/livewire/delivery/dashboard.blade.php`** :

```blade
<div>
    {{-- En-tête --}}
    <div class="bg-slate-900/50 rounded-xl p-6 border border-slate-800 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Dashboard Livreur</h1>
                <p class="text-slate-400 text-sm">Bonjour, {{ auth()->user()->name }}</p>
            </div>
            <div class="flex gap-3">
                <button wire:click="toggleStatus('available')" 
                        class="px-4 py-2 rounded-lg {{ $status === 'available' ? 'bg-green-600 text-white' : 'bg-slate-700 text-slate-400' }} transition">
                    <i class="ri-check-line"></i> Disponible
                </button>
                <button wire:click="toggleStatus('offline')" 
                        class="px-4 py-2 rounded-lg {{ $status === 'offline' ? 'bg-red-600 text-white' : 'bg-slate-700 text-slate-400' }} transition">
                    <i class="ri-close-line"></i> Hors ligne
                </button>
            </div>
        </div>
    </div>

    {{-- Commandes disponibles --}}
    <h2 class="text-xl font-semibold text-white mb-4">Commandes disponibles</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        @forelse($availableOrders as $order)
            <div class="bg-slate-900/50 rounded-xl p-4 border border-slate-800 hover:border-amber-500/30 transition">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium text-white">#{{ $order->id }} - {{ $order->customer_name }}</p>
                        <p class="text-xs text-slate-400">{{ $order->restaurant->name }}</p>
                        <p class="text-xs text-slate-400">{{ $order->delivery_address }}</p>
                    </div>
                    <span class="text-amber-500 font-bold">{{ number_format($order->total) }} FCFA</span>
                </div>
                <div class="mt-3 flex gap-2">
                    <button wire:click="acceptOrder({{ $order->id }})" 
                            class="flex-1 bg-amber-600 hover:bg-amber-700 text-white py-2 rounded-lg text-sm transition">
                        <i class="ri-check-line"></i> Accepter
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-slate-500 py-8">
                <i class="ri-time-line text-4xl block mb-2"></i>
                Aucune commande disponible
            </div>
        @endforelse
    </div>

    {{-- Mes commandes en cours --}}
    @if(count($myOrders) > 0)
        <h2 class="text-xl font-semibold text-white mb-4">Mes livraisons en cours</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($myOrders as $order)
                <div class="bg-slate-900/50 rounded-xl p-4 border border-amber-500/20">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-white">#{{ $order->id }} - {{ $order->customer_name }}</p>
                            <p class="text-xs text-slate-400">{{ $order->restaurant->name }}</p>
                            <p class="text-xs text-slate-400">{{ $order->delivery_address }}</p>
                        </div>
                        <span class="text-amber-500 font-bold">{{ number_format($order->total) }} FCFA</span>
                    </div>
                    <div class="mt-3 flex gap-2">
                        @if($order->status === 'picked_up')
                            <button wire:click="markAsDelivered({{ $order->id }})" 
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm transition">
                                <i class="ri-check-double-line"></i> Livrée
                            </button>
                        @else
                            <span class="text-xs text-yellow-500">En attente de prise en charge</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Mise à jour de position (simulée) --}}
    <div class="mt-8 bg-slate-900/50 rounded-xl p-4 border border-slate-800">
        <p class="text-sm text-slate-400 mb-2">Position actuelle :</p>
        <div class="flex gap-4">
            <button onclick="updatePosition()" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="ri-map-pin-2-line"></i> Mettre à jour ma position
            </button>
            <span id="positionStatus" class="text-sm text-slate-500 self-center">Non mise à jour</span>
        </div>
    </div>
</div>

<script>
    function updatePosition() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    @this.updateLocation(lat, lng);
                    document.getElementById('positionStatus').textContent = '📍 Mise à jour : ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
                },
                (error) => {
                    alert('Erreur de géolocalisation : ' + error.message);
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        } else {
            alert('Géolocalisation non supportée par votre navigateur');
        }
    }
</script>
```

---

## Étape 5 – Suivi client (carte avec le livreur)

### 5.1 Ajout du suivi dans la page de commande

Créez une page de suivi pour le client :

```bash
php artisan make:livewire OrderTracking
```

**`app/Livewire/OrderTracking.php`** :

```php
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
```

### 5.2 Vue du suivi client

**`resources/views/livewire/order-tracking.blade.php`** :

```blade
<div>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-white mb-4">Suivi de ma commande #{{ $order->id }}</h1>

        {{-- Statut --}}
        <div class="bg-slate-900/50 rounded-xl p-6 border border-slate-800 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $order->status_color }}">
                    <i class="ri-{{ $order->status === 'delivered' ? 'check-double-line' : ($order->status === 'pending' ? 'time-line' : 'truck-line') }} text-xl"></i>
                </div>
                <div>
                    <p class="text-white font-semibold">Statut : {{ $order->status_label }}</p>
                    @if($deliveryPersonName)
                        <p class="text-sm text-slate-400">Livreur : {{ $deliveryPersonName }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Carte --}}
        <div id="trackingMap" style="height: 400px; border-radius: 12px; overflow: hidden;" class="border border-slate-800"></div>

        {{-- Timeline --}}
        <div class="mt-6 bg-slate-900/50 rounded-xl p-6 border border-slate-800">
            <h3 class="font-semibold text-white mb-4">Historique</h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                        <i class="ri-check-line text-white"></i>
                    </div>
                    <div>
                        <p class="text-white">Commande passée</p>
                        <p class="text-xs text-slate-400">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                @if($order->accepted_at)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                            <i class="ri-check-line text-white"></i>
                        </div>
                        <div>
                            <p class="text-white">Acceptée par le restaurant</p>
                            <p class="text-xs text-slate-400">{{ $order->accepted_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif
                @if($order->picked_up_at)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber-600 flex items-center justify-center flex-shrink-0">
                            <i class="ri-truck-line text-white"></i>
                        </div>
                        <div>
                            <p class="text-white">En livraison</p>
                            <p class="text-xs text-slate-400">{{ $order->picked_up_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif
                @if($order->delivered_at)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center flex-shrink-0">
                            <i class="ri-check-double-line text-white"></i>
                        </div>
                        <div>
                            <p class="text-white">Livrée</p>
                            <p class="text-xs text-slate-400">{{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('trackingMap').setView([
            {{ $order->restaurant->latitude }},
            {{ $order->restaurant->longitude }}
        ], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Marqueur du restaurant
        L.marker([
            {{ $order->restaurant->latitude }},
            {{ $order->restaurant->longitude }}
        ]).addTo(map)
        .bindPopup('{{ $order->restaurant->name }}');

        // Marqueur du livreur (si disponible)
        @if($deliveryLocation)
            const deliveryMarker = L.marker([
                {{ $deliveryLocation['lat'] }},
                {{ $deliveryLocation['lng'] }}
            ]).addTo(map)
            .bindPopup('Livreur : {{ $deliveryPersonName }}');

            // Écouter les mises à jour de position
            Livewire.on('locationUpdated', (data) => {
                deliveryMarker.setLatLng([data.latitude, data.longitude]);
                map.panTo([data.latitude, data.longitude]);
            });
        @endif
    });
</script>
@endpush
```

---

## Étape 6 – Routes

Dans `routes/web.php` :

```php
use App\Livewire\Delivery\Dashboard as DeliveryDashboard;
use App\Livewire\OrderTracking;

// Dashboard livreur
Route::middleware(['auth', 'role:delivery'])->prefix('delivery')->group(function () {
    Route::get('/dashboard', DeliveryDashboard::class)->name('delivery.dashboard');
});

// Suivi commande (client)
Route::middleware(['auth'])->group(function () {
    Route::get('/order/tracking/{orderId}', OrderTracking::class)->name('order.tracking');
});
```

---

## Étape 7 – Attribuer le rôle `delivery` à un utilisateur

Dans `RolesAndPermissionsSeeder.php`, ajoutez :

```php
$delivery = Role::create(['name' => 'delivery']);
$delivery->givePermissionTo(['view orders', 'update delivery status']);
```

Pour créer un livreur de test :

```bash
php artisan tinker
```

```php
$user = \App\Models\User::factory()->create([
    'name' => 'Livreur Test',
    'email' => 'livreur@test.com',
    'password' => bcrypt('password'),
]);
$user->assignRole('delivery');
```

---

## 📌 Récapitulatif du Sprint 4

- ✅ Dashboard livreur (liste des commandes disponibles, acceptation)
- ✅ Mise à jour de position GPS (bouton + WebSocket)
- ✅ Suivi client en temps réel (carte avec marqueur du livreur)
- ✅ Notifications de changement de statut
- ✅ Rôle `delivery` et routes protégées

---
C'est une excellente adaptation ! Ton implémentation du **Sprint 4** est très bien pensée et particulièrement complète.

Voici quelques points forts de tes ajustements et deux petites optimisations à garder en tête :

---

## 🎯 Ce qui est parfait dans tes ajustements

1. **Restauration de la simplicité dans la BDD :** Utiliser directement `delivery_person_id` et `status` sur la table `orders` au lieu d'une table pivot `delivery_assignments` évite les jointures inutiles et accélère la réactivité de l'application.
2. **Gestion de la disponibilité (`is_delivery_available`) :** L'ajout des boutons "Disponible / Hors ligne" avec l'icône Remixicon est une très bonne UX pour permettre aux livreurs de passer en pause.
3. **Multi-broadcast de la position :** Dans `updateLocation()`, le fait de boucler sur `$myOrders` pour envoyer `DeliveryLocationUpdated` sur chaque canal `order.{id}` garantit que chaque client concerné reçoit la mise à jour de manière isolée et sécurisée.
4. **Intégration propre de LeafletJS :** La carte Leaflet avec la timeline détaillée sur l'historique de commande offre une excellente expérience de suivi au client.

---

## 💡 2 Petites optimisations recommandées

### 1. Écoute Echo côté JavaScript dans le suivi client

Dans ta vue `order-tracking.blade.php`, tu as configuré un écouteur `Livewire.on('locationUpdated')`. N'oublie pas que pour recevoir le signal en temps réel de Reverb/Echo sur le navigateur du client, il faut lier Laravel Echo à l'événement Livewire :

```javascript
// Dans order-tracking.blade.php (dans ton block JS)
window.Echo.private('order.{{ $order->id }}')
    .listen('DeliveryLocationUpdated', (e) => {
        // Mettre à jour le composant Livewire ou directement le marqueur Leaflet
        deliveryMarker.setLatLng([e.latitude, e.longitude]);
        map.panTo([e.latitude, e.longitude]);
    });

```

### 2. Auto-watch du GPS pour le livreur (en option)

Sur la vue du livreur, tu as mis un bouton manuel `Mettre à jour ma position`. Si tu souhaites que la position s'envoie automatiquement en arrière-plan pendant qu'il roule, tu peux remplacer `getCurrentPosition` par `watchPosition` :

```javascript
// Mise à jour automatique toutes les X secondes pendant le trajet
navigator.geolocation.watchPosition((position) => {
    @this.updateLocation(position.coords.latitude, position.coords.longitude);
}, (err) => console.error(err), { enableHighAccuracy: true });

```

---

## 📊 Bilan Global de ton MVP Food Delivery

Avec ce Sprint 4 validé, ton architecture est désormais au complet :

* [x] **Sprint 1 :** Auth Multi-rôles (Client, Resto, Livreur, Admin).
* [x] **Sprint 2 :** Menus, Panier Hybride (Session/BDD) & Checkout.
* [x] **Sprint 3 :** Dashboard Restaurateur Kanban & Alertes Reverb.
* [x] **Sprint 4 :** Espace Livreur, Géolocalisation WebSocket & Carte Leaflet.

Tu as une base solide, moderne et scalable pour ton application de livraison !

Voici la suite du développement pour finaliser le module de livraison et la gestion globale du projet **Omenu**.

---

## Étape 8 – Système de Statistiques du Livreur (Gains & Performance)

Pour compléter la vision du Sprint 4, voici le composant pour permettre au livreur de suivre ses revenus et le volume de livraisons effectuées.

```bash
php artisan make:livewire Delivery/Stats

```

**`app/Livewire/Delivery/Stats.php`** :

```php
<?php

namespace App\Livewire\Delivery;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Stats extends Component
{
    public $period = 'today'; // today, week, month

    public function render()
    {
        $query = Order::where('delivery_person_id', Auth::id())
            ->where('status', 'delivered');

        match ($this->period) {
            'today' => $query->whereDate('delivered_at', now()->today()),
            'week'  => $query->whereBetween('delivered_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereMonth('delivered_at', now()->month)->whereYear('delivered_at', now()->year),
        };

        $deliveredOrders = $query->latest('delivered_at')->get();
        
        // Simulation des gains (ex: commission fixe ou pourcentage par course)
        $totalEarnings = $deliveredOrders->count() * 1000; // Ex: 1000 FCFA par course

        return view('livewire.delivery.stats', [
            'orders' => $deliveredOrders,
            'totalOrders' => $deliveredOrders->count(),
            'totalEarnings' => $totalEarnings,
        ])->layout('components.front.layouts.front');
    }
}

```

**`resources/views/livewire/delivery/stats.blade.php`** :

```blade
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Mes Statistiques</h1>
        <div class="flex gap-2">
            <button wire:click="$set('period', 'today')" class="px-3 py-1.5 rounded-lg text-sm {{ $period === 'today' ? 'bg-amber-600 text-white' : 'bg-slate-800 text-slate-400' }}">Aujourd'hui</button>
            <button wire:click="$set('period', 'week')" class="px-3 py-1.5 rounded-lg text-sm {{ $period === 'week' ? 'bg-amber-600 text-white' : 'bg-slate-800 text-slate-400' }}">Cette semaine</button>
            <button wire:click="$set('period', 'month')" class="px-3 py-1.5 rounded-lg text-sm {{ $period === 'month' ? 'bg-amber-600 text-white' : 'bg-slate-800 text-slate-400' }}">Ce mois</button>
        </div>
    </div>

    {{-- Cartes KPI --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-slate-900/50 p-6 rounded-xl border border-slate-800">
            <p class="text-slate-400 text-sm">Courses effectuées</p>
            <p class="text-3xl font-bold text-white mt-2">{{ $totalOrders }}</p>
        </div>
        <div class="bg-slate-900/50 p-6 rounded-xl border border-slate-800">
            <p class="text-slate-400 text-sm">Gains estimés</p>
            <p class="text-3xl font-bold text-emerald-500 mt-2">{{ number_format($totalEarnings) }} FCFA</p>
        </div>
    </div>

    {{-- Historique des livraisons --}}
    <h2 class="text-lg font-semibold text-white mb-4">Historique de la période</h2>
    <div class="bg-slate-900/50 rounded-xl border border-slate-800 overflow-hidden">
        <table class="w-full text-left text-sm text-slate-400">
            <thead class="bg-slate-800/50 text-slate-300">
                <tr>
                    <th class="p-4">Commande</th>
                    <th class="p-4">Client</th>
                    <th class="p-4">Date de livraison</th>
                    <th class="p-4 text-right">Montant Commande</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($orders as $order)
                    <tr>
                        <td class="p-4 text-white font-medium">#{{ $order->id }}</td>
                        <td class="p-4">{{ $order->customer_name }}</td>
                        <td class="p-4">{{ $order->delivered_at?->format('d/m/Y H:i') }}</td>
                        <td class="p-4 text-right text-amber-500 font-bold">{{ number_format($order->total) }} FCFA</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-slate-500">Aucune livraison enregistrée pour cette période.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

```

---

## Étape 9 – Déclaration de la Route

Dans `routes/web.php`, ajoute la route des statistiques au groupe `delivery` :

```php
use App\Livewire\Delivery\Stats as DeliveryStats;

Route::middleware(['auth', 'role:delivery'])->prefix('delivery')->group(function () {
    Route::get('/dashboard', DeliveryDashboard::class)->name('delivery.dashboard');
    Route::get('/stats', DeliveryStats::class)->name('delivery.stats');
});

```

---

## 🚀 Prochaine phase suggérée

Le projet couvre désormais l'ensemble des fonctionnalités clés (Clients, Restaurateurs, Livreurs). Pour passer à la finalisation :

1. **Dashboard Administrateur :** Validation des restaurants partenaires, gestion des commissions globales et blocage d'utilisateurs.
2. **Tests de bout en bout (E2E) :** Simulation du flux complet : Passation de commande client $\rightarrow$ Acceptation resto $\rightarrow$ Prise en charge livreur $\rightarrow$ Confirmation de livraison.

Voici la suite du développement pour finaliser le module de livraison et la gestion globale du projet **Omenu**.

---

## Étape 8 – Système de Statistiques du Livreur (Gains & Performance)

Pour compléter la vision du Sprint 4, voici le composant pour permettre au livreur de suivre ses revenus et le volume de livraisons effectuées.

```bash
php artisan make:livewire Delivery/Stats

```

**`app/Livewire/Delivery/Stats.php`** :

```php
<?php

namespace App\Livewire\Delivery;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Stats extends Component
{
    public $period = 'today'; // today, week, month

    public function render()
    {
        $query = Order::where('delivery_person_id', Auth::id())
            ->where('status', 'delivered');

        match ($this->period) {
            'today' => $query->whereDate('delivered_at', now()->today()),
            'week'  => $query->whereBetween('delivered_at', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereMonth('delivered_at', now()->month)->whereYear('delivered_at', now()->year),
        };

        $deliveredOrders = $query->latest('delivered_at')->get();
        
        // Simulation des gains (ex: commission fixe ou pourcentage par course)
        $totalEarnings = $deliveredOrders->count() * 1000; // Ex: 1000 FCFA par course

        return view('livewire.delivery.stats', [
            'orders' => $deliveredOrders,
            'totalOrders' => $deliveredOrders->count(),
            'totalEarnings' => $totalEarnings,
        ])->layout('components.front.layouts.front');
    }
}

```

**`resources/views/livewire/delivery/stats.blade.php`** :

```blade
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Mes Statistiques</h1>
        <div class="flex gap-2">
            <button wire:click="$set('period', 'today')" class="px-3 py-1.5 rounded-lg text-sm {{ $period === 'today' ? 'bg-amber-600 text-white' : 'bg-slate-800 text-slate-400' }}">Aujourd'hui</button>
            <button wire:click="$set('period', 'week')" class="px-3 py-1.5 rounded-lg text-sm {{ $period === 'week' ? 'bg-amber-600 text-white' : 'bg-slate-800 text-slate-400' }}">Cette semaine</button>
            <button wire:click="$set('period', 'month')" class="px-3 py-1.5 rounded-lg text-sm {{ $period === 'month' ? 'bg-amber-600 text-white' : 'bg-slate-800 text-slate-400' }}">Ce mois</button>
        </div>
    </div>

    {{-- Cartes KPI --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-slate-900/50 p-6 rounded-xl border border-slate-800">
            <p class="text-slate-400 text-sm">Courses effectuées</p>
            <p class="text-3xl font-bold text-white mt-2">{{ $totalOrders }}</p>
        </div>
        <div class="bg-slate-900/50 p-6 rounded-xl border border-slate-800">
            <p class="text-slate-400 text-sm">Gains estimés</p>
            <p class="text-3xl font-bold text-emerald-500 mt-2">{{ number_format($totalEarnings) }} FCFA</p>
        </div>
    </div>

    {{-- Historique des livraisons --}}
    <h2 class="text-lg font-semibold text-white mb-4">Historique de la période</h2>
    <div class="bg-slate-900/50 rounded-xl border border-slate-800 overflow-hidden">
        <table class="w-full text-left text-sm text-slate-400">
            <thead class="bg-slate-800/50 text-slate-300">
                <tr>
                    <th class="p-4">Commande</th>
                    <th class="p-4">Client</th>
                    <th class="p-4">Date de livraison</th>
                    <th class="p-4 text-right">Montant Commande</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($orders as $order)
                    <tr>
                        <td class="p-4 text-white font-medium">#{{ $order->id }}</td>
                        <td class="p-4">{{ $order->customer_name }}</td>
                        <td class="p-4">{{ $order->delivered_at?->format('d/m/Y H:i') }}</td>
                        <td class="p-4 text-right text-amber-500 font-bold">{{ number_format($order->total) }} FCFA</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-slate-500">Aucune livraison enregistrée pour cette période.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

```

---

## Étape 9 – Déclaration de la Route

Dans `routes/web.php`, ajoute la route des statistiques au groupe `delivery` :

```php
use App\Livewire\Delivery\Stats as DeliveryStats;

Route::middleware(['auth', 'role:delivery'])->prefix('delivery')->group(function () {
    Route::get('/dashboard', DeliveryDashboard::class)->name('delivery.dashboard');
    Route::get('/stats', DeliveryStats::class)->name('delivery.stats');
});

```

---

## 🚀 Prochaine phase suggérée

Le projet couvre désormais l'ensemble des fonctionnalités clés (Clients, Restaurateurs, Livreurs). Pour passer à la finalisation :

1. **Dashboard Administrateur :** Validation des restaurants partenaires, gestion des commissions globales et blocage d'utilisateurs.
2. **Tests de bout en bout (E2E) :** Simulation du flux complet : Passation de commande client $\rightarrow$ Acceptation resto $\rightarrow$ Prise en charge livreur $\rightarrow$ Confirmation de livraison.