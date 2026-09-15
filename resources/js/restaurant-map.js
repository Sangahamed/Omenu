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

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 20,
            subdomains: 'abcd',
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>'
        }).addTo(this.map);

        this.markerCluster = L.markerClusterGroup({
            spiderfyOnMaxZoom: true,
            maxClusterRadius: 40,
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

    getDynamicIconSize() {
        const zoom = this.map ? this.map.getZoom() : 12;
        
        if (zoom >= 15) {
            return { size: 44, iconClass: 'text-lg', pinSize: 'w-11 h-11' }; 
        } else if (zoom <= 11) {
            return { size: 30, iconClass: 'text-xs', pinSize: 'w-8 h-8' }; 
        } else {
            return { size: 36, iconClass: 'text-sm', pinSize: 'w-9.5 h-9.5' }; 
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

            const cuisineBadge = feature.properties.cuisine 
                ? `<span class="inline-block px-2.5 py-0.5 rounded-full bg-red-50 text-red-700 text-[11px] font-bold uppercase tracking-wider mb-1.5">${feature.properties.cuisine}</span>`
                : '';

            // Photo de couverture du restaurant : `image` vaut null quand aucune
            // illustration n'est renseignee, on retombe alors sur une bande neutre.
            const cover = feature.properties.image
                ? `<img src="${feature.properties.image}" alt="${feature.properties.name}"
                        loading="lazy"
                        class="w-full h-32 object-cover block"
                        onerror="this.closest('.popup-cover').innerHTML='<div class=&quot;w-full h-32 flex items-center justify-center bg-slate-100 text-slate-400&quot;><i class=&quot;ri-restaurant-2-line text-3xl&quot;></i></div>'">`
                : `<div class="w-full h-32 flex items-center justify-center bg-slate-100 text-slate-400">
                       <i class="ri-restaurant-2-line text-3xl"></i>
                   </div>`;

            const popupContent = `
                <div class="popup-cover">${cover}</div>
                <div class="p-4 font-sans min-w-[240px]">
                    ${cuisineBadge}
                    <h4 class="font-display font-bold text-base text-slate-900 mb-1 leading-snug">
                        ${feature.properties.name}
                    </h4>

                    <div class="flex items-center gap-1.5 text-xs text-slate-500 mb-3">
                        <i class="ri-map-pin-2-fill text-red-600"></i>
                        <span class="font-medium">${feature.properties.address || feature.properties.city || 'Abidjan'}</span>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <a href="${feature.properties.url}"
                           class="w-full text-center px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-red-600 text-white text-xs font-semibold shadow-md transition-all duration-200 no-underline block"
                           style="color:#fff !important; text-decoration:none !important;">
                            <i class="ri-restaurant-2-line mr-1"></i> Voir l'établissement
                        </a>
                    </div>
                </div>
            `;

            marker.bindPopup(popupContent, { maxWidth: 280, className: 'custom-popup' });
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
        return L.divIcon({
            className: 'custom-marker-wrapper',
            html: `
                <div class="custom-marker-pin">
                    <div class="custom-marker-icon">
                        <i class="ri-restaurant-fill"></i>
                    </div>
                </div>
            `,
            iconSize: [38, 38],
            iconAnchor: [19, 38],
            popupAnchor: [0, -38]
        });
    }
}

window.appRestaurantMap = new RestaurantMap();