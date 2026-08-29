<style>
    /* Réplique des micro-interactions du header de production (soulignement rouge progressif) */
    .vr-nav-link {
        position: relative;
        padding-bottom: 2px;
    }
    .vr-nav-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 100%;
        height: 1px;
        background: theme('colors.brand.red.DEFAULT');
        transition: right .2s ease;
    }
    .vr-nav-link:hover::after, .vr-nav-link.active::after {
        right: 0;
    }
</style>

<header class="vr-header sticky top-0 left-0 right-0 z-50 bg-white border-b border-brand-black transition-all duration-300">
    <div class="container mx-auto px-4 h-20 flex items-center justify-between">
        <a href="/" class="flex items-center space-x-2 group">
            <span class="font-display text-2xl font-semibold tracking-tight text-brand-black">
                O<span class="text-brand-red">Menu</span>
            </span>
        </a>

        <nav class="hidden md:flex items-center space-x-8">
            <a href="/" class="vr-nav-link active text-sm text-ink-soft hover:text-brand-black">Accueil</a>
            <a href="/restaurants" class="vr-nav-link text-sm text-ink-soft hover:text-brand-black">Découvrir</a>
            <a href="/offres" class="vr-nav-link text-sm text-ink-soft hover:text-brand-black">Offres</a>
            <a href="/contact" class="vr-nav-link text-sm text-ink-soft hover:text-brand-black">Contact</a>
        </nav>

        <div class="hidden md:flex items-center space-x-4">
            @if(! auth()->check() || ! auth()->user()->hasRole('restaurant'))
                <a href="{{ route('restaurants.create') }}" class="text-xs font-semibold text-brand-red hover:text-brand-red-hover border border-brand-red hover:bg-brand-red-soft px-4 py-2 rounded-sm transition-all">
                    Devenir partenaire
                </a>
            @endif

            @guest
                <a href="/login" class="text-sm font-medium text-ink-soft hover:text-brand-black transition-colors">Connexion</a>
                <a href="/register" class="bg-brand-black hover:bg-brand-black-2 text-white text-xs px-5 py-2.5 rounded-sm font-semibold transition-all duration-300">
                    Créer un compte
                </a>
            @endguest

            @auth
                <livewire:cart />

                <a href="{{ route('space') }}" class="border border-brand-black hover:bg-brand-black hover:text-white text-brand-black text-xs px-4 py-2 rounded-sm transition-all">
                    Mon Espace
                </a>
            @endauth
        </div>

        <button id="vrTabMenu" class="md:hidden text-brand-black p-2 transition-colors focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
</header>

<div id="vrOverlay" class="fixed inset-0 bg-brand-black/60 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-opacity duration-300"></div>

<aside id="vrSidebar" class="fixed top-0 right-0 bottom-0 w-80 bg-white border-l border-brand-black z-50 transform translate-x-full transition-transform duration-300 ease-in-out p-6 flex flex-col justify-between">
    <div>
        <div class="flex items-center justify-between mb-8 border-b border-border pb-4">
            <span class="font-display text-xl font-semibold text-brand-black">O<span class="text-brand-red">Menu</span></span>
            <button id="vrCloseBtn" class="text-brand-black p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex flex-col space-y-4">
            <a href="/" class="text-lg font-display text-ink hover:text-brand-red transition-colors py-2 border-b border-border">Accueil</a>
            <a href="/restaurants" class="text-lg font-display text-ink hover:text-brand-red transition-colors py-2 border-b border-border">Découvrir</a>
            <a href="/offres" class="text-lg font-display text-ink hover:text-brand-red transition-colors py-2 border-b border-border">Offres</a>
            <a href="/contact" class="text-lg font-display text-ink hover:text-brand-red transition-colors py-2">Contact</a>
        </nav>
    </div>

    <div class="mt-auto border-t border-border pt-6 space-y-3">
        @if(! auth()->check() || ! auth()->user()->hasRole('restaurant'))
            <a href="{{ route('restaurants.create') }}" class="block text-center w-full border border-brand-red text-brand-red py-2.5 rounded-sm font-semibold transition text-sm">
                Devenir partenaire
            </a>
        @endif

        @guest
            <a href="/login" class="block text-center w-full bg-white border border-brand-black text-brand-black py-2.5 rounded-sm font-medium transition text-sm">
                Connexion
            </a>
            <a href="/register" class="block text-center w-full bg-brand-black text-white py-2.5 rounded-sm font-medium transition text-sm">
                Créer un compte
            </a>
        @endguest

        @auth
            <a href="{{ route('space') }}" class="block text-center w-full bg-white border border-brand-black text-brand-black py-2.5 rounded-sm font-medium transition text-sm">
                Mon Espace
            </a>
        @endauth
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const overlay = document.getElementById('vrOverlay');
        const sidebar = document.getElementById('vrSidebar');
        const tabMenu = document.getElementById('vrTabMenu');
        const closeBtn = document.getElementById('vrCloseBtn');

        function openMenu() {
            overlay.style.opacity = '1';
            overlay.style.pointerEvents = 'all';
            sidebar.style.transform = 'translateX(0)';
            document.body.style.overflow = 'hidden';
        }

        document.closeMenu = function() {
            overlay.style.opacity = '0';
            overlay.style.pointerEvents = 'none';
            sidebar.style.transform = 'translateX(100%)';
            document.body.style.overflow = '';
        }

        tabMenu?.addEventListener('click', openMenu);
        closeBtn?.addEventListener('click', document.closeMenu);
        overlay?.addEventListener('click', document.closeMenu);

        sidebar?.querySelectorAll('a, button').forEach(el => {
            el.addEventListener('click', document.closeMenu);
        });
    });
</script>
