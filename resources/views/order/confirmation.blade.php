@extends('components.front.layouts.front')

@section('title', 'Confirmation de commande')

@section('content')
<div class="container mx-auto px-4 py-20 text-center max-w-2xl">
    <div class="inline-flex items-center justify-center w-20 h-20 bg-amber-500/10 border border-amber-500/30 rounded-full mb-6">
        <span class="text-4xl">🔱</span>
    </div>
    
    <h1 class="text-4xl font-serif font-bold text-amber-400 mb-4 tracking-wider">L'excellence en préparation</h1>
    <p class="text-slate-300 text-lg mb-10 italic">"Votre table est réservée dans nos cuisines, le chef vient de prendre possession de votre commande."</p>
    
    <div class="bg-slate-900 border border-amber-500/10 rounded-2xl p-8 text-left shadow-2xl mb-10">
        <h2 class="font-serif font-bold text-xl text-slate-100 mb-4 border-b border-amber-500/10 pb-4">
            Commande #{{ $order->id }}
        </h2>
        
        <div class="space-y-3 text-sm text-slate-400 font-mono">
            <div class="flex justify-between">
                <span>Établissement culinaire :</span>
                <span class="text-slate-200 font-semibold font-serif">{{ $order->restaurant->name }}</span>
            </div>
            <div class="flex justify-between">
                <span>Statut de prise en charge :</span>
                <span class="px-3 py-1 text-xs rounded-lg text-slate-950 font-bold {{ $order->status_color }}">
                    {{ $order->status_label }}
                </span>
            </div>
            <div class="flex justify-between">
                <span>Règlement :</span>
                <span class="text-slate-200 uppercase">{{ $order->payment_method }}</span>
            </div>
            <div class="flex justify-between border-t border-slate-800 pt-3 mt-3">
                <span class="font-serif text-slate-200 text-base">Valeur totale :</span>
                <span class="text-base text-amber-400 font-bold">{{ number_format($order->total) }} FCFA</span>
            </div>
        </div>
    </div>
    
    <a href="{{ route('home') }}" class="inline-block bg-gradient-to-r from-violet-600 to-cyan-600 hover:from-violet-700 hover:to-cyan-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-all duration-300 hover:scale-[1.02]">
        Retourner au salon principal
    </a>
</div>
@endsection