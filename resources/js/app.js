import './bootstrap';
import './restaurant-map';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';

Alpine.plugin(focus);
Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();




import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

if (typeof window.Echo === 'undefined') {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        wssPort: import.meta.env.VITE_REVERB_PORT,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
}

// Pour le restaurant
if (window.authUserId) {
    // On suppose que l'utilisateur connecté est un restaurateur
    // On récupère son restaurant_id via une route ou un attribut
    fetch('/api/restaurant-id')
        .then(res => res.json())
        .then(data => {
            if (data.restaurant_id) {
                window.Echo.private(`restaurant.${data.restaurant_id}`)
                    .listen('OrderPlaced', (e) => {
                        // Notification toast
                        Toastify({
                            text: `Nouvelle commande #${e.order_id} de ${e.customer_name} (${e.total} FCFA)`,
                            duration: 10000,
                            close: true,
                            gravity: 'top',
                            position: 'right',
                            backgroundColor: '#f59e0b',
                            stopOnFocus: true,
                        }).showToast();

                        // Recharger le dashboard Livewire
                        Livewire.dispatch('newOrder');
                    });
            }
        });
}