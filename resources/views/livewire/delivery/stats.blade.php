<div class="container mx-auto px-4 md:px-8 py-10">

    {{-- En-tête + sélecteur de période --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-display font-semibold text-brand-black tracking-tight">Mes statistiques</h1>
            <p class="text-xs text-ink-soft uppercase tracking-widest font-semibold mt-1">
                Gains et performance de livraison
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach($periods as $key => $label)
                <button type="button" wire:click="setPeriod('{{ $key }}')"
                        class="text-xs font-semibold px-3 py-1.5 rounded-sm border transition-colors
                               {{ $period === $key
                                  ? 'bg-brand-black text-white border-brand-black'
                                  : 'bg-white text-ink-soft border-border hover:border-brand-black' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Indicateurs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-md border border-border relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-brand-black"></div>
            <div class="pl-3">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-ink-soft uppercase font-semibold tracking-wide">Courses</span>
                    <div class="w-8 h-8 rounded-sm bg-[#EFEFEC] flex items-center justify-center">
                        <i class="ri-e-bike-2-line text-sm text-ink"></i>
                    </div>
                </div>
                <p class="text-3xl font-display font-bold text-brand-black">{{ $totalOrders }}</p>
                <p class="text-xs text-ink-soft mt-1">livraisons effectuées</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-md border border-border relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-brand-red"></div>
            <div class="pl-3">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-ink-soft uppercase font-semibold tracking-wide">Gains estimés</span>
                    <div class="w-8 h-8 rounded-sm bg-brand-red-soft flex items-center justify-center">
                        <i class="ri-wallet-3-line text-sm text-brand-red"></i>
                    </div>
                </div>
                <p class="text-2xl font-display font-bold text-brand-red">
                    {{ number_format($totalEarnings, 0, ',', ' ') }}
                </p>
                <p class="text-xs text-ink-soft mt-1">
                    FCFA · {{ number_format($commission, 0, ',', ' ') }} par course
                </p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-md border border-border relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-[#1F7A4D]"></div>
            <div class="pl-3">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-ink-soft uppercase font-semibold tracking-wide">Valeur livrée</span>
                    <div class="w-8 h-8 rounded-sm bg-[#EFEFEC] flex items-center justify-center">
                        <i class="ri-shopping-bag-3-line text-sm text-ink"></i>
                    </div>
                </div>
                <p class="text-2xl font-display font-bold text-ink">
                    {{ number_format($deliveredValue, 0, ',', ' ') }}
                </p>
                <p class="text-xs text-ink-soft mt-1">FCFA de commandes</p>
            </div>
        </div>

        <div class="bg-white p-5 rounded-md border border-border relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-border-strong"></div>
            <div class="pl-3">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs text-ink-soft uppercase font-semibold tracking-wide">Durée moyenne</span>
                    <div class="w-8 h-8 rounded-sm bg-[#EFEFEC] flex items-center justify-center">
                        <i class="ri-timer-line text-sm text-ink"></i>
                    </div>
                </div>
                <p class="text-3xl font-display font-bold text-ink">
                    {{ $averageMinutes !== null ? $averageMinutes : '—' }}
                    @if($averageMinutes !== null)<span class="text-base font-sans font-medium text-ink-soft">min</span>@endif
                </p>
                <p class="text-xs text-ink-soft mt-1">de la prise en charge à la livraison</p>
            </div>
        </div>
    </div>

    {{-- Historique --}}
    <div class="bg-white border border-border rounded-md overflow-hidden">
        <div class="p-5 border-b border-border flex items-center justify-between">
            <div>
                <h2 class="font-display font-semibold text-base text-brand-black">Historique de la période</h2>
                <p class="text-xs text-ink-soft mt-0.5">
                    {{ $periods[$period] ?? "Aujourd'hui" }} ·
                    {{ $lifetimeOrders }} livraison{{ $lifetimeOrders > 1 ? 's' : '' }} au total
                </p>
            </div>

            <a href="{{ route('delivery.dashboard') }}"
               class="inline-flex items-center gap-1.5 text-xs text-brand-red hover:text-brand-red-hover font-semibold">
                <i class="ri-arrow-left-line"></i>
                Retour au tableau de bord
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#FBFBFA] border-b border-border text-xs text-ink-soft uppercase">
                    <tr>
                        <th class="py-3.5 px-5 font-semibold">Commande</th>
                        <th class="py-3.5 px-5 font-semibold">Client</th>
                        <th class="py-3.5 px-5 font-semibold">Restaurant</th>
                        <th class="py-3.5 px-5 font-semibold">Livrée le</th>
                        <th class="py-3.5 px-5 font-semibold text-right">Montant</th>
                        <th class="py-3.5 px-5 font-semibold text-right">Gain</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border text-ink">
                    @forelse($orders as $order)
                        <tr class="hover:bg-[#FBFBFA] transition-colors">
                            <td class="py-3.5 px-5 font-semibold text-xs">#{{ $order->id }}</td>
                            <td class="py-3.5 px-5 text-xs">{{ $order->customer_name }}</td>
                            <td class="py-3.5 px-5 text-xs text-ink-soft">{{ $order->restaurant?->name ?? '—' }}</td>
                            <td class="py-3.5 px-5 text-xs text-ink-soft">
                                {{ $order->delivered_at?->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="py-3.5 px-5 text-right font-mono font-bold text-ink text-xs">
                                {{ number_format($order->total, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="py-3.5 px-5 text-right font-mono font-bold text-brand-red text-xs">
                                +{{ number_format($commission, 0, ',', ' ') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <i class="ri-e-bike-2-line text-3xl text-ink-soft block mb-2"></i>
                                <p class="text-sm text-ink-soft">Aucune livraison sur cette période.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->count() >= 50)
            <div class="px-5 py-3 border-t border-border text-xs text-ink-soft">
                Les 50 livraisons les plus récentes sont affichées ; les indicateurs ci-dessus portent sur toute la période.
            </div>
        @endif
    </div>
</div>
