<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h1 class="font-display text-2xl font-semibold text-brand-black tracking-tight mb-1">
        Bon retour parmi nous
    </h1>
    <p class="text-sm text-ink-soft mb-6">
        Connectez-vous pour retrouver vos adresses et vos commandes.
    </p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <div class="relative mt-1">
                <i class="ri-mail-line absolute left-3 top-1/2 -translate-y-1/2 text-ink-soft text-sm"></i>
                <x-text-input wire:model="form.email" id="email" class="block w-full pl-9" type="email" name="email" required autofocus autocomplete="username" placeholder="vous@exemple.com" />
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" value="Mot de passe" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-brand-red hover:text-brand-red-hover font-medium" href="{{ route('password.request') }}" wire:navigate>
                        Mot de passe oublié ?
                    </a>
                @endif
            </div>
            <div class="relative mt-1">
                <i class="ri-lock-line absolute left-3 top-1/2 -translate-y-1/2 text-ink-soft text-sm"></i>
                <x-text-input wire:model="form.password" id="password" class="block w-full pl-9"
                                type="password"
                                name="password"
                                required autocomplete="current-password" placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <label for="remember" class="flex items-center gap-2 cursor-pointer select-none">
            <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-border text-brand-black shadow-sm focus:ring-brand-red" name="remember">
            <span class="text-sm text-ink-soft">Se souvenir de moi</span>
        </label>

        <x-primary-button class="w-full justify-center py-3">
            Se connecter
        </x-primary-button>
    </form>

    <p class="text-center text-sm text-ink-soft mt-8">
        Pas encore de compte ?
        <a href="{{ route('register') }}" wire:navigate class="text-brand-red hover:text-brand-red-hover font-semibold">
            Créer un compte
        </a>
    </p>
</div>
