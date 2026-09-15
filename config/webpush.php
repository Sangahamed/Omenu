<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cles VAPID
    |--------------------------------------------------------------------------
    |
    | Elles authentifient le serveur aupres des services de push (FCM, Mozilla,
    | WNS...). Generez-les une fois avec `php artisan webpush:vapid` puis
    | collez-les dans le .env. Sans elles, le canal web push reste inactif :
    | les toasts et les notifications en base continuent de fonctionner.
    |
    */

    'vapid' => [
        'subject' => env('VAPID_SUBJECT', env('APP_URL', 'http://localhost')),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Duree de vie et urgence par defaut des messages push
    |--------------------------------------------------------------------------
    */

    'ttl' => (int) env('WEBPUSH_TTL', 3600),
    'urgency' => env('WEBPUSH_URGENCY', 'normal'),

    /*
    |--------------------------------------------------------------------------
    | Icone affichee dans la notification systeme
    |--------------------------------------------------------------------------
    */

    'icon' => env('WEBPUSH_ICON', '/favicon.ico'),
    'badge' => env('WEBPUSH_BADGE', '/favicon.ico'),

    /*
    |--------------------------------------------------------------------------
    | Fichier de configuration OpenSSL (Windows / XAMPP)
    |--------------------------------------------------------------------------
    |
    | Le chiffrement Web Push genere une paire de cles ECDH ephemere a chaque
    | envoi. Sur les installations XAMPP, openssl ne trouve pas son openssl.cnf
    | et la generation echoue (« configuration file routines::no such file »).
    |
    | PHP lit OPENSSL_CONF au demarrage du module : la variable doit donc etre
    | posee dans l'environnement du systeme, pas depuis le code. Ce reglage
    | sert uniquement au diagnostic (`php artisan webpush:doctor`), qui indique
    | la commande exacte a lancer. Laissez vide sur un serveur Linux.
    |
    */

    'openssl_conf' => env('OPENSSL_CONF_PATH'),

];
