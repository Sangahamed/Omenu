<div>
    <div class="flex flex-col h-screen bg-slate-900 text-slate-100">
        <div class="md:hidden bg-slate-950 border-b border-amber-500/10 py-3 px-4 flex items-center justify-between z-50 flex-shrink-0">
            <h1 class="text-xl font-serif font-bold text-amber-500">Omenu</h1>
            <button wire:click="toggleSidebar" class="text-slate-400 p-2 hover:text-amber-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <div class="flex flex-1 overflow-hidden relative min-h-0">
            <aside class="w-full md:w-80 bg-slate-950 border-r border-amber-500/10 z-40 transition-all duration-300 transform {{ $isSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0' }} absolute md:relative h-full overflow-y-auto">
                <div class="p-4 md:p-6 h-full flex flex-col justify-between">
                    <div>
                        <div class="hidden md:flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-serif font-bold text-amber-500">Omenu</h1>
                        </div>

                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-sm uppercase tracking-widest font-semibold text-slate-400">Filtres</h2>
                            <button wire:click="toggleSidebar" class="md:hidden text-slate-400 hover:text-amber-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-medium text-slate-400 mb-1">Rechercher</label>
                            <input type="text" wire:model.live.debounce.400ms="search" placeholder="Restaurant, cuisine..."
                                class="w-full px-4 py-2 bg-slate-900 border border-slate-800 rounded-lg focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-slate-100 placeholder-slate-500">
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Type de cuisine</label>
                                <select wire:model.live.debounce.300ms="cuisine" class="w-full bg-slate-900 border-slate-800 text-slate-200 rounded-lg focus:ring-amber-500">
                                    <option value="">Toutes</option>
                                    <option value="italien">Italien</option>
                                    <option value="fast-food">Fast-food</option>
                                    <option value="africain">Africain</option>
                                    <option value="chinois">Chinois</option>
                                    <option value="libanais">Libanais</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Ville</label>
                                <input type="text" wire:model.live.debounce.400ms="city" placeholder="Ex: Abidjan..." class="w-full bg-slate-900 border-slate-800 text-slate-200 rounded-lg focus:ring-amber-500">
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-slate-400 mb-1">Gamme de prix</label>
                                <select wire:model.live.debounce.300ms="priceRange" class="w-full bg-slate-900 border-slate-800 text-slate-200 rounded-lg focus:ring-amber-500">
                                    <option value="">Tous</option>
                                    <option value="1">CFA (Économique)</option>
                                    <option value="2">CFA (Standard)</option>
                                    <option value="3">CFA (Premium)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        <button wire:click="resetFilters" class="w-full bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 py-2 px-4 rounded-lg transition text-sm">
                            Réinitialiser les filtres
                        </button>
                        <div class="p-3 bg-slate-900/50 border border-slate-800 rounded-lg text-center">
                            <div class="text-xs text-slate-400">
                                <span class="font-bold text-amber-500 text-sm">{{ $total }}</span> établissements trouvés
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="flex-1 relative min-h-0 bg-slate-950">
                <div class="absolute top-4 left-4 z-30">
                    <button wire:click="toggleViewMode" class="flex items-center space-x-2 bg-slate-900/90 backdrop-blur border border-amber-500/20 hover:border-amber-500 text-slate-200 px-4 py-2 rounded-xl shadow-2xl transition">
                        @if($viewMode === 'map')
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            <span class="text-xs font-medium">Passer en vue Liste</span>
                        @else
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                            <span class="text-xs font-medium">Passer en vue Carte</span>
                        @endif
                    </button>
                </div>

                <div class="h-full w-full relative {{ $viewMode === 'map' ? '' : 'hidden invisible absolute' }}">
                    <div id="map" wire:ignore class="h-full w-full z-10"></div>
                </div>

                <div class="h-full overflow-y-auto bg-slate-900/40 {{ $viewMode === 'list' ? '' : 'hidden' }}">
                    <div class="p-6 pt-20 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($menus as $menu)
                            <div class="bg-slate-950 border border-slate-800/80 rounded-2xl shadow-xl overflow-hidden group hover:border-amber-500/30 transition-all duration-300 flex flex-col justify-between">
                                <div class="relative h-44 overflow-hidden bg-slate-900">
                                    @if($menu['image'])
                                        <img src="{{ $menu['image'] }}" alt="{{ $menu['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-700 bg-slate-950/45">
                                            <i class="ri-restaurant-2-line text-4xl text-slate-800"></i>
                                        </div>
                                    @endif
                                    <div class="absolute top-2 left-2 bg-amber-500/90 text-slate-950 text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded">
                                        {{ $menu['category'] ?? 'Plat' }}
                                    </div>
                                    @if(!$menu['is_available'])
                                        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center text-rose-400 font-medium text-sm tracking-wider uppercase">
                                            Indisponible
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4 flex-1 flex flex-col justify-between">
                                    <div>
                                        <h3 class="font-serif font-medium text-lg text-slate-200 group-hover:text-amber-500 transition-colors line-clamp-1">
                                            {{ $menu['name'] }}
                                        </h3>
                                        <p class="text-xs text-slate-400 mt-1 mb-3 line-clamp-2">{{ $menu['description'] ?? 'Aucune description disponible.' }}</p>
                                    </div>
                                    
                                    <div>
                                        <div class="border-t border-slate-900 pt-3 space-y-3">
                                            <div class="flex items-center justify-between gap-3">
                                                <span class="text-amber-500 font-bold text-lg">
                                                    {{ $menu['price'] }} FCFA
                                                </span>

                                                @if($menu['is_available'])
                                                    <button
                                                        wire:click="$dispatch('addToCart', { menuId: {{ $menu['id'] }} })"
                                                        class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-cyan-600 hover:from-violet-700 hover:to-cyan-700 text-white font-semibold transition-all duration-300 hover:scale-[1.02] shadow-md shadow-violet-950/30">
                                                        Commander
                                                    </button>
                                                @else
                                                    <button
                                                        disabled
                                                        class="px-5 py-2.5 rounded-xl bg-slate-700 text-slate-400 cursor-not-allowed">
                                                        Indisponible
                                                    </button>
                                                @endif
                                            </div>

                                            <a href="{{ route('restaurants.show', $menu['restaurant_slug']) }}"
                                               class="flex items-center gap-1 text-xs text-slate-400 hover:text-amber-400 transition-colors truncate">
                                                <i class="ri-map-pin-2-line text-amber-500"></i>
                                                {{ $menu['restaurant_name'] }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-24">
                                <div class="mb-4">
                                    <i class="ri-search-eye-line text-5xl text-amber-500/30"></i>
                                </div>
                                <h3 class="text-lg font-medium text-slate-400">Aucune proposition culinaire</h3>
                                <p class="text-xs text-slate-500 mt-1">Modifiez vos critères de filtrage pour élargir la recherche.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>