/**
 * Service worker OMenu — reception des notifications Web Push.
 *
 * Il est servi depuis la racine (/sw.js) pour couvrir tout le site.
 */

self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', (event) => event.waitUntil(self.clients.claim()));

self.addEventListener('push', (event) => {
    let payload = {};

    try {
        payload = event.data ? event.data.json() : {};
    } catch (error) {
        payload = { title: 'OMenu', body: event.data ? event.data.text() : '' };
    }

    const title = payload.title || 'OMenu';

    event.waitUntil(
        self.registration.showNotification(title, {
            body: payload.body || '',
            icon: payload.icon || '/favicon.ico',
            badge: payload.badge || '/favicon.ico',
            tag: payload.tag || 'omenu',
            renotify: true,
            data: { url: payload.url || '/' },
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const target = (event.notification.data && event.notification.data.url) || '/';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            // Si un onglet OMenu est deja ouvert, on le reutilise plutot que
            // d'en empiler un nouveau a chaque notification.
            for (const client of clientList) {
                if ('focus' in client) {
                    client.navigate(target);
                    return client.focus();
                }
            }

            if (self.clients.openWindow) {
                return self.clients.openWindow(target);
            }
        })
    );
});
