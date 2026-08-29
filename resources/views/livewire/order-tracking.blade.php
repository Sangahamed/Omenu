<div>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-display font-semibold text-brand-black mb-4">Suivi de ma commande #{{ $order->id }}</h1>

        {{-- Statut --}}
        <div class="bg-white rounded-md p-6 border border-border mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $order->status === 'cancelled' ? 'bg-brand-red text-white' : 'bg-brand-black text-white' }}">
                    <i class="ri-{{ $order->status === 'delivered' ? 'check-double-line' : ($order->status === 'pending' ? 'time-line' : 'truck-line') }} text-xl"></i>
                </div>
                <div>
                    <p class="text-ink font-semibold">Statut : <span class="badge {{ $order->status_color }}">{{ $order->status_label }}</span></p>
                    @if($deliveryPersonName)
                        <p class="text-sm text-ink-soft mt-1">Livreur : {{ $deliveryPersonName }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Carte --}}
        <div id="trackingMap" style="height: 400px; border-radius: 3px; overflow: hidden;" class="border border-border"></div>

        {{-- Timeline --}}
        <div class="mt-6 bg-white rounded-md p-6 border border-border">
            <h3 class="font-display font-semibold text-ink mb-4">Historique</h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-brand-black flex items-center justify-center flex-shrink-0">
                        <i class="ri-check-line text-white"></i>
                    </div>
                    <div>
                        <p class="text-ink">Commande passée</p>
                        <p class="text-xs text-ink-soft">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                @if($order->accepted_at)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-brand-black flex items-center justify-center flex-shrink-0">
                            <i class="ri-check-line text-white"></i>
                        </div>
                        <div>
                            <p class="text-ink">Acceptée par le restaurant</p>
                            <p class="text-xs text-ink-soft">{{ $order->accepted_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif
                @if($order->picked_up_at)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-brand-red flex items-center justify-center flex-shrink-0">
                            <i class="ri-truck-line text-white"></i>
                        </div>
                        <div>
                            <p class="text-ink">En livraison</p>
                            <p class="text-xs text-ink-soft">{{ $order->picked_up_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                @endif
                @if($order->delivered_at)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-brand-black flex items-center justify-center flex-shrink-0">
                            <i class="ri-check-double-line text-white"></i>
                        </div>
                        <div>
                            <p class="text-ink">Livrée</p>
                            <p class="text-xs text-ink-soft">{{ $order->delivered_at->format('d/m/Y H:i') }}</p>
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
