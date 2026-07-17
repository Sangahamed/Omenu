<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-serif font-bold text-amber-500 tracking-wider mb-8">Paiement & Livraison</h1>

    <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-slate-900 border border-amber-500/10 rounded-2xl p-6 shadow-xl">
                <h2 class="text-xl font-serif font-bold text-slate-100 mb-6 flex items-center gap-2">
                    <span class="text-amber-500 inline-flex">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
                    </span> 
                    Coordonnées de votre table
                </h2>

                <form wire:submit.prevent="placeOrder" class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Nom Complet</label>
                            <input type="text" wire:model="customer_name" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-4 py-3 text-slate-200 focus:outline-none transition-colors duration-300">
                            @error('customer_name') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Numéro de Téléphone</label>
                            <input type="tel" wire:model="customer_phone" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-4 py-3 text-slate-200 focus:outline-none transition-colors duration-300">
                            @error('customer_phone') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Adresse de Résidence / Bureau</label>
                        <textarea wire:model="delivery_address" rows="3" placeholder="Indiquez l'emplacement de livraison avec le maximum de précisions" class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-4 py-3 text-slate-200 focus:outline-none transition-colors duration-300"></textarea>
                        @error('delivery_address') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Consignes de Service (Optionnel)</label>
                        <textarea wire:model="delivery_instructions" rows="2" placeholder="Ex: Code d'accès, appeler à l'arrivée..." class="w-full bg-slate-950 border border-slate-800 focus:border-amber-500 rounded-xl px-4 py-3 text-slate-200 focus:outline-none transition-colors duration-300"></textarea>
                    </div>

                    <div class="border-t border-amber-500/10 pt-6">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-4">Mode de Règlement</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="relative flex flex-col p-4 bg-slate-950 border rounded-xl cursor-pointer select-none transition-all duration-300 {{ $payment_method === 'stripe' ? 'border-amber-500 shadow-md shadow-amber-500/5' : 'border-slate-800 hover:border-slate-700' }}">
                                <input type="radio" wire:model.live="payment_method" value="stripe" class="sr-only">
                                <span class="text-sm font-bold text-slate-200">Carte de Crédit</span>
                                <span class="text-[11px] text-slate-500 mt-1">Visa, Mastercard</span>
                            </label>

                            <label class="relative flex flex-col p-4 bg-slate-950 border rounded-xl cursor-pointer select-none transition-all duration-300 {{ $payment_method === 'orange_money' ? 'border-amber-500 shadow-md shadow-amber-500/5' : 'border-slate-800 hover:border-slate-700' }}">
                                <input type="radio" wire:model.live="payment_method" value="orange_money" class="sr-only">
                                <span class="text-sm font-bold text-slate-200">Orange Money</span>
                                <span class="text-[11px] text-slate-500 mt-1">Directement en Côte d'Ivoire</span>
                            </label>

                            <label class="relative flex flex-col p-4 bg-slate-950 border rounded-xl cursor-pointer select-none transition-all duration-300 {{ $payment_method === 'wave' ? 'border-amber-500 shadow-md shadow-amber-500/5' : 'border-slate-800 hover:border-slate-700' }}">
                                <input type="radio" wire:model.live="payment_method" value="wave" class="sr-only">
                                <span class="text-sm font-bold text-slate-200">Wave Mobile</span>
                                <span class="text-[11px] text-slate-500 mt-1">Transaction 1%</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-6 bg-gradient-to-r from-violet-600 to-cyan-600 hover:from-violet-700 hover:to-cyan-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all duration-300 hover:scale-[1.01]">
                        Finaliser mon expérience culinaire
                    </button>
                </form>
            </div>
        </div>

        {{-- Facturation Panier --}}
        <div class="lg:col-span-1">
            <div class="bg-slate-900 border border-amber-500/10 rounded-2xl p-6 shadow-xl sticky top-28">
                <h2 class="text-lg font-serif font-bold text-slate-100 mb-6 border-b border-amber-500/10 pb-4">Résumé de votre commande</h2>

                <div class="space-y-4 max-h-72 overflow-y-auto pr-2 mb-6">
                    @foreach($cart as $item)
                        <div class="flex justify-between items-start gap-4">
                            <div class="min-w-0">
                                <p class="text-sm font-serif font-semibold text-slate-200 truncate">{{ $item['name'] }}</p>
                                <p class="text-xs text-slate-400">Quantité : {{ $item['quantity'] }}</p>
                            </div>
                            <span class="text-sm font-mono text-amber-500 font-semibold">{{ number_format($item['price'] * $item['quantity']) }} XOF</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-slate-800 pt-4 space-y-2.5 text-xs text-slate-400 font-mono">
                    <div class="flex justify-between">
                        <span>Gastronomie</span>
                        <span>{{ number_format($subtotal) }} XOF</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Frais de service & livraison</span>
                        <span>{{ $delivery_fee > 0 ? number_format($delivery_fee) . ' XOF' : 'Gratuit' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>TVA (18%)</span>
                        <span>{{ number_format($tax) }} XOF</span>
                    </div>
                </div>

                <div class="border-t border-amber-500/20 pt-4 mt-4">
                    <div class="flex justify-between items-baseline">
                        <span class="font-serif font-bold text-slate-200">Montant Total</span>
                        <span class="text-2xl font-mono font-bold text-amber-400">{{ number_format($total) }} XOF</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>