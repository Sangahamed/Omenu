<div>
    {{-- Statistiques --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700">
            <div class="text-xs text-slate-400 uppercase">Total commandes</div>
            <div class="text-2xl font-bold text-white">{{ $statistics['total_orders'] }}</div>
        </div>
        <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700">
            <div class="text-xs text-slate-400 uppercase">Aujourd'hui</div>
            <div class="text-2xl font-bold text-white">{{ $statistics['today_orders'] }}</div>
        </div>
        <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700">
            <div class="text-xs text-slate-400 uppercase">CA aujourd'hui</div>
            <div class="text-2xl font-bold text-amber-500">{{ number_format($statistics['today_revenue']) }} FCFA</div>
        </div>
        <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700">
            <div class="text-xs text-slate-400 uppercase">En attente</div>
            <div class="text-2xl font-bold text-yellow-500">{{ $statistics['pending'] }}</div>
        </div>
        <div class="bg-slate-800/50 rounded-xl p-4 border border-slate-700">
            <div class="text-xs text-slate-400 uppercase">En cours</div>
            <div class="text-2xl font-bold text-blue-500">{{ $statistics['in_progress'] }}</div>
        </div>
    </div>

    {{-- Kanban --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach(['pending' => 'En attente', 'accepted' => 'Acceptée', 'preparing' => 'Préparation', 'ready' => 'Prête', 'picked_up' => 'Récupérée', 'delivered' => 'Livrée', 'cancelled' => 'Annulée'] as $status => $label)
            <div class="bg-slate-900/50 rounded-xl p-4 border border-slate-800 min-h-[300px]">
                <h3 class="font-semibold text-sm text-slate-300 uppercase tracking-wider flex justify-between items-center mb-4">
                    <span>{{ $label }}</span>
                    <span class="bg-slate-800 text-xs px-2 py-0.5 rounded-full">{{ $ordersByStatus[$status]->total() }}</span>
                </h3>

                <div class="space-y-3 max-h-[500px] overflow-y-auto">
                    @forelse($ordersByStatus[$status] as $order)
                        <div class="bg-slate-800/40 rounded-lg p-3 border border-slate-700/50 hover:border-amber-500/30 transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-white text-sm">#{{ $order->id }} - {{ $order->customer_name }}</p>
                                    <p class="text-xs text-slate-400">{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                                <span class="text-amber-500 font-bold text-sm">{{ number_format($order->total) }} FCFA</span>
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
                                                class="text-xs bg-amber-600 hover:bg-amber-700 text-white px-3 py-1 rounded transition">
                                            {{ __('Passer à ' . $nextStatuses[$status]) }}
                                        </button>
                                    @endif

                                    @if($status === 'pending')
                                        <button wire:click="updateStatus({{ $order->id }}, 'cancelled')"
                                                class="text-xs bg-red-600/20 hover:bg-red-600/30 text-red-400 px-3 py-1 rounded transition">
                                            Annuler
                                        </button>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-500 italic">Terminée</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-slate-500 text-sm py-6">Aucune commande</div>
                    @endforelse
                </div>

                <div class="mt-3">
                    {{ $ordersByStatus[$status]->links() }}
                </div>
            </div>
        @endforeach
    </div>

    {{-- Plats populaires --}}
    <div class="mt-8 bg-slate-900/50 rounded-xl p-6 border border-slate-800">
        <h3 class="font-semibold text-white mb-4">Plats les plus commandés</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
            @forelse($popularMenus as $menu)
                <div class="bg-slate-800/30 rounded-lg p-3 text-center border border-slate-700/50">
                    <p class="text-sm font-medium text-white">{{ $menu->name }}</p>
                    <p class="text-xs text-amber-500">{{ $menu->total_quantity }} commandes</p>
                </div>
            @empty
                <p class="text-slate-500 text-sm col-span-full text-center">Aucune donnée pour le moment</p>
            @endforelse
        </div>
    </div>
</div>