@extends('components.front.layouts.front')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-2xl">
    <h1 class="text-3xl font-display font-semibold text-brand-black tracking-tight mb-2">
        Inscrire mon restaurant
    </h1>
    <p class="text-ink-soft text-sm mb-8">
        Renseignez les informations de votre établissement. Il sera visible dès validation par notre équipe.
    </p>

    @if ($errors->any())
        <div class="flash flash-error mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('restaurants.store') }}" class="bg-white border border-border rounded-md p-6 space-y-5">
        @csrf

        <div>
            <x-input-label for="name" value="Nom du restaurant" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" required autofocus />
        </div>

        <div>
            <x-input-label for="description" value="Description" />
            <textarea id="description" name="description" rows="3"
                      class="mt-1 block w-full border-border focus:border-brand-red focus:ring-brand-red rounded-sm">{{ old('description') }}</textarea>
        </div>

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <x-input-label for="address" value="Adresse" />
                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" value="{{ old('address') }}" required />
            </div>
            <div>
                <x-input-label for="city" value="Ville" />
                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" value="{{ old('city') }}" required />
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <x-input-label for="latitude" value="Latitude" />
                <x-text-input id="latitude" name="latitude" type="text" class="mt-1 block w-full" value="{{ old('latitude') }}" required placeholder="Ex: 5.359951" />
            </div>
            <div>
                <x-input-label for="longitude" value="Longitude" />
                <x-text-input id="longitude" name="longitude" type="text" class="mt-1 block w-full" value="{{ old('longitude') }}" required placeholder="Ex: -4.008256" />
            </div>
        </div>
        <p class="text-xs text-ink-soft -mt-3">
            Astuce : cherchez votre établissement sur Google Maps, clic droit sur le point exact → copiez les coordonnées.
        </p>

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <x-input-label for="phone" value="Téléphone (WhatsApp)" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" value="{{ old('phone') }}" placeholder="+225 07 00 00 00 00" />
            </div>
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email') }}" />
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-5">
            <div>
                <x-input-label for="cuisine_type" value="Type de cuisine" />
                <x-text-input id="cuisine_type" name="cuisine_type" type="text" class="mt-1 block w-full" value="{{ old('cuisine_type') }}" placeholder="Ex: Africain, Italien..." />
            </div>
            <div>
                <x-input-label for="price_range" value="Gamme de prix" />
                <select id="price_range" name="price_range" class="mt-1 block w-full border-border focus:border-brand-red focus:ring-brand-red rounded-sm">
                    <option value="">—</option>
                    <option value="€" {{ old('price_range') === '€' ? 'selected' : '' }}>€ (Économique)</option>
                    <option value="€€" {{ old('price_range') === '€€' ? 'selected' : '' }}>€€ (Standard)</option>
                    <option value="€€€" {{ old('price_range') === '€€€' ? 'selected' : '' }}>€€€ (Premium)</option>
                </select>
            </div>
        </div>

        <x-primary-button class="w-full justify-center py-3">
            Créer mon restaurant
        </x-primary-button>
    </form>
</div>
@endsection
