import './bootstrap';
import './restaurant-map';
import { showToast } from './notifications';

document.addEventListener('alpine:init', () => {
    if (window.Alpine) {
        // Plugins Alpine si nécessaire via l'instance Livewire
    }
});




// window.Echo est instancie une seule fois, dans resources/js/echo.js
// (importe par bootstrap.js, donc deja execute ici). Le dupliquer ouvrait
// une seconde connexion WebSocket pour rien.

// Temps reel restaurateur : nouvelle commande poussee par Reverb.
// (La bibliotheque Toastify n'a jamais ete installee : on passe par le systeme
// de toasts maison, qui sert aussi aux evenements `notify` de Livewire.)
if (window.authUserId && window.Echo) {
    fetch('/restaurant-id', { headers: { Accept: 'application/json' } })
        .then((res) => (res.ok ? res.json() : null))
        .then((data) => {
            if (!data || !data.restaurant_id) return;

            window.Echo.private(`restaurant.${data.restaurant_id}`).listen('OrderPlaced', (e) => {
                showToast(
                    `Nouvelle commande #${e.order_id} de ${e.customer_name} (${e.total} FCFA)`,
                    'success',
                    10000
                );

                window.Livewire.dispatch('newOrder');
                window.Livewire.dispatch('notificationsRefresh');
            });
        })
        .catch(() => {});
}