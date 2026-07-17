@extends('components.front.layouts.front')

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100">
    
    {{-- 1. Hero Header du Restaurant --}}
    <div class="relative h-[350px] md:h-[450px] overflow-hidden">
        {{-- Image de couverture du restaurant (ou image par défaut élégante) --}}
        @if($restaurant->cover_image)
            <img src="{{ asset('storage/' . $restaurant->cover_image) }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 flex items-center justify-center opacity-85">
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-amber-500/5 via-transparent to-transparent"></div>
                <i class="ri-restaurant-2-line text-8xl text-slate-800/60 animate-pulse"></i>
            </div>
        @endif
        
        {{-- Overlay sombre dégradé --}}
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>

        {{-- Infos du Restaurant sur le Hero --}}
        <div class="absolute bottom-0 inset-x-0 pb-10">
            <div class="container mx-auto px-4 md:px-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs uppercase tracking-widest font-semibold text-amber-500 hover:text-amber-400 transition-colors mb-4 group">
                    <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> Explorer les adresses
                </a>
                
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <span class="px-3 py-1 bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs uppercase font-bold tracking-wider rounded-full">
                            {{ $restaurant->cuisine_type }}
                        </span>
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-serif font-bold text-slate-100 tracking-wide mt-3 mb-2">
                            {{ $restaurant->name }}
                        </h1>
                        <p class="text-slate-400 text-sm md:text-base max-w-2xl font-serif italic mb-4">
                            "{{ $restaurant->description ?? 'Une expérience gastronomique d\'exception vous attend.' }}"
                        </p>
                        
                        <div class="flex flex-wrap items-center gap-y-2 gap-x-6 text-xs text-slate-300 font-medium">
                            <span class="flex items-center gap-1.5">
                                <i class="ri-map-pin-line text-amber-500 text-sm"></i> 
                                {{ $restaurant->address }}, {{ $restaurant->city }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <i class="ri-price-tag-3-line text-amber-500 text-sm"></i> 
                                Gamme : <span class="font-bold text-amber-400">{{ $restaurant->price_range }}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Corps de la page --}}
    <div class="container mx-auto px-4 md:px-8 py-12">
        <div class="grid lg:grid-cols-3 gap-12">
            
            {{-- Colonne Principale : Menu du Restaurant --}}
            <div class="lg:col-span-2 space-y-10">
                <div>
                    <h2 class="text-2xl font-serif font-bold text-slate-100 tracking-wider mb-2 flex items-center gap-3">
                        <span class="w-8 h-px bg-amber-500/50"></span> Notre Carte Gastronomique
                    </h2>
                    <p class="text-xs text-slate-500 uppercase tracking-widest font-semibold pl-11">Plats préparés à la commande par notre Chef</p>
                </div>

                {{-- Liste de tous les plats du restaurant --}}
                <div class="grid sm:grid-cols-2 gap-6">
                    @forelse($restaurant->menus as $menu)
                        <div class="bg-slate-900/40 border border-slate-900 hover:border-amber-500/20 rounded-2xl p-4 flex flex-col justify-between transition-all duration-300 group shadow-lg">
                            <div class="flex gap-4">
                                {{-- Image du plat ou image par défaut premium --}}
                                <div class="w-20 h-20 bg-slate-950 rounded-xl overflow-hidden flex-shrink-0 border border-slate-800 flex items-center justify-center">
                                    @if($menu->image)
                                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        {{-- Image SVG par défaut élégante selon la catégorie --}}
                                        <div class="w-full h-full flex items-center justify-center bg-slate-950 text-slate-800">
                                            <i class="ri-restaurant-line text-2xl text-amber-500/30"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <span class="text-[10px] uppercase font-bold tracking-wider text-amber-500/80">
                                        {{ $menu->category ?? 'Plat signature' }}
                                    </span>
                                    <h3 class="font-serif font-semibold text-slate-200 text-base mt-0.5 group-hover:text-amber-400 transition-colors truncate">
                                        {{ $menu->name }}
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                        {{ $menu->description ?? 'Préparation raffinée élaborée à partir d\'ingrédients de premier choix.' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-900">
                                <span class="text-amber-500 font-mono font-bold text-sm">
                                    {{ number_format($menu->price) }} FCFA
                                </span>

                                @if($menu->is_available ?? true)
                                    <button
                                        wire:click="$dispatch('addToCart', { menuId: {{ $menu->id }} })"
                                        class="px-4 py-1.5 rounded-lg bg-slate-950 hover:bg-amber-600 text-slate-300 hover:text-slate-950 border border-slate-800 hover:border-amber-600 font-semibold text-xs transition-all duration-300">
                                        Ajouter au panier
                                    </button>
                                @else
                                    <span class="text-[10px] uppercase tracking-wider text-rose-500 font-semibold">Indisponible</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center border border-dashed border-slate-800 rounded-2xl">
                            <i class="ri-compass-3-line text-5xl text-slate-700 block mb-4"></i>
                            <h3 class="font-serif font-semibold text-slate-400 text-lg">La carte est en cours de création</h3>
                            <p class="text-xs text-slate-600 mt-1">Revenez très bientôt pour découvrir nos créations culinaires.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Colonne Latérale : Suggestions directement ajustées dans le code --}}
            <div class="lg:col-span-1">
                <div class="bg-slate-900/50 border border-amber-500/5 rounded-2xl p-6 shadow-xl sticky top-28 space-y-6">
                    <div>
                        <h3 class="text-lg font-serif font-bold text-slate-100 flex items-center gap-2">
                            <i class="ri-sparkling-2-line text-amber-500"></i> Suggestions du Chef
                        </h3>
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-semibold mt-1">Sélections recommandées par l'établissement</p>
                    </div>

                    {{-- Liste de suggestions codées en dur --}}
                    <div class="space-y-4">
                        
                        {{-- Suggestion 1 --}}
                        <div class="flex items-center gap-3 p-3 bg-slate-950/60 rounded-xl border border-slate-900">
                            <div class="w-12 h-12 bg-slate-900 rounded-lg flex-shrink-0 flex items-center justify-center">
                                <i class="ri-goblet-line text-xl text-amber-500"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-serif font-bold text-slate-200 truncate">Sélection de Vin du Sommelier</h4>
                                <p class="text-[10px] text-slate-500 line-clamp-1">L'accord parfait pour sublimer votre dîner</p>
                                <span class="text-[11px] font-mono font-bold text-amber-500 mt-0.5 block">À la carte</span>
                            </div>
                        </div>

                        {{-- Suggestion 2 --}}
                        <div class="flex items-center gap-3 p-3 bg-slate-950/60 rounded-xl border border-slate-900">
                            <div class="w-12 h-12 bg-slate-900 rounded-lg flex-shrink-0 flex items-center justify-center">
                                <i class="ri-cake-3-line text-xl text-amber-500"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-serif font-bold text-slate-200 truncate">Le Secret Chocolaté de Saison</h4>
                                <p class="text-[10px] text-slate-500 line-clamp-1">Mousse au chocolat grand cru et éclats de fèves</p>
                                <span class="text-[11px] font-mono font-bold text-amber-500 mt-0.5 block">Dessert du moment</span>
                            </div>
                        </div>

                        {{-- Suggestion 3 --}}
                        <div class="flex items-center gap-3 p-3 bg-slate-950/60 rounded-xl border border-slate-900">
                            <div class="w-12 h-12 bg-slate-900 rounded-lg flex-shrink-0 flex items-center justify-center">
                                <i class="ri-leaf-line text-xl text-amber-500"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-serif font-bold text-slate-200 truncate">Option "Chef d'Œuvre" Bio</h4>
                                <p class="text-[10px] text-slate-500 line-clamp-1">Certains de nos plats sont déclinables en version végétarienne</p>
                                <span class="text-[11px] font-mono font-bold text-amber-500 mt-0.5 block">Sur demande</span>
                            </div>
                        </div>

                    </div>

                    {{-- Petit encart informatif sur la livraison --}}
                    <div class="p-4 bg-amber-500/[0.02] border border-amber-500/10 rounded-xl flex gap-3 text-xs text-slate-400">
                        <i class="ri-shield-user-line text-amber-500 text-lg flex-shrink-0"></i>
                        <div>
                            <h4 class="font-serif font-bold text-slate-200 mb-0.5">Service de livraison d'Élite</h4>
                            <p class="leading-relaxed text-[11px]">Notre propre équipe de coursiers assure un acheminement sécurisé de vos plats.</p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection