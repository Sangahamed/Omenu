<div>
    {{-- Statistiques --}}
    <div class="stats-grid grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="stat-card bg-white rounded-md p-4 border border-border">
            <div class="text-xs text-ink-soft uppercase">Total commandes</div>
            <div class="text-2xl font-display font-bold text-brand-black">{{ $statistics['total_orders'] }}</div>
        </div>
        <div class="stat-card bg-white rounded-md p-4 border border-border">
            <div class="text-xs text-ink-soft uppercase">Aujourd'hui</div>
            <div class="text-2xl font-display font-bold text-brand-black">{{ $statistics['today_orders'] }}</div>
        </div>
        <div class="stat-card bg-white rounded-md p-4 border border-border">
            <div class="text-xs text-ink-soft uppercase">CA aujourd'hui</div>
            <div class="text-2xl font-display font-bold text-brand-red">{{ number_format($statistics['today_revenue']) }} FCFA</div>
        </div>
        <div class="stat-card bg-white rounded-md p-4 border border-border">
            <div class="text-xs text-ink-soft uppercase">En attente</div>
            <div class="text-2xl font-display font-bold text-ink-soft">{{ $statistics['pending'] }}</div>
        </div>
        <div class="stat-card bg-white rounded-md p-4 border border-border">
            <div class="text-xs text-ink-soft uppercase">En cours</div>
            <div class="text-2xl font-display font-bold text-brand-black">{{ $statistics['in_progress'] }}</div>
        </div>
    </div>

    {{-- Kanban --}}
    <div class="commandes-board grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach(['pending' => 'En attente', 'accepted' => 'Acceptée', 'preparing' => 'Préparation', 'ready' => 'Prête', 'picked_up' => 'Récupérée', 'delivered' => 'Livrée', 'cancelled' => 'Annulée'] as $status => $label)
            <div class="bg-white rounded-md p-4 border border-border min-h-[300px]">
                <h3 class="font-semibold text-sm text-ink-soft uppercase tracking-wider flex justify-between items-center mb-4">
                    <span>{{ $label }}</span>
                    <span class="bg-[#EFEFEC] text-ink-soft text-xs px-2 py-0.5 rounded-pill">{{ $ordersByStatus[$status]->total() }}</span>
                </h3>

                <div class="space-y-3 max-h-[500px] overflow-y-auto">
                    @forelse($ordersByStatus[$status] as $order)
                        <div class="commande-card statut-{{ $status }} bg-white rounded-sm p-3 border border-border hover:border-brand-black transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-ink text-sm">#{{ $order->id }} - {{ $order->customer_name }}</p>
                                    <p class="text-xs text-ink-soft">{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="text-brand-red font-bold text-sm">{{ number_format($order->total) }} FCFA</span>
                            </div>

                            <div class="mt-2 flex flex-wrap gap-1">
                                @php
                                    $nextStatuses = [
                                        'pending' => 'accepted',
                                        'accepted' => 'preparing',
                                        'preparing' => 'ready',
                                        'ready' => 'picked_up',
                                        'picked_up' => 'delivered',
                                    ];
                                @endphp

                                @if($status !== 'delivered' && $status !== 'cancelled')
                                    @if(isset($nextStatuses[$status]))
                                        <button wire:click="updateStatus({{ $order->id }}, '{{ $nextStatuses[$status] }}')"
                                                class="text-xs bg-brand-black hover:bg-brand-black-2 text-white px-3 py-1 rounded-sm transition">
                                            {{ __('Passer à ' . $nextStatuses[$status]) }}
                                        </button>
                                    @endif

                                    @if($status === 'pending')
                                        <button wire:click="updateStatus({{ $order->id }}, 'cancelled')"
                                                class="text-xs bg-brand-red-soft hover:bg-brand-red-soft text-brand-red px-3 py-1 rounded-sm transition">
                                            Annuler
                                        </button>
                                    @endif
                                @else
                                    <span class="text-xs text-ink-soft italic">Terminée</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-ink-soft text-sm py-6">Aucune commande</div>
                    @endforelse
                </div>

                <div class="mt-3">
                    {{ $ordersByStatus[$status]->links() }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Plats populaires --}}
    <div class="mt-8 bg-white rounded-md p-6 border border-border">
        <h3 class="font-display font-semibold text-ink mb-4">Plats les plus commandés</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
            @forelse($popularMenus as $menu)
                <div class="bg-white rounded-sm p-3 text-center border border-border">
                    <p class="text-sm font-medium text-ink">{{ $menu->name }}</p>
                    <p class="text-xs text-brand-red">{{ $menu->total_quantity }} commandes</p>
                </div>
            @empty
                <p class="text-ink-soft text-sm col-span-full text-center">Aucune donnée pour le moment</p>
            @endforelse
        </div>
    </div>
</div>
