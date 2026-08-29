<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-display font-semibold text-brand-black tracking-tight mb-8">Paiement & Livraison</h1>

    <div class="grid lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white border border-border rounded-md p-6">
                <h2 class="text-xl font-display font-semibold text-ink mb-6 flex items-center gap-2">
                    <span class="text-brand-red inline-flex">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
                    </span>
                    Coordonnées de votre table
                </h2>

                <form wire:submit.prevent="placeOrder" class="space-y-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-ink-soft mb-2">Nom Complet</label>
                            <input type="text" wire:model="customer_name" class="w-full bg-white border border-border focus:border-brand-red rounded-sm px-4 py-3 text-ink focus:outline-none transition-colors duration-300">
                            @error('customer_name') <span class="text-brand-red text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-ink-soft mb-2">Numéro de Téléphone</label>
                            <input type="tel" wire:model="customer_phone" class="w-full bg-white border border-border focus:border-brand-red rounded-sm px-4 py-3 text-ink focus:outline-none transition-colors duration-300">
                            @error('customer_phone') <span class="text-brand-red text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-ink-soft mb-2">Adresse de Résidence / Bureau</label>
                        <textarea wire:model="delivery_address" rows="3" placeholder="Indiquez l'emplacement de livraison avec le maximum de précisions" class="w-full bg-white border border-border focus:border-brand-red rounded-sm px-4 py-3 text-ink focus:outline-none transition-colors duration-300"></textarea>
                        @error('delivery_address') <span class="text-brand-red text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-ink-soft mb-2">Consignes de Service (Optionnel)</label>
                        <textarea wire:model="delivery_instructions" rows="2" placeholder="Ex: Code d'accès, appeler à l'arrivée..." class="w-full bg-white border border-border focus:border-brand-red rounded-sm px-4 py-3 text-ink focus:outline-none transition-colors duration-300"></textarea>
                    </div>

                    <div class="border-t border-border pt-6">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-ink-soft mb-4">Mode de Règlement</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="relative flex flex-col p-4 bg-white border rounded-sm cursor-pointer select-none transition-all duration-300 {{ $payment_method === 'stripe' ? 'border-brand-red' : 'border-border hover:border-brand-black' }}">
                                <input type="radio" wire:model.live="payment_method" value="stripe" class="sr-only">
                                <span class="text-sm font-bold text-ink">Carte de Crédit</span>
                                <span class="text-[11px] text-ink-soft mt-1">Visa, Mastercard</span>
                            </label>

                            <label class="relative flex flex-col p-4 bg-white border rounded-sm cursor-pointer select-none transition-all duration-300 {{ $payment_method === 'orange_money' ? 'border-brand-red' : 'border-border hover:border-brand-black' }}">
                                <input type="radio" wire:model.live="payment_method" value="orange_money" class="sr-only">
                                <span class="text-sm font-bold text-ink">Orange Money</span>
                                <span class="text-[11px] text-ink-soft mt-1">Directement en Côte d'Ivoire</span>
                            </label>

                            <label class="relative flex flex-col p-4 bg-white border rounded-sm cursor-pointer select-none transition-all duration-300 {{ $payment_method === 'wave' ? 'border-brand-red' : 'border-border hover:border-brand-black' }}">
                                <input type="radio" wire:model.live="payment_method" value="wave" class="sr-only">
                                <span class="text-sm font-bold text-ink">Wave Mobile</span>
                                <span class="text-[11px] text-ink-soft mt-1">Transaction 1%</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-6 bg-brand-black hover:bg-brand-black-2 text-white font-bold py-4 rounded-sm transition-all duration-300">
                        Finaliser mon expérience culinaire
                    </button>

                    <p class="text-center text-[11px] text-ink-soft">
                        Une confirmation vous sera aussi envoyée par WhatsApp après validation du paiement.
                    </p>
                </form>
            </div>
        </div>

        {{-- Facturation Panier --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-border rounded-md p-6 sticky top-28">
                <h2 class="text-lg font-display font-semibold text-ink mb-6 border-b border-border pb-4">Résumé de votre commande</h2>

                <div class="space-y-4 max-h-72 overflow-y-auto pr-2 mb-6">
                    @foreach($cart as $item)
                        <div class="flex justify-between items-start gap-4">
                            <div class="min-w-0">
                                <p class="text-sm font-display font-semibold text-ink truncate">{{ $item['name'] }}</p>
                                <p class="text-xs text-ink-soft">Quantité : {{ $item['quantity'] }}</p>
                            </div>
                            <span class="text-sm font-mono text-brand-red font-semibold">{{ number_format($item['price'] * $item['quantity']) }} XOF</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-border pt-4 space-y-2.5 text-xs text-ink-soft font-mono">
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

                <div class="border-t border-brand-black pt-4 mt-4">
                    <div class="flex justify-between items-baseline">
                        <span class="font-display font-bold text-ink">Montant Total</span>
                        <span class="text-2xl font-mono font-bold text-brand-red">{{ number_format($total) }} XOF</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
