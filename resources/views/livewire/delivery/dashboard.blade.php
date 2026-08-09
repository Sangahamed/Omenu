<div>
    {{-- En-tête --}}
    <div class="bg-slate-900/50 rounded-xl p-6 border border-slate-800 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Dashboard Livreur</h1>
                <p class="text-slate-400 text-sm">Bonjour, {{ auth()->user()->name }}</p>
            </div>
            <div class="flex gap-3">
                <button wire:click="toggleStatus('available')" 
                        class="px-4 py-2 rounded-lg {{ $status === 'available' ? 'bg-green-600 text-white' : 'bg-slate-700 text-slate-400' }} transition">
                    <i class="ri-check-line"></i> Disponible
                </button>
                <button wire:click="toggleStatus('offline')" 
                        class="px-4 py-2 rounded-lg {{ $status === 'offline' ? 'bg-red-600 text-white' : 'bg-slate-700 text-slate-400' }} transition">
                    <i class="ri-close-line"></i> Hors ligne
                </button>
            </div>
        </div>
    </div>

    {{-- Commandes disponibles --}}
    <h2 class="text-xl font-semibold text-white mb-4">Commandes disponibles</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        @forelse($availableOrders as $order)
            <div class="bg-slate-900/50 rounded-xl p-4 border border-slate-800 hover:border-amber-500/30 transition">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium text-white">#{{ $order->id }} - {{ $order->customer_name }}</p>
                        <p class="text-xs text-slate-400">{{ $order->restaurant->name }}</p>
                        <p class="text-xs text-slate-400">{{ $order->delivery_address }}</p>
                    </div>
                    <span class="text-amber-500 font-bold">{{ number_format($order->total) }} FCFA</span>
                </div>
                <div class="mt-3 flex gap-2">
                    <button wire:click="acceptOrder({{ $order->id }})" 
                            class="flex-1 bg-amber-600 hover:bg-amber-700 text-white py-2 rounded-lg text-sm transition">
                        <i class="ri-check-line"></i> Accepter
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-slate-500 py-8">
                <i class="ri-time-line text-4xl block mb-2"></i>
                Aucune commande disponible
            </div>
        @endforelse
    </div>

    {{-- Mes commandes en cours --}}
    @if(count($myOrders) > 0)
        <h2 class="text-xl font-semibold text-white mb-4">Mes livraisons en cours</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($myOrders as $order)
                <div class="bg-slate-900/50 rounded-xl p-4 border border-amber-500/20">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-medium text-white">#{{ $order->id }} - {{ $order->customer_name }}</p>
                            <p class="text-xs text-slate-400">{{ $order->restaurant->name }}</p>
                            <p class="text-xs text-slate-400">{{ $order->delivery_address }}</p>
                        </div>
                        <span class="text-amber-500 font-bold">{{ number_format($order->total) }} FCFA</span>
                    </div>
                    <div class="mt-3 flex gap-2">
                        @if($order->status === 'picked_up')
                            <button wire:click="markAsDelivered({{ $order->id }})" 
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm transition">
                                <i class="ri-check-double-line"></i> Livrée
                            </button>
                        @else
                            <span class="text-xs text-yellow-500">En attente de prise en charge</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Mise à jour de position (simulée) --}}
    <div class="mt-8 bg-slate-900/50 rounded-xl p-4 border border-slate-800">
        <p class="text-sm text-slate-400 mb-2">Position actuelle :</p>
        <div class="flex gap-4">
            <button wire:click="toggleStatus('available')" 
                    class="px-4 py-2 rounded-lg {{ $status === 'available' ? 'bg-green-600 text-white' : 'bg-slate-700 text-slate-400' }} transition">
                <i class="ri-check-line"></i> Disponible
            </button>
            <button wire:click="toggleStatus('offline')" 
                    class="px-4 py-2 rounded-lg {{ $status === 'offline' ? 'bg-red-600 text-white' : 'bg-slate-700 text-slate-400' }} transition">
                <i class="ri-close-line"></i> Hors ligne
            </button>
        </div>
        <div class="flex gap-4">
            <button onclick="startWatchingPosition()" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="ri-map-pin-2-line"></i> Démarrer le suivi
            </button>
            <button onclick="stopWatchingPosition()" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg text-sm transition">
                <i class="ri-pause-line"></i> Arrêter
            </button>
        </div>
    </div>
</div>

<script>
   let watchId = null;

function startWatchingPosition() {
    if (navigator.geolocation) {
        watchId = navigator.geolocation.watchPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                @this.updateLocation(lat, lng);
                document.getElementById('positionStatus').textContent = 
                    '📍 En cours : ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
            },
            (error) => {
                console.error('Erreur GPS :', error.message);
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 5000 }
        );
    }
}

function stopWatchingPosition() {
    if (watchId) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
        document.getElementById('positionStatus').textContent = '⏸️ Surveillance arrêtée';
    }
}

// Lancer automatiquement si le statut est 'available' ou 'on_delivery'
@if($status === 'available')
    document.addEventListener('DOMContentLoaded', startWatchingPosition);
@endif

// Arrêter quand on passe hors ligne (à gérer avec un événement Livewire ou un bouton)
</script>