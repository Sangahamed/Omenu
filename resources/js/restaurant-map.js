import L from 'leaflet';
import 'leaflet.markercluster';

class RestaurantMap {
    constructor() {
        this.map = null;
        this.markerCluster = null;
        this.initialized = false;
        this.rawRestaurantsData = [];
        
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.init());
        } else {
            this.init();
        }
    }

    init() {
        if (document.getElementById('map') && !this.initialized) {
            this.initMap();
            this.setupLivewireListeners();
            this.initialized = true;
        }
    }

    initMap() {
        
        this.map = L.map('map', {
            zoomControl: false,
            attributionControl: false
        }).setView([5.3167, -4.0333], 12);

        L.control.zoom({ position: 'bottomright' }).addTo(this.map);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 20
        }).addTo(this.map);

        this.markerCluster = L.markerClusterGroup({
            spiderfyOnMaxZoom: true,
            maxClusterRadius: 50,
            showCoverageOnHover: false
        });
        
        this.map.addLayer(this.markerCluster);

        
        this.map.on('zoomend', () => {
            if (this.rawRestaurantsData.length > 0) {
                this.updateMarkers(this.rawRestaurantsData, false); 
            }
        });
    }

    setupLivewireListeners() {
        document.addEventListener('livewire:init', () => {
            Livewire.on('restaurantsUpdated', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                this.updateMarkers(data.restaurants || data, true);
            });
        });

        document.addEventListener('restaurantsUpdated', (e) => {
            this.updateMarkers(e.detail.restaurants || e.detail, true);
        });

        window.addEventListener('resize', () => this.refreshMapSize());
        
        const mapContainer = document.getElementById('map');
        if (mapContainer && mapContainer.parentElement) {
            const observer = new MutationObserver(() => this.refreshMapSize());
            observer.observe(mapContainer.parentElement, { attributes: true, attributeFilter: ['class'] });
        }
    }

    refreshMapSize() {
        if (this.map) {
            setTimeout(() => {
                this.map.invalidateSize();
            }, 50);
        }
    }

    /**
     * Calcule la taille modulaire du marqueur selon le niveau de zoom
     * Zoom élevé (> 14) -> Grand & très visible (restaurants éloignés)
     * Zoom faible (< 12) -> Moyen à petit (éviter la surcharge visuelle)
     */
    getDynamicIconSize() {
        const zoom = this.map ? this.map.getZoom() : 12;
        
        if (zoom >= 15) {
            return { size: 42, iconClass: 'text-lg', pinSize: 'w-10 h-10' }; 
        } else if (zoom <= 11) {
            return { size: 26, iconClass: 'text-xs', pinSize: 'w-7 h-7' }; 
        } else {
            return { size: 34, iconClass: 'text-sm', pinSize: 'w-8.5 h-8.5' }; 
        }
    }

    updateMarkers(restaurants, shouldFitBounds = true) {
        if (!this.markerCluster || !this.map) return;
        
        this.rawRestaurantsData = restaurants;
        
        this.markerCluster.clearLayers();
        const items = Array.isArray(restaurants) ? restaurants : [];

        if (items.length === 0) return;

        const bounds = L.latLngBounds();
        const iconConfig = this.getDynamicIconSize();

        items.forEach(feature => {
            if (!feature.geometry || !feature.geometry.coordinates) return;
            const [lng, lat] = feature.geometry.coordinates;
            
            const marker = L.marker([lat, lng], {
                icon: this.getCustomIcon(iconConfig)
            });

            const popupContent = `
                <div class="p-4 font-sans min-w-[240px]">

                    <h4 class="font-serif text-base font-bold text-slate-900 mb-1">
                        ${feature.properties.name}
                    </h4>

                    <div class="flex items-center gap-1 text-xs text-slate-500 mb-3">
                        <i class="ri-map-pin-line text-amber-500"></i>
                        <span>${feature.properties.address || feature.properties.city}</span>
                    </div>

                    <div class="flex items-center justify-between border-t border-slate-200 pt-3">

                        <div>
                            <p class="text-[11px] uppercase text-slate-400 tracking-wide">
                                Gamme
                            </p>
                            <span class="font-bold text-amber-600">
                                ${feature.properties.price_range || '€€'}
                            </span>
                        </div>

                        <a href="${feature.properties.url}"
                        class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-violet-600 to-cyan-600 px-4 py-2 text-sm font-semibold no-underline transition-all duration-300 hover:scale-105 hover:from-violet-700 hover:to-cyan-700"
                        style="color:#fff !important; text-decoration:none !important;">
                            <i class="ri-restaurant-line mr-1"></i>
                            Voir le menu
                        </a>

                    </div>

                </div>
            `;

            marker.bindPopup(popupContent, { maxWidth: 260, className: 'custom-popup' });
            this.markerCluster.addLayer(marker);
            bounds.extend([lat, lng]);
        });

        if (shouldFitBounds && items.length > 0) {
            setTimeout(() => {
                this.map.fitBounds(bounds, { padding: [50, 50], maxZoom: 15 });
            }, 100);
        }
    }

    getCustomIcon(config) {
        
        const svgIcon = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-1/2 h-1/2 text-slate-950">
                <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/>
                <path d="M7 2v20"/>
                <path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/>
            </svg>
        `;

        return L.divIcon({
            className: 'custom-marker-wrapper',
            html: `
                <div class="relative ${config.pinSize} bg-gradient-to-br from-amber-400 to-amber-600 rounded-full shadow-xl border-2 border-slate-950 flex items-center justify-center transform hover:scale-110 transition-transform duration-200">
                    ${svgIcon}
                    <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-amber-600 rotate-45 border-r border-b border-slate-950 -z-10"></div>
                </div>
            `,
            iconSize: [config.size, config.size],
            iconAnchor: [config.size / 2, config.size],
            popupAnchor: [0, -config.size]
        });
    }
}

window.appRestaurantMap = new RestaurantMap();