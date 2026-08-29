<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Clés API Stripe
    |--------------------------------------------------------------------------
    | Renseignées via les variables d'environnement STRIPE_KEY / STRIPE_SECRET.
    | Ce fichier était référencé (config('stripe.secret_key')) mais n'existait
    | pas — le paiement Stripe était donc silencieusement non fonctionnel.
    */

    'key' => env('STRIPE_KEY'),
    'secret_key' => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
];
