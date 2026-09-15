import{r as e}from"./rolldown-runtime-QTnfLwEv.js";import{n as t,t as n}from"./leaflet-2IpqudzF.js";var r=e(t(),1);n(),window.appRestaurantMap=new class{constructor(){this.map=null,this.markerCluster=null,this.initialized=!1,this.rawRestaurantsData=[],document.readyState===`loading`?document.addEventListener(`DOMContentLoaded`,()=>this.init()):this.init()}init(){document.getElementById(`map`)&&!this.initialized&&(this.initMap(),this.setupLivewireListeners(),this.initialized=!0)}initMap(){this.map=r.default.map(`map`,{zoomControl:!1,attributionControl:!1}).setView([5.3167,-4.0333],12),r.default.control.zoom({position:`bottomright`}).addTo(this.map),r.default.tileLayer(`https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png`,{maxZoom:20,subdomains:`abcd`,attribution:`&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>`}).addTo(this.map),this.markerCluster=r.default.markerClusterGroup({spiderfyOnMaxZoom:!0,maxClusterRadius:40,showCoverageOnHover:!1}),this.map.addLayer(this.markerCluster),this.map.on(`zoomend`,()=>{this.rawRestaurantsData.length>0&&this.updateMarkers(this.rawRestaurantsData,!1)})}setupLivewireListeners(){document.addEventListener(`livewire:init`,()=>{Livewire.on(`restaurantsUpdated`,e=>{let t=Array.isArray(e)?e[0]:e;this.updateMarkers(t.restaurants||t,!0)})}),document.addEventListener(`restaurantsUpdated`,e=>{this.updateMarkers(e.detail.restaurants||e.detail,!0)}),window.addEventListener(`resize`,()=>this.refreshMapSize());let e=document.getElementById(`map`);e&&e.parentElement&&new MutationObserver(()=>this.refreshMapSize()).observe(e.parentElement,{attributes:!0,attributeFilter:[`class`]})}refreshMapSize(){this.map&&setTimeout(()=>{this.map.invalidateSize()},50)}getDynamicIconSize(){let e=this.map?this.map.getZoom():12;return e>=15?{size:44,iconClass:`text-lg`,pinSize:`w-11 h-11`}:e<=11?{size:30,iconClass:`text-xs`,pinSize:`w-8 h-8`}:{size:36,iconClass:`text-sm`,pinSize:`w-9.5 h-9.5`}}updateMarkers(e,t=!0){if(!this.markerCluster||!this.map)return;this.rawRestaurantsData=e,this.markerCluster.clearLayers();let n=Array.isArray(e)?e:[];if(n.length===0)return;let i=r.default.latLngBounds(),a=this.getDynamicIconSize();n.forEach(e=>{if(!e.geometry||!e.geometry.coordinates)return;let[t,n]=e.geometry.coordinates,o=r.default.marker([n,t],{icon:this.getCustomIcon(a)}),s=e.properties.cuisine?`<span class="inline-block px-2.5 py-0.5 rounded-full bg-red-50 text-red-700 text-[11px] font-bold uppercase tracking-wider mb-1.5">${e.properties.cuisine}</span>`:``,c=`
                <div class="popup-cover">${e.properties.image?`<img src="${e.properties.image}" alt="${e.properties.name}"
                        loading="lazy"
                        class="w-full h-32 object-cover block"
                        onerror="this.closest('.popup-cover').innerHTML='<div class=&quot;w-full h-32 flex items-center justify-center bg-slate-100 text-slate-400&quot;><i class=&quot;ri-restaurant-2-line text-3xl&quot;></i></div>'">`:`<div class="w-full h-32 flex items-center justify-center bg-slate-100 text-slate-400">
                       <i class="ri-restaurant-2-line text-3xl"></i>
                   </div>`}</div>
                <div class="p-4 font-sans min-w-[240px]">
                    ${s}
                    <h4 class="font-display font-bold text-base text-slate-900 mb-1 leading-snug">
                        ${e.properties.name}
                    </h4>

                    <div class="flex items-center gap-1.5 text-xs text-slate-500 mb-3">
                        <i class="ri-map-pin-2-fill text-red-600"></i>
                        <span class="font-medium">${e.properties.address||e.properties.city||`Abidjan`}</span>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <a href="${e.properties.url}"
                           class="w-full text-center px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-red-600 text-white text-xs font-semibold shadow-md transition-all duration-200 no-underline block"
                           style="color:#fff !important; text-decoration:none !important;">
                            <i class="ri-restaurant-2-line mr-1"></i> Voir l'établissement
                        </a>
                    </div>
                </div>
            `;o.bindPopup(c,{maxWidth:280,className:`custom-popup`}),this.markerCluster.addLayer(o),i.extend([n,t])}),t&&n.length>0&&setTimeout(()=>{this.map.fitBounds(i,{padding:[50,50],maxZoom:15})},100)}getCustomIcon(e){return r.default.divIcon({className:`custom-marker-wrapper`,html:`
                <div class="custom-marker-pin">
                    <div class="custom-marker-icon">
                        <i class="ri-restaurant-fill"></i>
                    </div>
                </div>
            `,iconSize:[38,38],iconAnchor:[19,38],popupAnchor:[0,-38]})}};