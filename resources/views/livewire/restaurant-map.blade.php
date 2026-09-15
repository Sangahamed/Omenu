<div>
    <div class="flex flex-col h-screen bg-slate-50 text-slate-900 font-sans">
        <div class="flex flex-1 overflow-hidden relative min-h-0">
            <!-- Overlay mobile backdrop quand la sidebar est ouverte -->
            @if($isSidebarOpen)
                <div wire:click="toggleSidebar" class="md:hidden fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40"></div>
            @endif

            <!-- Sidebar Filtres Moderne -->
            <aside class="w-full md:w-80 bg-white border-r border-slate-200 z-50 transition-all duration-300 transform {{ $isSidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0' }} absolute md:relative h-full overflow-y-auto shadow-2xl md:shadow-none">
                <div class="p-5 md:p-6 h-full flex flex-col justify-between">
                    <div>
                        <!-- Titre OMenu (Desktop seulement) -->

                        <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-3 md:border-none md:pb-0">
                            <h2 class="text-xs uppercase tracking-widest font-bold text-slate-400">Filtres de recherche</h2>
                            <button wire:click="toggleSidebar" class="md:hidden text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>

                        <!-- Recherche -->
                        <div class="mb-5">
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Rechercher</label>
                            <div class="relative">
                                <i class="ri-search-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" wire:model.live.debounce.400ms="search" placeholder="Restaurant, plat, cuisine..."
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-red/20 focus:border-brand-red text-slate-900 text-sm placeholder-slate-400 transition-all">
                            </div>
                        </div>

                        <!-- Filtres par catégorie -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Type de cuisine</label>
                                <select wire:model.live.debounce.300ms="cuisine" class="w-full bg-slate-50 border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-brand-red/20 focus:border-brand-red py-2.5 transition-all">
                                    <option value="">Toutes les cuisines</option>
                                    @foreach($cuisines as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Ville</label>
                                <div class="relative">
                                    <i class="ri-map-pin-line absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                    <input type="text" wire:model.live.debounce.400ms="city" placeholder="Ex: Abidjan, Cocody..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-brand-red/20 focus:border-brand-red transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 space-y-3.5 pt-6 border-t border-slate-100">
                        <button wire:click="resetFilters" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-800 py-2.5 px-4 rounded-xl transition text-xs font-semibold flex items-center justify-center gap-2">
                            <i class="ri-refresh-line text-sm"></i> Réinitialiser les filtres
                        </button>
                        <button wire:click="toggleSidebar" class="md:hidden w-full bg-slate-900 hover:bg-brand-red text-white py-2.5 px-4 rounded-xl transition text-xs font-bold shadow-md">
                            Voir les résultats ({{ $total }})
                        </button>
                        <div class="p-3 bg-red-50/60 border border-red-100 rounded-xl text-center hidden md:block">
                            <div class="text-xs text-slate-600 font-medium">
                                <span class="font-extrabold text-brand-red text-sm">{{ $total }}</span> établissements disponibles
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 relative min-h-0 bg-slate-50">
                <!-- Floating Toggle View Button (Desktop uniquement) -->
                <div class="hidden md:block absolute top-5 left-5 z-30">
                    <button wire:click="toggleViewMode" class="flex items-center space-x-2 bg-white/90 backdrop-blur-md border border-slate-200/80 text-slate-900 px-4 py-2.5 rounded-2xl shadow-lg hover:shadow-xl hover:bg-white transition-all duration-200">
                        @if($viewMode === 'map')
                            <i class="ri-list-check-2 text-brand-red text-base"></i>
                            <span class="text-xs font-bold">Vue Liste</span>
                        @else
                            <i class="ri-map-2-line text-brand-red text-base"></i>
                            <span class="text-xs font-bold">Vue Carte</span>
                        @endif
                    </button>
                </div>

                <!-- Vue Carte Leaflet -->
                <div class="h-full w-full relative {{ $viewMode === 'map' ? '' : 'hidden invisible absolute' }}">
                    <div id="map" wire:ignore class="h-full w-full z-10"></div>
                </div>

                <!-- Vue Liste (Cartes Modernes de Plats / Restaurants) -->
                <div class="h-full overflow-y-auto bg-slate-50/80 {{ $viewMode === 'list' ? '' : 'hidden' }}">
                    <div class="p-4 sm:p-6 pt-4 md:pt-24 pb-20 md:pb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 max-w-7xl mx-auto">
                        @forelse($menus as $menu)
                            <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">
                                <div class="relative h-48 overflow-hidden bg-slate-100">
                                    @if($menu['image'])
                                        <img src="{{ $menu['image'] }}" alt="{{ $menu['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300 bg-slate-100">
                                            <i class="ri-restaurant-2-line text-5xl"></i>
                                        </div>
                                    @endif
                                    
                                    <!-- Badge de Catégorie Floating -->
                                    <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-md text-slate-800 text-[11px] font-bold tracking-wide px-3 py-1 rounded-full shadow-sm">
                                        {{ $menu['category'] ?? 'Plat' }}
                                    </div>

                                    @if(!$menu['is_available'])
                                        <div class="absolute inset-0 bg-slate-900/75 backdrop-blur-xs flex items-center justify-center text-white font-bold text-xs tracking-wider uppercase">
                                            Indisponible
                                        </div>
                                    @endif
                                </div>

                                <div class="p-5 flex-1 flex flex-col justify-between">
                                    <div>
                                        <h3 class="font-display font-semibold text-base text-slate-900 group-hover:text-brand-red transition-colors line-clamp-1">
                                            {{ $menu['name'] }}
                                        </h3>
                                        <p class="text-xs text-slate-500 mt-1 mb-4 line-clamp-2 leading-relaxed">
                                            {{ $menu['description'] ?? 'Aucune description disponible pour ce plat.' }}
                                        </p>
                                    </div>

                                    <div class="border-t border-slate-100 pt-3 space-y-2">
                                        <div class="flex items-center justify-between gap-2 min-w-0">
                                            <div class="flex-shrink-0 whitespace-nowrap">
                                                <span class="text-brand-red font-display font-extrabold text-base sm:text-lg tracking-tight">
                                                    {{ $menu['price'] }}
                                                </span>
                                                <span class="text-[10px] font-bold text-slate-500 uppercase ml-0.5">FCFA</span>
                                            </div>

                                            @if($menu['is_available'])
                                                <button
                                                    wire:click="$dispatch('addToCart', { menuId: {{ $menu['id'] }} })"
                                                    class="flex-shrink-0 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-brand-red text-white font-semibold text-xs shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-1.5">
                                                    <i class="ri-shopping-bag-3-line text-sm"></i> <span>Commander</span>
                                                </button>
                                            @else
                                                <button
                                                    disabled
                                                    class="flex-shrink-0 px-3.5 py-2 rounded-xl bg-slate-100 text-slate-400 font-semibold text-xs cursor-not-allowed">
                                                    Indisponible
                                                </button>
                                            @endif
                                        </div>

                                        <a href="{{ route('restaurants.show', $menu['restaurant_slug']) }}"
                                           class="flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-900 transition-colors truncate pt-1">
                                            <i class="ri-map-pin-2-fill text-brand-red flex-shrink-0"></i>
                                            <span class="truncate">{{ $menu['restaurant_name'] }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-20 bg-white border border-slate-100 rounded-3xl shadow-sm my-6 p-8">
                                <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="ri-search-eye-line text-3xl text-brand-red"></i>
                                </div>
                                <h3 class="text-lg font-bold text-slate-900">Aucun plat disponible</h3>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Essayez de modifier vos critères de recherche ou de réinitialiser les filtres.</p>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>

        <!-- Navigation bar mobile façon app native (Bottom Navbar) -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 h-16 bg-white/95 backdrop-blur-md border-t border-slate-200 z-50 flex items-center justify-around shadow-2xl px-2">
            <!-- Onglet Carte -->
            <button wire:click="$set('viewMode', 'map')"
                    class="flex flex-col items-center justify-center flex-1 h-full py-1 text-xs font-semibold transition-all relative {{ $viewMode === 'map' ? 'text-brand-red' : 'text-slate-400 hover:text-slate-600' }}">
                <i class="ri-map-2-fill text-xl leading-none mb-0.5"></i>
                <span>Carte</span>
                @if($viewMode === 'map')
                    <span class="absolute bottom-1 w-1.5 h-1.5 rounded-full bg-brand-red"></span>
                @endif
            </button>

            <!-- Onglet Liste -->
            <button wire:click="$set('viewMode', 'list')"
                    class="flex flex-col items-center justify-center flex-1 h-full py-1 text-xs font-semibold transition-all relative {{ $viewMode === 'list' ? 'text-brand-red' : 'text-slate-400 hover:text-slate-600' }}">
                <i class="ri-list-check-2 text-xl leading-none mb-0.5"></i>
                <span>Liste</span>
                @if($viewMode === 'list')
                    <span class="absolute bottom-1 w-1.5 h-1.5 rounded-full bg-brand-red"></span>
                @endif
            </button>

            <!-- Onglet Filtres -->
            <button wire:click="toggleSidebar"
                    class="flex flex-col items-center justify-center flex-1 h-full py-1 text-xs font-semibold transition-all relative {{ $isSidebarOpen ? 'text-brand-red' : 'text-slate-400 hover:text-slate-600' }}">
                <div class="relative">
                    <i class="ri-filter-3-line text-xl leading-none"></i>
                    @if($search || $cuisine || $city)
                        <span class="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-brand-red ring-2 ring-white"></span>
                    @endif
                </div>
                <span class="mt-0.5">Filtres</span>
            </button>

            <!-- Onglet Menu (Navigation Générale du site) -->
            <button type="button" onclick="window.openVrMenu && window.openVrMenu()"
                    class="flex flex-col items-center justify-center flex-1 h-full py-1 text-xs font-semibold text-slate-400 hover:text-slate-600 transition-all">
                <i class="ri-menu-line text-xl leading-none mb-0.5"></i>
                <span>Menu</span>
            </button>
        </nav>
    </div>
</div>
