<div class="min-h-screen bg-white text-ink">

    {{-- En-tête --}}
    <div class="bg-brand-black">
        <div class="container mx-auto px-4 md:px-8 py-16 md:py-20">
            <span class="text-xs uppercase tracking-widest font-semibold text-brand-red">Nous écrire</span>
            <h1 class="text-4xl md:text-5xl font-display font-semibold text-white tracking-tight mt-3 mb-3">
                Contact
            </h1>
            <p class="text-[#C9C9C6] text-sm md:text-base max-w-xl leading-relaxed">
                Une question sur une commande, un partenariat, un souci technique ?
                Notre équipe vous répond sous 24 h ouvrées.
            </p>
        </div>
    </div>

    <div class="container mx-auto px-4 md:px-8 py-12">
        <div class="grid lg:grid-cols-3 gap-10">

            {{-- Formulaire --}}
            <div class="lg:col-span-2">
                @if($sent)
                    <div class="border border-brand-black rounded-md p-10 text-center">
                        <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-brand-red-soft mb-4">
                            <i class="ri-check-line text-2xl text-brand-red"></i>
                        </span>
                        <h2 class="text-xl font-display font-semibold text-brand-black">Message envoyé</h2>
                        <p class="text-sm text-ink-soft mt-2 max-w-md mx-auto leading-relaxed">
                            Merci {{ $name }}. Nous revenons vers vous à l'adresse
                            <span class="text-ink font-medium">{{ $email }}</span> sous 24 h ouvrées.
                        </p>

                        <div class="flex flex-wrap justify-center gap-3 mt-6">
                            <button type="button" wire:click="sendAnother"
                                    class="bg-white border border-brand-black text-brand-black text-xs font-semibold uppercase tracking-wider px-5 py-3 rounded-sm hover:bg-brand-black hover:text-white transition-colors">
                                Écrire un autre message
                            </button>
                            <a href="{{ route('home') }}"
                               class="bg-brand-black text-white text-xs font-semibold uppercase tracking-wider px-5 py-3 rounded-sm hover:bg-brand-black-2 transition-colors">
                                Retour à l'accueil
                            </a>
                        </div>
                    </div>
                @else
                    <form wire:submit.prevent="submit" class="border border-border rounded-md p-6 md:p-8 space-y-5">
                        <div class="grid md:grid-cols-2 gap-5">
                            <div>
                                <label for="contact-name" class="block text-xs uppercase tracking-wider font-semibold text-ink-soft mb-1.5">
                                    Votre nom
                                </label>
                                <input id="contact-name" type="text" wire:model="name" autocomplete="name"
                                       class="w-full border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2.5 text-sm">
                                @error('name') <p class="text-[11px] text-brand-red mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="contact-email" class="block text-xs uppercase tracking-wider font-semibold text-ink-soft mb-1.5">
                                    Votre e-mail
                                </label>
                                <input id="contact-email" type="email" wire:model="email" autocomplete="email"
                                       class="w-full border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2.5 text-sm">
                                @error('email') <p class="text-[11px] text-brand-red mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="contact-subject" class="block text-xs uppercase tracking-wider font-semibold text-ink-soft mb-1.5">
                                Sujet
                            </label>
                            <input id="contact-subject" type="text" wire:model="subject"
                                   placeholder="Ex. Problème sur ma commande #123"
                                   class="w-full border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2.5 text-sm">
                            @error('subject') <p class="text-[11px] text-brand-red mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="contact-message" class="block text-xs uppercase tracking-wider font-semibold text-ink-soft mb-1.5">
                                Message
                            </label>
                            <textarea id="contact-message" wire:model="message" rows="7"
                                      placeholder="Décrivez votre demande…"
                                      class="w-full border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2.5 text-sm"></textarea>
                            @error('message') <p class="text-[11px] text-brand-red mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Piège à robots : masqué visuellement et ignoré des lecteurs d'écran. --}}
                        <div class="hidden" aria-hidden="true">
                            <label for="contact-website">Ne pas remplir</label>
                            <input id="contact-website" type="text" wire:model="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="flex items-center gap-4 pt-1">
                            <button type="submit" wire:loading.attr="disabled"
                                    class="bg-brand-black hover:bg-brand-black-2 text-white text-xs font-semibold uppercase tracking-wider px-6 py-3 rounded-sm transition-colors disabled:opacity-60">
                                <span wire:loading.remove wire:target="submit">Envoyer le message</span>
                                <span wire:loading wire:target="submit">Envoi…</span>
                            </button>
                            <p class="text-[11px] text-ink-soft">Réponse sous 24 h ouvrées.</p>
                        </div>
                    </form>
                @endif
            </div>

            {{-- Coordonnées --}}
            <div class="space-y-4">
                <div class="border border-border rounded-md p-6 space-y-5">
                    <h2 class="font-display font-semibold text-base text-brand-black">Nous joindre</h2>

                    @foreach([
                        ['ri-map-pin-line', 'Adresse', "Rue du Commerce, Plateau\nAbidjan, Côte d'Ivoire"],
                        ['ri-phone-line', 'Téléphone', "+225 27 20 00 00 00"],
                        ['ri-mail-line', 'E-mail', 'contact@omenu.ci'],
                        ['ri-time-line', 'Horaires', "Lundi au samedi\n8 h – 22 h"],
                    ] as [$icon, $label, $value])
                        <div class="flex gap-3">
                            <i class="{{ $icon }} text-brand-red text-lg mt-0.5 flex-shrink-0"></i>
                            <div class="min-w-0">
                                <p class="text-xs uppercase tracking-wider font-semibold text-ink-soft">{{ $label }}</p>
                                <p class="text-sm text-ink whitespace-pre-line leading-relaxed">{{ $value }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <a href="{{ route('restaurants.create') }}"
                   class="block border border-brand-red bg-brand-red-soft rounded-md p-5 hover:bg-brand-red hover:text-white transition-colors group">
                    <i class="ri-store-2-line text-xl text-brand-red group-hover:text-white"></i>
                    <h3 class="font-display font-semibold text-sm text-ink group-hover:text-white mt-2">
                        Vous êtes restaurateur ?
                    </h3>
                    <p class="text-xs text-ink-soft group-hover:text-white/80 mt-1 leading-relaxed">
                        Inscrivez votre établissement et recevez vos commandes sur OMenu.
                    </p>
                </a>
            </div>
        </div>
    </div>
</div>
