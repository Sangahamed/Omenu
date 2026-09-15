<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                {{-- Avatar initiales --}}
                <div class="w-12 h-12 rounded-full bg-brand-black flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-display font-semibold text-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <h1 class="font-display font-semibold text-xl text-brand-black tracking-tight">
                        Bonjour, {{ $user->name }} 👋
                    </h1>
                    <p class="text-xs text-ink-soft mt-0.5">
                        {{ $user->email }} · Membre depuis {{ $user->created_at->format('M Y') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-brand-black hover:bg-brand-black-2 text-white text-xs px-4 py-2.5 rounded-sm font-semibold transition-all">
                    <i class="ri-compass-3-line text-sm"></i>
                    <span>Explorer la carte</span>
                </a>
                @if(! $user->hasRole('restaurant'))
                    <a href="{{ route('restaurants.create') }}" class="inline-flex items-center gap-2 border border-brand-red text-brand-red hover:bg-brand-red-soft text-xs px-4 py-2 rounded-sm font-semibold transition-all">
                        <i class="ri-store-2-line text-sm"></i>
                        <span>Devenir partenaire</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Cartes de synthèse --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-md border border-border relative overflow-hidden group hover:shadow-sm transition-shadow">
                    <div class="absolute top-0 left-0 w-1 h-full bg-brand-black"></div>
                    <div class="pl-3">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs text-ink-soft uppercase font-semibold tracking-wide">Total commandes</span>
                            <div class="w-8 h-8 rounded-sm bg-[#EFEFEC] flex items-center justify-center">
                                <i class="ri-shopping-bag-3-line text-sm text-ink"></i>
                            </div>
                        </div>
                        <p class="text-3xl font-display font-bold text-brand-black">{{ $stats['total_orders'] }}</p>
                        <p class="text-xs text-ink-soft mt-1">commandes passées</p>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-md border border-border relative overflow-hidden group hover:shadow-sm transition-shadow">
                    <div class="absolute top-0 left-0 w-1 h-full bg-brand-red"></div>
                    <div class="pl-3">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs text-ink-soft uppercase font-semibold tracking-wide">En cours</span>
                            <div class="w-8 h-8 rounded-sm bg-brand-red-soft flex items-center justify-center">
                                <i class="ri-time-line text-sm text-brand-red animate-pulse"></i>
                            </div>
                        </div>
                        <p class="text-3xl font-display font-bold text-brand-red">{{ $stats['active_orders'] }}</p>
                        <p class="text-xs text-ink-soft mt-1">en préparation/livraison</p>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-md border border-border relative overflow-hidden group hover:shadow-sm transition-shadow">
                    <div class="absolute top-0 left-0 w-1 h-full bg-[#E8B4B8]"></div>
                    <div class="pl-3">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs text-ink-soft uppercase font-semibold tracking-wide">Favoris</span>
                            <div class="w-8 h-8 rounded-sm bg-[#EFEFEC] flex items-center justify-center">
                                <i class="ri-heart-line text-sm text-ink"></i>
                            </div>
                        </div>
                        <p class="text-3xl font-display font-bold text-ink">{{ $stats['favorites'] }}</p>
                        <p class="text-xs text-ink-soft mt-1">restaurants sauvegardés</p>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-md border border-border relative overflow-hidden group hover:shadow-sm transition-shadow">
                    <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500"></div>
                    <div class="pl-3">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs text-ink-soft uppercase font-semibold tracking-wide">Total dépensé</span>
                            <div class="w-8 h-8 rounded-sm bg-emerald-50 flex items-center justify-center">
                                <i class="ri-money-cny-circle-line text-sm text-emerald-600"></i>
                            </div>
                        </div>
                        <p class="text-2xl font-display font-bold text-ink">
                            {{ number_format($stats['total_spent'], 0, ',', ' ') }}
                        </p>
                        <p class="text-xs text-ink-soft mt-1">FCFA en commandes livrées</p>
                    </div>
                </div>
            </div>

            {{-- Commandes actives en cours --}}
            @if($activeOrders->isNotEmpty())
                <div class="bg-white border border-brand-red/20 rounded-md overflow-hidden">
                    <div class="p-5 border-b border-border flex items-center gap-3 bg-brand-red-soft/20">
                        <span class="w-2.5 h-2.5 rounded-full bg-brand-red animate-pulse flex-shrink-0"></span>
                        <h2 class="font-display font-semibold text-base text-brand-black">
                            Commandes en cours ({{ $activeOrders->count() }})
                        </h2>
                        <span class="ml-auto text-xs text-ink-soft">Mise à jour en temps réel</span>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 p-5">
                        @foreach($activeOrders as $order)
                            <div class="border border-border rounded-sm p-4 flex flex-col gap-3 hover:border-brand-red/30 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-display font-semibold text-sm text-ink">
                                            Commande #{{ $order->id }}
                                        </p>
                                        <p class="text-xs text-ink-soft mt-0.5">
                                            Chez <span class="font-medium text-ink">{{ $order->restaurant?->name ?? 'Restaurant' }}</span>
                                        </p>
                                    </div>
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 whitespace-nowrap">
                                        {{ $order->status_label }}
                                    </span>
                                </div>

                                {{-- Barre de progression du statut --}}
                                @php
                                    $statuses = ['pending' => 0, 'accepted' => 1, 'preparing' => 2, 'ready' => 3, 'picked_up' => 4, 'delivered' => 5];
                                    $progress = ($statuses[$order->status] ?? 0) * 20;
                                @endphp
                                <div class="w-full bg-[#EFEFEC] rounded-full h-1.5">
                                    <div class="bg-brand-red h-1.5 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                                </div>

                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-ink-soft">
                                        <i class="ri-time-line"></i> {{ $order->created_at->diffForHumans() }}
                                    </span>
                                    <div class="flex items-center gap-3">
                                        <span class="font-mono font-bold text-brand-red">
                                            {{ number_format($order->total, 0, ',', ' ') }} FCFA
                                        </span>
                                        <a href="{{ route('order.tracking', $order->id) }}" class="inline-flex items-center gap-1 bg-brand-black hover:bg-brand-black-2 text-white text-xs px-3 py-1.5 rounded-sm font-semibold transition-colors">
                                            <i class="ri-truck-line"></i>
                                            <span>Suivre</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Layout en deux colonnes : historique + actions rapides --}}
            <div class="grid lg:grid-cols-3 gap-6">

                {{-- Historique des commandes (2/3) --}}
                <div class="lg:col-span-2 bg-white border border-border rounded-md overflow-hidden">
                    <div class="p-5 border-b border-border flex items-center justify-between">
                        <div>
                            <h2 class="font-display font-semibold text-base text-brand-black">
                                Historique des commandes
                            </h2>
                            <p class="text-xs text-ink-soft mt-0.5">Vos dernières commandes sur OMenu</p>
                        </div>
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-1 text-xs text-brand-red hover:text-brand-red-hover font-semibold">
                            Nouvelle commande
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </div>

                    @if($orders->isEmpty())
                        <div class="p-12 text-center">
                            <div class="w-16 h-16 mx-auto rounded-full bg-[#EFEFEC] flex items-center justify-center mb-3">
                                <i class="ri-shopping-bag-3-line text-2xl text-ink-soft"></i>
                            </div>
                            <h3 class="font-display font-semibold text-ink text-base">Aucune commande pour le moment</h3>
                            <p class="text-xs text-ink-soft mt-1 max-w-sm mx-auto">
                                Découvrez les restaurants gastronomiques et commandez vos plats favoris en quelques clics.
                            </p>
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mt-4 bg-brand-black hover:bg-brand-black-2 text-white text-xs px-4 py-2 rounded-sm font-semibold transition-all">
                                <i class="ri-compass-3-line"></i>
                                <span>Découvrir les adresses</span>
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-[#FBFBFA] border-b border-border text-xs text-ink-soft uppercase">
                                    <tr>
                                        <th class="py-3.5 px-5 font-semibold">Commande</th>
                                        <th class="py-3.5 px-5 font-semibold">Restaurant</th>
                                        <th class="py-3.5 px-5 font-semibold">Date</th>
                                        <th class="py-3.5 px-5 font-semibold">Montant</th>
                                        <th class="py-3.5 px-5 font-semibold">Statut</th>
                                        <th class="py-3.5 px-5 font-semibold text-right"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border text-ink">
                                    @foreach($orders as $order)
                                        <tr class="hover:bg-[#FBFBFA] transition-colors">
                                            <td class="py-3.5 px-5 font-semibold text-xs">
                                                #{{ $order->id }}
                                            </td>
                                            <td class="py-3.5 px-5 font-medium text-xs">
                                                {{ $order->restaurant?->name ?? 'N/A' }}
                                            </td>
                                            <td class="py-3.5 px-5 text-xs text-ink-soft">
                                                {{ $order->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="py-3.5 px-5 font-mono font-bold text-brand-red text-xs">
                                                {{ number_format($order->total, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td class="py-3.5 px-5">
                                                @php
                                                    $statusClass = match($order->status) {
                                                        'delivered' => 'bg-emerald-100 text-emerald-800',
                                                        'cancelled' => 'bg-red-100 text-red-800',
                                                        'pending' => 'bg-slate-100 text-slate-700',
                                                        default => 'bg-amber-100 text-amber-800',
                                                    };
                                                @endphp
                                                <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded-full {{ $statusClass }}">
                                                    {{ $order->status_label }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                                @if($order->restaurant)
                                                    <a href="{{ route('restaurants.show', $order->restaurant->slug) }}" class="inline-flex items-center gap-1 text-xs text-ink-soft hover:text-brand-red font-semibold transition-colors mr-3" title="Recommander chez {{ $order->restaurant->name }}">
                                                        <i class="ri-refresh-line"></i>
                                                        <span>Recommander</span>
                                                    </a>
                                                @endif
                                                <a href="{{ route('order.tracking', $order->id) }}" class="inline-flex items-center gap-1 text-xs text-brand-black hover:text-brand-red font-semibold transition-colors">
                                                    <span>Détails</span>
                                                    <i class="ri-arrow-right-s-line"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- Colonne droite : profil + actions rapides --}}
                <div class="flex flex-col gap-5">

                    {{-- Carte Profil --}}
                    <div class="bg-white border border-border rounded-md p-5 space-y-4">
                        <h3 class="font-display font-semibold text-sm text-brand-black">Mon profil</h3>
                        <div class="space-y-3 text-xs text-ink-soft">
                            <div class="flex items-center gap-2">
                                <i class="ri-user-line w-4 text-center"></i>
                                <span class="text-ink font-medium">{{ $user->name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="ri-mail-line w-4 text-center"></i>
                                <span>{{ $user->email }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="ri-shield-check-line w-4 text-center text-emerald-500"></i>
                                <span class="text-emerald-600 font-medium">Compte vérifié</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="ri-medal-line w-4 text-center text-brand-red"></i>
                                <span class="capitalize">
                                    @foreach($user->getRoleNames() as $role)
                                        {{ $role }}@if(!$loop->last), @endif
                                    @endforeach
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('profile') }}" class="inline-flex items-center gap-1.5 text-xs text-brand-red hover:text-brand-red-hover font-semibold mt-2">
                            <i class="ri-settings-3-line"></i>
                            <span>Modifier mon profil</span>
                        </a>
                    </div>

                    {{-- Mes favoris --}}
                    <div class="bg-white border border-border rounded-md overflow-hidden">
                        <div class="p-4 border-b border-border flex items-center justify-between">
                            <h3 class="font-display font-semibold text-sm text-brand-black flex items-center gap-2">
                                <i class="ri-heart-line text-brand-red"></i> Mes favoris
                            </h3>
                            <span class="text-xs text-ink-soft">{{ $favorites->count() }}</span>
                        </div>

                        @forelse($favorites as $restaurant)
                            <div class="flex items-center gap-3 p-3 border-b border-border last:border-b-0 hover:bg-[#FBFBFA] transition-colors">
                                <div class="w-11 h-11 rounded-sm bg-[#EFEFEC] overflow-hidden flex-shrink-0 flex items-center justify-center">
                                    @if($restaurant->cover_image)
                                        <img src="{{ asset('storage/'.$restaurant->cover_image) }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="ri-restaurant-2-line text-ink-soft"></i>
                                    @endif
                                </div>

                                <a href="{{ route('restaurants.show', $restaurant->slug) }}" class="flex-1 min-w-0 group">
                                    <p class="text-sm font-semibold text-ink group-hover:text-brand-red transition-colors truncate">
                                        {{ $restaurant->name }}
                                    </p>
                                    <p class="text-xs text-ink-soft truncate">
                                        {{ $restaurant->cuisine_type ?: 'Restaurant' }} · {{ $restaurant->city }}
                                    </p>
                                </a>

                                <livewire:favorite-toggle :restaurant-id="$restaurant->id" :compact="true" :key="'fav-'.$restaurant->id" />
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center">
                                <i class="ri-heart-add-line text-2xl text-ink-soft block mb-2"></i>
                                <p class="text-xs text-ink-soft leading-relaxed">
                                    Aucun favori pour l'instant.<br>
                                    Ajoutez un restaurant depuis sa fiche pour le retrouver ici.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Actions rapides --}}
                    <div class="bg-white border border-border rounded-md overflow-hidden">
                        <div class="p-4 border-b border-border">
                            <h3 class="font-display font-semibold text-sm text-brand-black">Accès rapide</h3>
                        </div>
                        <div class="divide-y divide-border">
                            <a href="{{ route('home') }}" class="flex items-center gap-3 p-4 hover:bg-[#FBFBFA] transition-colors group">
                                <div class="w-9 h-9 rounded-sm bg-[#EFEFEC] group-hover:bg-brand-black group-hover:text-white flex items-center justify-center text-ink transition-colors flex-shrink-0">
                                    <i class="ri-map-2-line text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-ink group-hover:text-brand-red transition-colors">Carte interactive</p>
                                    <p class="text-xs text-ink-soft">Localisez les restaurants</p>
                                </div>
                                <i class="ri-arrow-right-s-line text-ink-soft group-hover:text-brand-red transition-colors"></i>
                            </a>

                            <a href="{{ route('restaurants.index') }}" class="flex items-center gap-3 p-4 hover:bg-[#FBFBFA] transition-colors group">
                                <div class="w-9 h-9 rounded-sm bg-[#EFEFEC] group-hover:bg-brand-black group-hover:text-white flex items-center justify-center text-ink transition-colors flex-shrink-0">
                                    <i class="ri-restaurant-line text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-ink group-hover:text-brand-red transition-colors">Catalogue restaurants</p>
                                    <p class="text-xs text-ink-soft">Parcourir les menus</p>
                                </div>
                                <i class="ri-arrow-right-s-line text-ink-soft group-hover:text-brand-red transition-colors"></i>
                            </a>

                            <a href="{{ route('checkout') }}" class="flex items-center gap-3 p-4 hover:bg-[#FBFBFA] transition-colors group">
                                <div class="w-9 h-9 rounded-sm bg-[#EFEFEC] group-hover:bg-brand-black group-hover:text-white flex items-center justify-center text-ink transition-colors flex-shrink-0">
                                    <i class="ri-shopping-cart-2-line text-sm"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-ink group-hover:text-brand-red transition-colors">Mon panier</p>
                                    <p class="text-xs text-ink-soft">Finaliser ma commande</p>
                                </div>
                                <i class="ri-arrow-right-s-line text-ink-soft group-hover:text-brand-red transition-colors"></i>
                            </a>

                            @if(! $user->hasRole('restaurant'))
                                <a href="{{ route('restaurants.create') }}" class="flex items-center gap-3 p-4 hover:bg-[#FBFBFA] transition-colors group">
                                    <div class="w-9 h-9 rounded-sm bg-brand-red-soft group-hover:bg-brand-red group-hover:text-white flex items-center justify-center text-brand-red transition-colors flex-shrink-0">
                                        <i class="ri-store-2-line text-sm"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-ink group-hover:text-brand-red transition-colors">Devenir partenaire</p>
                                        <p class="text-xs text-ink-soft">Inscrire mon restaurant</p>
                                    </div>
                                    <i class="ri-arrow-right-s-line text-ink-soft group-hover:text-brand-red transition-colors"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Statistiques personnelles --}}
                    @if($stats['total_orders'] > 0)
                        <div class="bg-brand-black text-white rounded-md p-5 space-y-3">
                            <h3 class="font-display font-semibold text-sm">Votre fidélité</h3>
                            <p class="text-xs text-white/60">
                                Vous avez passé <span class="text-white font-semibold">{{ $stats['total_orders'] }} commande(s)</span>
                                sur OMenu depuis votre inscription.
                            </p>
                            @php
                                $deliveredCount = $stats['delivered_orders'];
                                $levelText = $deliveredCount >= 20 ? 'Ambassadeur 🥇' : ($deliveredCount >= 10 ? 'Régulier ⭐' : ($deliveredCount >= 5 ? 'Fidèle 🎖️' : 'Nouveau membre'));
                            @endphp
                            <div class="flex items-center gap-2 pt-2 border-t border-white/10">
                                <i class="ri-vip-crown-line text-brand-red"></i>
                                <span class="text-sm font-semibold">{{ $levelText }}</span>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
