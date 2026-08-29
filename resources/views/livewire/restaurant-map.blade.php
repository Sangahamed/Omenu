<div>
    <div class="flex flex-col h-screen bg-white text-ink">
        <div class="md:hidden bg-white border-b border-brand-black py-3 px-4 flex items-center justify-between z-50 flex-shrink-0">
            <h1 class="text-xl font-display font-semibold text-brand-black">O<span class="text-brand-red">Menu</span></h1>
            <button wire:click="toggleSidebar" class="text-brand-black p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <div class="flex flex-1 overflow-hidden relative min-h-0">
            <aside class="w-full md:w-80 bg-white border-r border-border z-40 transition-all duration-300 transform {{ $isSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0' }} absolute md:relative h-full overflow-y-auto">
                <div class="p-4 md:p-6 h-full flex flex-col justify-between">
                    <div>
                        <div class="hidden md:flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-display font-semibold text-brand-black">O<span class="text-brand-red">Menu</span></h1>
                        </div>

                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-sm uppercase tracking-widest font-semibold text-ink-soft">Filtres</h2>
                            <button wire:click="toggleSidebar" class="md:hidden text-ink-soft hover:text-brand-black">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-medium text-ink-soft mb-1">Rechercher</label>
                            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Restaurant, cuisine..."
                                class="w-full px-4 py-2 bg-white border border-border rounded-sm focus:ring-1 focus:ring-brand-red focus:border-brand-red text-ink placeholder-ink-soft">
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-ink-soft mb-1">Type de cuisine</label>
                                <select wire:model.live.debounce.300ms="cuisine" class="w-full bg-white border-border text-ink rounded-sm focus:ring-brand-red focus:border-brand-red">
                                    <option value="">Toutes</option>
                                    @foreach($cuisines as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-ink-soft mb-1">Ville</label>
                                <input type="text" wire:model.live.debounce.400ms="city" placeholder="Ex: Abidjan..." class="w-full bg-white border-border text-ink rounded-sm focus:ring-brand-red focus:border-brand-red">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-ink-soft mb-1">Gamme de prix</label>
                                <select wire:model.live.debounce.300ms="priceRange" class="w-full bg-white border-border text-ink rounded-sm focus:ring-brand-red focus:border-brand-red">
                                    <option value="">Tous</option>
                                    <option value="€">CFA (Économique)</option>
                                    <option value="€€">CFA (Standard)</option>
                                    <option value="€€€">CFA (Premium)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        <button wire:click="resetFilters" class="w-full bg-white hover:bg-brand-black hover:text-white border border-brand-black text-brand-black py-2 px-4 rounded-sm transition text-sm">
                            Réinitialiser les filtres
                        </button>
                        <div class="p-3 bg-white border border-border rounded-sm text-center">
                            <div class="text-xs text-ink-soft">
                                <span class="font-bold text-brand-red text-sm">{{ $total }}</span> établissements trouvés
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="flex-1 relative min-h-0 bg-white">
                <div class="absolute top-4 left-4 z-30">
                    <button wire:click="toggleViewMode" class="flex items-center space-x-2 bg-white/95 backdrop-blur border border-brand-black text-ink px-4 py-2 rounded-sm shadow-sm transition">
                        @if($viewMode === 'map')
                            <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            <span class="text-xs font-medium">Passer en vue Liste</span>
                        @else
                            <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                            <span class="text-xs font-medium">Passer en vue Carte</span>
                        @endif
                    </button>
                </div>

                <div class="h-full w-full relative {{ $viewMode === 'map' ? '' : 'hidden invisible absolute' }}">
                    <div id="map" wire:ignore class="h-full w-full z-10"></div>
                </div>

                <div class="h-full overflow-y-auto bg-white {{ $viewMode === 'list' ? '' : 'hidden' }}">
                    <div class="p-6 pt-20 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($menus as $menu)
                            <div class="plat-card bg-white border border-border rounded-md overflow-hidden group hover:border-brand-black transition-all duration-300 flex flex-col justify-between">
                                <div class="relative h-44 overflow-hidden bg-[#EFEFEC]">
                                    @if($menu['image'])
                                        <img src="{{ $menu['image'] }}" alt="{{ $menu['name'] }}" class="w-full h-full object-cover grayscale-[0.15] group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-ink-soft bg-[#EFEFEC]">
                                            <i class="ri-restaurant-2-line text-4xl"></i>
                                        </div>
                                    @endif
                                    <div class="absolute top-2 left-2 bg-brand-black text-white text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-sm">
                                        {{ $menu['category'] ?? 'Plat' }}
                                    </div>
                                    @if(!$menu['is_available'])
                                        <div class="absolute inset-0 bg-brand-black/80 backdrop-blur-xs flex items-center justify-center text-white font-medium text-sm tracking-wider uppercase">
                                            Indisponible
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4 flex-1 flex flex-col justify-between">
                                    <div>
                                        <h3 class="font-display font-medium text-lg text-ink group-hover:text-brand-red transition-colors line-clamp-1">
                                            {{ $menu['name'] }}
                                        </h3>
                                        <p class="text-xs text-ink-soft mt-1 mb-3 line-clamp-2">{{ $menu['description'] ?? 'Aucune description disponible.' }}</p>
                                    </div>

                                    <div>
                                        <div class="border-t border-border pt-3 space-y-3">
                                            <div class="flex items-center justify-between gap-3">
                                                <span class="text-brand-red font-display font-bold text-lg">
                                                    {{ $menu['price'] }} FCFA
                                                </span>

                                                @if($menu['is_available'])
                                                    <button
                                                        wire:click="$dispatch('addToCart', { menuId: {{ $menu['id'] }} })"
                                                        class="px-5 py-2.5 rounded-sm bg-brand-black hover:bg-brand-black-2 text-white font-semibold transition-all duration-300">
                                                        Commander
                                                    </button>
                                                @else
                                                    <button
                                                        disabled
                                                        class="px-5 py-2.5 rounded-sm bg-[#EFEFEC] text-ink-soft cursor-not-allowed">
                                                        Indisponible
                                                    </button>
                                                @endif
                                            </div>

                                            <a href="{{ route('restaurants.show', $menu['restaurant_slug']) }}"
                                               class="flex items-center gap-1 text-xs text-ink-soft hover:text-brand-black transition-colors truncate">
                                                <i class="ri-map-pin-2-line text-brand-red"></i>
                                                {{ $menu['restaurant_name'] }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-24">
                                <div class="mb-4">
                                    <i class="ri-search-eye-line text-5xl text-ink-soft/40"></i>
                                </div>
                                <h3 class="text-lg font-medium text-ink-soft">Aucune proposition culinaire</h3>
                                <p class="text-xs text-ink-soft mt-1">Modifiez vos critères de filtrage pour élargir la recherche.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>
