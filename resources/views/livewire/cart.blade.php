<div class="relative" x-data="{ open: false }">
    {{-- Bouton Panier Premium --}}
    <button @click="open = !open" class="relative p-2.5 text-slate-400 hover:text-amber-400 rounded-xl hover:bg-slate-900/60 border border-transparent hover:border-amber-500/10 transition-all duration-300 focus:outline-none">
        <i class="ri-shopping-bag-3-line text-xl"></i>
        @if($itemCount > 0)
            <span class="absolute -top-1 -right-1 bg-gradient-to-r from-pink-500 to-rose-500 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center animate-pulse shadow-md">
                {{ $itemCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panier thémé Cyber-Luxe --}}
    <div x-show="open" 
         @click.away="open = false" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         class="absolute right-0 mt-3 w-96 bg-[#0d1f38] rounded-2xl border border-amber-500/10 shadow-2xl z-50 overflow-hidden" 
         style="display: none;">
         
        <div class="p-4 border-b border-amber-500/10 bg-[#0a1628]/80 flex justify-between items-center">
            <h3 class="font-serif text-lg font-bold text-amber-500">Votre Sélection</h3>
            <span class="text-xs font-mono font-medium text-slate-400">#{{ $itemCount }} Plats</span>
        </div>

        <div class="overflow-y-auto max-h-[340px] p-4 space-y-4 division-y divide-amber-500/5">
            @forelse($cart as $id => $item)
                <div class="flex items-center gap-3 pb-3 border-b border-slate-800/40 last:border-0">
                    <div class="w-14 h-14 bg-slate-950 rounded-xl flex-shrink-0 overflow-hidden border border-amber-500/10 flex items-center justify-center">
                        @if($item['image'])
                            <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                        @else
                            <i class="ri-restaurant-line text-amber-500/40 text-xl"></i>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-100 truncate font-serif">{{ $item['name'] }}</p>
                        <p class="text-[11px] text-slate-400 truncate mb-1">{{ $item['restaurant_name'] ?? 'Restaurant' }}</p>
                        <p class="text-xs font-mono font-semibold text-amber-400">{{ number_format($item['price']) }} FCFA</p>
                    </div>

                    <div class="flex items-center bg-slate-950 rounded-lg border border-slate-800 px-1 py-0.5">
                        <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})" class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-amber-500 transition-colors">-</button>
                        <span class="w-6 text-center text-xs font-mono font-bold text-slate-200">{{ $item['quantity'] }}</span>
                        <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})" class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-amber-500 transition-colors">+</button>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-slate-500">
                    <div class="mb-4">
                        <i class="ri-shopping-bag-3-line text-5xl text-amber-500/30"></i>
                    </div>
                    <p class="font-serif italic text-sm text-slate-300">Votre panier est vide</p>
                    <p class="text-xs text-slate-600 mt-1">Laissez-vous tenter par nos adresses d'exception.</p>
                </div>
            @endforelse
        </div>

        @if(count($cart) > 0)
            <div class="p-4 border-t border-amber-500/10 bg-[#0a1628]/90">
                <div class="flex justify-between items-baseline mb-4">
                    <span class="font-serif text-slate-400 text-sm">Estimation total</span>
                    <span class="text-xl font-mono font-bold text-amber-400">{{ number_format($total) }} FCFA</span>
                </div>
                
                <div class="space-y-2">
                    <a href="{{ route('checkout') }}" class="block w-full text-center bg-gradient-to-r from-violet-600 to-cyan-600 hover:from-violet-700 hover:to-cyan-700 text-white text-xs font-bold py-3 px-4 rounded-xl shadow-lg transition-all duration-300 hover:scale-[1.01]">
                        Accéder au paiement
                    </a>
                    <button wire:click="clearCart" class="w-full text-[11px] text-slate-500 hover:text-rose-400 transition-colors py-1">
                        Abandonner la sélection
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>