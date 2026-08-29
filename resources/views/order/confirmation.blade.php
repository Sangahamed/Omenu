@extends('components.front.layouts.front')

@section('title', 'Confirmation de commande')

@section('content')
<div class="container mx-auto px-4 py-20 text-center max-w-2xl">
    <div class="inline-flex items-center justify-center w-20 h-20 bg-brand-red-soft border border-brand-red/30 rounded-full mb-6">
        <span class="text-4xl">🔱</span>
    </div>

    <h1 class="text-4xl font-display font-semibold text-brand-black mb-4 tracking-tight">L'excellence en préparation</h1>
    <p class="text-ink-soft text-lg mb-10 italic">"Votre table est réservée dans nos cuisines, le chef vient de prendre possession de votre commande."</p>

    <div class="panier-confirmation bg-white border border-brand-black rounded-md p-8 text-left mb-10">
        <h2 class="font-display font-semibold text-xl text-brand-black mb-4 border-b border-border pb-4">
            Commande #{{ $order->id }}
        </h2>

        <div class="space-y-3 text-sm text-ink-soft font-mono">
            <div class="flex justify-between">
                <span>Établissement culinaire :</span>
                <span class="text-ink font-semibold font-display">{{ $order->restaurant->name }}</span>
            </div>
            <div class="flex justify-between">
                <span>Statut de prise en charge :</span>
                <span class="badge {{ $order->status_color }} font-bold">
                    {{ $order->status_label }}
                </span>
            </div>
            <div class="flex justify-between">
                <span>Règlement :</span>
                <span class="text-ink uppercase">{{ $order->payment_method }}</span>
            </div>
            <div class="flex justify-between border-t border-border pt-3 mt-3">
                <span class="font-display text-ink text-base">Valeur totale :</span>
                <span class="text-base text-brand-red font-bold">{{ number_format($order->total) }} FCFA</span>
            </div>
        </div>

        {{-- Confirmation WhatsApp : envoie au restaurant le récapitulatif de la commande --}}
        @if($order->restaurant->phone)
            @php
                $waPhone = preg_replace('/[^0-9]/', '', $order->restaurant->phone);
                $waMessage = "Bonjour, je viens de valider la commande #{$order->id} sur OMenu chez {$order->restaurant->name} pour un montant de " . number_format($order->total, 0, ',', ' ') . " FCFA. Merci de confirmer la prise en charge.";
            @endphp
            <a
                href="https://wa.me/{{ $waPhone }}?text={{ urlencode($waMessage) }}"
                target="_blank"
                rel="noopener"
                class="btn-whatsapp block text-center mt-6 py-3 rounded-sm font-semibold text-sm bg-whatsapp hover:bg-whatsapp-hover text-white transition-colors"
            >
                Confirmer par WhatsApp
            </a>
        @endif
    </div>

    <a href="{{ route('home') }}" class="inline-block bg-brand-black hover:bg-brand-black-2 text-white font-bold py-3 px-8 rounded-sm transition-all duration-300">
        Retourner au salon principal
    </a>
</div>
@endsection
