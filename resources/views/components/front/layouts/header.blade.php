<style>
    :root {
        --navy: #0a1628;
        --navy-mid: #0d1f38;
        --navy-card: #112244;
        --gold: #c9a84c;
        --gold-light: #e8c96d;
        --violet: #6c5ce7;
        --cyan: #00b4d8;
        --pink: #e056a0;
        --text: #e8edf5;
        --muted: #718096;
        --border: rgba(201, 168, 76, 0.12);
        --font-serif: 'Cormorant Garamond', Georgia, serif;
        --font-sans: 'Plus Jakarta Sans', system-ui, sans-serif;
    }

    .vr-header {
        background: rgba(10, 22, 40, 0.85);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid var(--border);
    }

    .vr-nav-link {
        position: relative;
        font-family: var(--font-serif);
        font-size: 16px;
        letter-spacing: 0.5px;
        color: #a0aec0;
        transition: color .25s ease;
    }

    .vr-nav-link:hover, .vr-nav-link.active {
        color: var(--gold-light);
    }

    .vr-nav-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 100%;
        height: 1px;
        background: linear-gradient(90deg, var(--gold), transparent);
        transition: right .3s ease;
    }

    .vr-nav-link:hover::after, .vr-nav-link.active::after {
        right: 0;
    }

    .vr-brand {
        font-family: var(--font-serif);
        font-weight: 700;
        letter-spacing: 1.5px;
        background: linear-gradient(135deg, #fff 30%, var(--gold-light));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>

<header class="vr-header sticky top-0 left-0 right-0 z-50 transition-all duration-300">
    <div class="container mx-auto px-4 h-20 flex items-center justify-between">
        <a href="/" class="flex items-center space-x-2 group">
            <span class="vr-brand text-2xl tracking-widest">OMENU</span>
        </a>

        <nav class="hidden md:flex items-center space-x-8">
            <a href="/" class="vr-nav-link active">Accueil</a>
            <a href="/restaurants" class="vr-nav-link">Découvrir</a>
            <a href="/offres" class="vr-nav-link">Offres</a>
            <a href="/contact" class="vr-nav-link">Contact</a>
        </nav>

        <div class="hidden md:flex items-center space-x-4">
            @guest
                <a href="/login" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">Connexion</a>
                <a href="/register" class="bg-gradient-to-r from-violet-600 to-cyan-600 hover:from-violet-700 hover:to-cyan-700 text-white text-xs px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-violet-950/40 transition-all duration-300 hover:scale-[1.02]">
                    Créer un compte
                </a>
            @endguest

            @auth
            <livewire:cart /> 
        
                <a href="/dashboard" class="border border-amber-500/20 hover:border-amber-500/60 bg-slate-900/60 text-amber-400 text-xs px-4 py-2 rounded-xl transition-all">
                    Mon Espace
                </a>
            @endauth
        </div>

        <button id="vrTabMenu" class="md:hidden text-slate-400 hover:text-amber-500 p-2 transition-colors focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
</header>

<div id="vrOverlay" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-opacity duration-300"></div>

<aside id="vrSidebar" class="fixed top-0 right-0 bottom-0 w-80 bg-[var(--navy-mid)] border-l border-amber-500/10 z-50 transform translate-x-full transition-transform duration-300 ease-in-out p-6 flex flex-col justify-between">
    <div>
        <div class="flex items-center justify-between mb-8 border-b border-amber-500/10 pb-4">
            <span class="vr-brand text-xl tracking-wider">OMENU</span>
            <button id="vrCloseBtn" class="text-slate-400 hover:text-amber-400 p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex flex-col space-y-4">
            <a href="/" class="text-lg font-serif text-slate-300 hover:text-amber-400 transition-colors py-2 border-b border-slate-900">Accueil</a>
            <a href="/restaurants" class="text-lg font-serif text-slate-300 hover:text-amber-400 transition-colors py-2 border-b border-slate-900">Découvrir</a>
            <a href="/offres" class="text-lg font-serif text-slate-300 hover:text-amber-400 transition-colors py-2 border-b border-slate-900">Offres</a>
            <a href="/contact" class="text-lg font-serif text-slate-300 hover:text-amber-400 transition-colors py-2">Contact</a>
        </nav>
    </div>

    <div class="mt-auto border-t border-amber-500/10 pt-6 space-y-3">
        @guest
            <a href="/login" class="block text-center w-full bg-slate-900 border border-slate-800 text-slate-300 py-2.5 rounded-xl font-medium transition text-sm">
                Connexion
            </a>
            <a href="/register" class="block text-center w-full bg-gradient-to-r from-violet-600 to-cyan-600 text-white py-2.5 rounded-xl font-medium transition text-sm shadow-md">
                Créer un compte
            </a>
        @endguest
        
        @auth
            <a href="/dashboard" class="block text-center w-full bg-slate-900 border border-amber-500/20 text-amber-400 py-2.5 rounded-xl font-medium transition text-sm">
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