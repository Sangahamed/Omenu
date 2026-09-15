/**
 * Notifications OMenu
 *
 * 1. Toasts : rend visible tous les `dispatch('notify', ...)` emis par les
 *    composants Livewire (panier, dashboards, admin...). Jusqu'ici aucun
 *    ecouteur n'existait, les evenements se perdaient donc silencieusement.
 * 2. Web Push : enregistrement du service worker et de l'abonnement navigateur.
 */

/* -------------------------------------------------------------------------
 * Toasts
 * ---------------------------------------------------------------------- */

const TOAST_STYLES = {
    success: { bar: '#1F7A4D', icon: 'ri-checkbox-circle-line' },
    error: { bar: '#A9271E', icon: 'ri-error-warning-line' },
    warning: { bar: '#B4791A', icon: 'ri-alert-line' },
    info: { bar: '#121212', icon: 'ri-information-line' },
};

function toastContainer() {
    let container = document.getElementById('omenu-toasts');

    if (!container) {
        container = document.createElement('div');
        container.id = 'omenu-toasts';
        container.setAttribute('role', 'status');
        container.setAttribute('aria-live', 'polite');
        container.className =
            'fixed top-4 right-4 z-[9999] flex flex-col gap-2 w-[min(22rem,calc(100vw-2rem))]';
        document.body.appendChild(container);
    }

    return container;
}

export function showToast(message, type = 'info', duration = 4500) {
    if (!message) return;

    const style = TOAST_STYLES[type] || TOAST_STYLES.info;
    const toast = document.createElement('div');

    toast.className =
        'flex items-start gap-3 bg-white border border-[#DEDDD9] rounded-sm shadow-lg px-4 py-3 ' +
        'text-sm text-[#121212] translate-x-4 opacity-0 transition-all duration-200';
    toast.style.borderLeft = '3px solid ' + style.bar;

    const icon = document.createElement('i');
    icon.className = style.icon + ' text-lg leading-none mt-0.5';
    icon.style.color = style.bar;

    const text = document.createElement('p');
    text.className = 'flex-1 leading-snug';
    text.textContent = message;

    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'text-[#5C5C58] hover:text-[#121212] leading-none';
    close.innerHTML = '&times;';
    close.setAttribute('aria-label', 'Fermer');

    toast.append(icon, text, close);
    toastContainer().appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.remove('translate-x-4', 'opacity-0');
    });

    const dismiss = () => {
        toast.classList.add('translate-x-4', 'opacity-0');
        setTimeout(() => toast.remove(), 200);
    };

    close.addEventListener('click', dismiss);
    const timer = setTimeout(dismiss, duration);
    toast.addEventListener('mouseenter', () => clearTimeout(timer));

    return toast;
}

/**
 * Livewire v3 transmet les parametres nommes sous forme d'objet, mais certains
 * composants envoient encore un tableau positionnel : on accepte les deux.
 */
function normalisePayload(payload) {
    const data = Array.isArray(payload) ? payload[0] : payload;

    if (typeof data === 'string') {
        return { message: data, type: 'info' };
    }

    return {
        message: (data && (data.message || data.body)) || '',
        type: (data && (data.type || data.level)) || 'info',
    };
}

function handleNotify(payload) {
    const { message, type } = normalisePayload(payload);
    showToast(message, type);
}

document.addEventListener('livewire:init', () => {
    window.Livewire.on('notify', handleNotify);
});

// Permet aussi un simple window.dispatchEvent(new CustomEvent('notify', {...}))
window.addEventListener('notify', (event) => handleNotify(event.detail));
window.omenuToast = showToast;

/* -------------------------------------------------------------------------
 * Web Push
 * ---------------------------------------------------------------------- */

const VAPID_PUBLIC_KEY = import.meta.env.VITE_VAPID_PUBLIC_KEY || '';

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = window.atob(base64);

    return Uint8Array.from([...raw].map((char) => char.charCodeAt(0)));
}

function csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');

    return meta ? meta.getAttribute('content') : '';
}

export const push = {
    /**
     * Le Web Push exige un service worker, l'API Push, une cle VAPID publique
     * et une origine sure (HTTPS, ou localhost en developpement).
     */
    isSupported() {
        return (
            'serviceWorker' in navigator &&
            'PushManager' in window &&
            Boolean(VAPID_PUBLIC_KEY)
        );
    },

    permission() {
        return typeof Notification === 'undefined' ? 'unsupported' : Notification.permission;
    },

    registration() {
        return navigator.serviceWorker.register('/sw.js', { scope: '/' });
    },

    async subscribe() {
        if (!this.isSupported()) {
            showToast(
                'Les notifications push ne sont pas disponibles sur ce navigateur.',
                'warning'
            );
            return null;
        }

        const permission = await Notification.requestPermission();

        if (permission !== 'granted') {
            showToast(
                'Notifications refusées. Vous pouvez les réactiver dans les réglages du navigateur.',
                'warning'
            );
            return null;
        }

        const registration = await this.registration();
        await navigator.serviceWorker.ready;

        const existing = await registration.pushManager.getSubscription();
        const subscription =
            existing ||
            (await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
            }));

        const payload = subscription.toJSON();
        const encodings = window.PushManager.supportedContentEncodings || ['aesgcm'];

        const response = await fetch('/push-subscriptions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                Accept: 'application/json',
            },
            body: JSON.stringify({
                endpoint: payload.endpoint,
                keys: payload.keys,
                contentEncoding: encodings[0],
            }),
        });

        if (!response.ok) {
            showToast("L'abonnement aux notifications a échoué.", 'error');
            return null;
        }

        window.dispatchEvent(
            new CustomEvent('push-subscription-changed', { detail: { subscribed: true } })
        );

        return subscription;
    },

    async unsubscribe() {
        if (!('serviceWorker' in navigator)) return;

        const registration = await navigator.serviceWorker.getRegistration('/sw.js');
        const subscription = registration
            ? await registration.pushManager.getSubscription()
            : null;

        if (!subscription) return;

        await fetch('/push-subscriptions', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                Accept: 'application/json',
            },
            body: JSON.stringify({ endpoint: subscription.endpoint }),
        });

        await subscription.unsubscribe();

        showToast('Notifications désactivées sur cet appareil.', 'info');
        window.dispatchEvent(
            new CustomEvent('push-subscription-changed', { detail: { subscribed: false } })
        );
    },

    async isSubscribed() {
        if (!('serviceWorker' in navigator)) return false;

        const registration = await navigator.serviceWorker.getRegistration('/sw.js');

        return Boolean(registration && (await registration.pushManager.getSubscription()));
    },
};

window.omenuPush = push;

// Reabonnement silencieux : si l'utilisateur a deja accorde la permission, on
// reenregistre l'abonnement pour qu'il survive au vidage du cache navigateur.
document.addEventListener('DOMContentLoaded', () => {
    if (!push.isSupported() || !document.body.dataset.userId) return;

    if (Notification.permission === 'granted') {
        push.subscribe().catch(() => {});
    }
});
