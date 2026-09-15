<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rémunération des livreurs
    |--------------------------------------------------------------------------
    |
    | Commission fixe versée au livreur pour chaque course livrée, en FCFA.
    | Le tableau de bord des statistiques s'en sert pour estimer les gains ;
    | la valeur était écrite en dur dans la spec d'origine.
    |
    */

    'commission_per_order' => (int) env('DELIVERY_COMMISSION', 1000),

];
