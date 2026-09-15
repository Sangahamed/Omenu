@extends('components.front.layouts.front')

@section('title', 'Validation en cours')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-2xl">

        <div class="bg-white border border-brand-black rounded-md overflow-hidden">
            <div class="bg-brand-black px-8 py-10 text-center">
                <span class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/10 mb-4">
                    <i class="ri-time-line text-3xl text-white"></i>
                </span>
                <h1 class="text-2xl md:text-3xl font-display font-semibold text-white tracking-tight">
                    Votre restaurant est en cours de validation
                </h1>
                <p class="text-[#C9C9C6] text-sm mt-3 max-w-md mx-auto leading-relaxed">
                    Notre équipe vérifie les informations de
                    <span class="text-white font-semibold">{{ $restaurant->name }}</span>.
                    Vous recevrez une notification dès que la fiche sera publiée.
                </p>
            </div>

            <div class="px-8 py-8 space-y-6">

                {{-- Étapes du parcours --}}
                <ol class="space-y-4">
                    <li class="flex gap-4">
                        <span class="flex-shrink-0 w-7 h-7 rounded-full bg-brand-black text-white text-xs font-bold flex items-center justify-center">
                            <i class="ri-check-line"></i>
                        </span>
                        <div>
                            <p class="text-sm font-display font-semibold text-ink">Dossier reçu</p>
                            <p class="text-xs text-ink-soft mt-0.5">
                                Soumis le {{ $restaurant->created_at->translatedFormat('d F Y à H:i') }}.
                            </p>
                        </div>
                    </li>

                    <li class="flex gap-4">
                        <span class="flex-shrink-0 w-7 h-7 rounded-full bg-brand-red text-white text-xs font-bold flex items-center justify-center animate-pulse">
                            2
                        </span>
                        <div>
                            <p class="text-sm font-display font-semibold text-ink">Vérification par notre équipe</p>
                            <p class="text-xs text-ink-soft mt-0.5">
                                Adresse, coordonnées et conformité de l'établissement. Comptez 24 à 48 h ouvrées.
                            </p>
                        </div>
                    </li>

                    <li class="flex gap-4 opacity-50">
                        <span class="flex-shrink-0 w-7 h-7 rounded-full border border-border text-ink-soft text-xs font-bold flex items-center justify-center">
                            3
                        </span>
                        <div>
                            <p class="text-sm font-display font-semibold text-ink">Mise en ligne</p>
                            <p class="text-xs text-ink-soft mt-0.5">
                                Votre tableau de bord et votre carte deviennent accessibles aux clients.
                            </p>
                        </div>
                    </li>
                </ol>

                {{-- Récapitulatif --}}
                <div class="border border-border rounded-sm divide-y divide-border text-sm">
                    @foreach([
                        'Établissement' => $restaurant->name,
                        'Adresse' => $restaurant->address.', '.$restaurant->city,
                        'Cuisine' => $restaurant->cuisine_type ?: '—',
                        'Téléphone' => $restaurant->phone ?: '—',
                    ] as $label => $value)
                        <div class="flex justify-between gap-4 px-4 py-2.5">
                            <span class="text-xs uppercase tracking-wider text-ink-soft font-semibold">{{ $label }}</span>
                            <span class="text-ink text-right">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="p-4 bg-brand-red-soft border border-brand-red/20 rounded-sm flex gap-3 text-xs text-ink-soft">
                    <i class="ri-notification-3-line text-brand-red text-lg flex-shrink-0"></i>
                    <p class="leading-relaxed">
                        Activez les notifications depuis la cloche du menu pour être prévenu
                        immédiatement, même quand cet onglet est fermé.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('home') }}"
                       class="flex-1 text-center bg-white border border-brand-black text-brand-black text-sm font-semibold px-4 py-2.5 rounded-sm hover:bg-brand-black hover:text-white transition-colors">
                        Retour à l'accueil
                    </a>

                    <a href="{{ route('restaurants.show', $restaurant->slug) }}"
                       class="flex-1 text-center bg-brand-black text-white text-sm font-semibold px-4 py-2.5 rounded-sm hover:bg-brand-black-2 transition-colors">
                        Prévisualiser ma fiche
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
