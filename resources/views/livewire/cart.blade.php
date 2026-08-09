<div
    x-show="open"
    @click.away="open = false"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-2 scale-95"
    class="absolute right-0 mt-3 w-96 max-w-[calc(100vw-2rem)] bg-[#0d1f38] rounded-2xl border border-amber-500/10 shadow-2xl shadow-black/30 z-50 overflow-hidden"
    style="display: none;"
>

    {{-- ============================================================
         EN-TÊTE
         ============================================================ --}}
    <div class="p-4 border-b border-amber-500/10 bg-[#0a1628]/80 flex justify-between items-center">

        <div>
            <h3 class="font-serif text-lg font-bold text-amber-500">
                Votre Sélection
            </h3>

            @if($restaurantName)
                <p class="text-[10px] text-slate-500 mt-0.5 truncate max-w-[220px]">
                    {{ $restaurantName }}
                </p>
            @endif
        </div>

        <span class="text-xs font-mono font-medium text-slate-400">
            {{ $itemCount }} {{ $itemCount > 1 ? 'Plats' : 'Plat' }}
        </span>
    </div>


    {{-- ============================================================
         LISTE DES ARTICLES
         ============================================================ --}}
    <div class="overflow-y-auto max-h-[340px] p-4 space-y-3">

        @forelse($cart as $id => $item)

            <div
                wire:key="cart-item-{{ $id }}"
                class="group flex items-center gap-3 pb-3 border-b border-slate-800/40 last:border-0"
            >

                {{-- Image --}}
                <div class="w-14 h-14 bg-slate-950 rounded-xl flex-shrink-0 overflow-hidden border border-amber-500/10 flex items-center justify-center">

                    @if(!empty($item['image']))
                        <img
                            src="{{ asset('storage/' . $item['image']) }}"
                            alt="{{ $item['name'] ?? 'Plat' }}"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                    @else
                        <i class="ri-restaurant-line text-amber-500/40 text-xl"></i>
                    @endif

                </div>


                {{-- Informations du plat --}}
                <div class="flex-1 min-w-0">

                    <p class="text-sm font-semibold text-slate-100 truncate font-serif">
                        {{ $item['name'] ?? 'Plat' }}
                    </p>

                    <p class="text-[11px] text-slate-400 truncate mb-1">
                        {{ $item['restaurant_name'] ?? $restaurantName ?? 'Établissement' }}
                    </p>

                    <p class="text-xs font-mono font-semibold text-amber-400">
                        {{ number_format((float) ($item['price'] ?? 0), 0, ',', ' ') }} FCFA
                    </p>

                </div>


                {{-- Contrôle quantité --}}
                <div class="flex items-center bg-slate-950 rounded-lg border border-slate-800 px-1 py-0.5">

                    <button
                        type="button"
                        wire:click="updateQuantity({{ $id }}, {{ max(0, ($item['quantity'] ?? 1) - 1) }})"
                        wire:loading.attr="disabled"
                        wire:target="updateQuantity({{ $id }}, {{ max(0, ($item['quantity'] ?? 1) - 1) }})"
                        aria-label="Diminuer la quantité"
                        class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-amber-500 hover:bg-amber-500/10 transition-colors disabled:opacity-50"
                    >
                        <span aria-hidden="true">−</span>
                    </button>

                    <span
                        class="w-7 text-center text-xs font-mono font-bold text-slate-200"
                        aria-label="Quantité"
                    >
                        {{ $item['quantity'] ?? 1 }}
                    </span>

                    <button
                        type="button"
                        wire:click="updateQuantity({{ $id }}, {{ min(99, ($item['quantity'] ?? 1) + 1) }})"
                        wire:loading.attr="disabled"
                        wire:target="updateQuantity({{ $id }}, {{ min(99, ($item['quantity'] ?? 1) + 1) }})"
                        aria-label="Augmenter la quantité"
                        class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-amber-500 hover:bg-amber-500/10 transition-colors disabled:opacity-50"
                    >
                        <span aria-hidden="true">+</span>
                    </button>

                </div>


                {{-- Suppression --}}
                <button
                    type="button"
                    wire:click="removeItem({{ $id }})"
                    wire:loading.attr="disabled"
                    wire:target="removeItem({{ $id }})"
                    aria-label="Supprimer {{ $item['name'] ?? 'cet article' }}"
                    title="Supprimer"
                    class="w-7 h-7 flex-shrink-0 rounded-lg flex items-center justify-center text-slate-600 hover:text-rose-400 hover:bg-rose-500/10 transition-all duration-200 opacity-0 group-hover:opacity-100 focus:opacity-100"
                >
                    <i class="ri-delete-bin-line text-sm"></i>
                </button>

            </div>

        @empty

            {{-- Panier vide --}}
            <div class="text-center py-12 text-slate-500">

                <div class="mb-4">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-500/5 border border-amber-500/10 flex items-center justify-center">
                        <i class="ri-shopping-bag-3-line text-4xl text-amber-500/30"></i>
                    </div>
                </div>

                <p class="font-serif italic text-sm text-slate-300">
                    Votre panier est vide
                </p>

                <p class="text-xs text-slate-600 mt-1 px-4">
                    Laissez-vous tenter par nos adresses d'exception.
                </p>

            </div>

        @endforelse

    </div>


    {{-- ============================================================
         FOOTER / TOTAL
         ============================================================ --}}
    @if(count($cart) > 0)

        <div class="p-4 border-t border-amber-500/10 bg-[#0a1628]/90">

            {{-- Restaurant --}}
            @if($restaurantName)
                <div class="flex items-center gap-2 mb-3 text-[11px] text-slate-500">
                    <i class="ri-store-2-line text-amber-500/60"></i>

                    <span class="truncate">
                        Commande chez
                        <span class="text-slate-300 font-medium">
                            {{ $restaurantName }}
                        </span>
                    </span>
                </div>
            @endif


            {{-- Total --}}
            <div class="flex justify-between items-baseline mb-4">

                <span class="font-serif text-slate-400 text-sm">
                    Estimation total
                </span>

                <span class="text-xl font-mono font-bold text-amber-400">
                    {{ number_format($total, 0, ',', ' ') }} FCFA
                </span>

            </div>


            {{-- Actions --}}
            <div class="space-y-2">

                <a
                    href="{{ route('checkout') }}"
                    class="flex items-center justify-center gap-2 w-full text-center bg-gradient-to-r from-violet-600 to-cyan-600 hover:from-violet-700 hover:to-cyan-700 text-white text-xs font-bold py-3 px-4 rounded-xl shadow-lg shadow-violet-950/20 transition-all duration-300 hover:scale-[1.01] active:scale-[0.99]"
                >
                    <i class="ri-secure-payment-line"></i>
                    <span>Accéder au paiement</span>
                </a>

                <button
                    type="button"
                    wire:click="clearCart"
                    wire:loading.attr="disabled"
                    wire:target="clearCart"
                    class="w-full flex items-center justify-center gap-1.5 text-[11px] text-slate-500 hover:text-rose-400 transition-colors py-1 disabled:opacity-50"
                >
                    <i class="ri-delete-bin-6-line"></i>
                    <span>Vider le panier</span>
                </button>

            </div>

        </div>

    @endif

</div>