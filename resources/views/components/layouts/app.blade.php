{{--
    Mise en page par défaut des composants Livewire pleine page.

    Livewire 3 cherche « components.layouts.app » quand un composant routé
    ne précise aucun layout ; sans ce fichier il lève « Livewire page
    component layout view not found » (erreur 500). On délègue à la mise en
    page principale plutôt que de la dupliquer.
--}}
@include('layouts.app', ['slot' => $slot, 'header' => $header ?? null])
