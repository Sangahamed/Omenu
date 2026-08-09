<div>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-white mb-4">Suivi de ma commande #{{ $order->id }}</h1>

        {{-- Statut --}}
        <div class="bg-slate-900/50 rounded-xl p-6 border border-slate-800 mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $order->status_color }}">
                    <i class="ri-{{ $order->status === 'delivered' ? 'check-double-line' : ($order->status === 'pending' ? 'time-line' : 'truck-line') }} text-xl"></i>
                </div>
                <div>
                    <p class="text-white font-semibold">Statut : {{ $order->status_label }}</p>
                    @if($deliveryPersonName)
                        <p class="text-sm text-slate-400">Livreur : {{ $deliveryPersonName }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Carte --}}
        <div id="trackingMap" style="height: 400px; border-radius: 12px; overflow: hidden;" class="border border-slate-800"></div>

        {{-- Timeline --}}
        <div class="mt-6 bg-slate-900/50 rounded-xl p-6 border border-slate-800">
            <h3 class="font-semibold text-white mb-4">Historique</h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center flex-shrink-0">
                        <i class="ri-check-line text-white"></i>
                    </div>
                    <div>
                        <p class="text-white">Commande passée</p>
                        <p class="text-xs text-slate-400">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                @if($order->accepted_at)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center flex-shrink-0">
                            <i class="ri-check-line text-white"></i>
                        </div>
                        <div>
                            <p class="text-white">Acceptée par le restaurant</p>
                            <p class="text-xs text-slate-400">{{ $order->accepted_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif
                @if($order->picked_up_at)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber-600 flex items-center justify-center flex-shrink-0">
                            <i class="ri-truck-line text-white"></i>
                        </div>
                        <div>
                            <p class="text-white">En livraison</p>
                            <p class="text-xs text-slate-400">{{ $order->picked_up_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif
                @if($order->delivered_at)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center flex-shrink-0">
                            <i class="ri-check-double-line text-white"></i>
                        </div>
                        <div>
                            <p class="text-white">Livrée</p>
                            <p class="text-xs text-slate-400">{{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = L.map('trackingMap').setView([
            {{ $order->restaurant->latitude }},
            {{ $order->restaurant->longitude }}
        ], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Marqueur du restaurant
        L.marker([
            {{ $order->restaurant->latitude }},
            {{ $order->restaurant->longitude }}
        ]).addTo(map)
        .bindPopup('{{ $order->restaurant->name }}');

        // Marqueur du livreur (si disponible)
        @if($deliveryLocation)
            const deliveryMarker = L.marker([
                {{ $deliveryLocation['lat'] }},
                {{ $deliveryLocation['lng'] }}
            ]).addTo(map)
            .bindPopup('Livreur : {{ $deliveryPersonName }}');

            // Écouter les mises à jour de position
            Livewire.on('locationUpdated', (data) => {
                deliveryMarker.setLatLng([data.latitude, data.longitude]);
                map.panTo([data.latitude, data.longitude]);
            });

            // Après avoir créé le marqueur deliveryMarker
            if (window.Echo) {
                window.Echo.private('order.{{ $order->id }}')
                    .listen('DeliveryLocationUpdated', (e) => {
                        if (deliveryMarker) {
                            deliveryMarker.setLatLng([e.latitude, e.longitude]);
                            map.panTo([e.latitude, e.longitude]);
                        }
                    });
            }
        @endif
    });
</script>
@endpush