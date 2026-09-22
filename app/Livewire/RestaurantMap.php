<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\Menu; 
use Livewire\Component;

class RestaurantMap extends Component
{
    public $search = '';
    public $cuisine = '';
    public $city = '';
    
    // Propriétés de statut simples
    public $total = 0;
    public $viewMode = 'map';

    // Fermee par defaut : sur mobile la sidebar occupe toute la largeur, donc
    // l'ouvrir au chargement faisait demarrer la page sur les filtres au lieu
    // de la carte. Sur desktop l'affichage ne depend pas de ce drapeau
    // (la classe md:translate-x-0 garde la colonne visible).
    public $isSidebarOpen = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'cuisine' => ['except' => ''],
        'city' => ['except' => ''],
        'viewMode' => ['except' => 'map'],
    ];

    public function mount()
    {
       
    }

    public function getRestaurantsData()
    {
        $query = Restaurant::query()
            ->published()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('city', 'like', "%{$this->search}%")
                  ->orWhere('cuisine_type', 'like', "%{$this->search}%");
            });
        }

        if ($this->cuisine) {
            $query->where('cuisine_type', $this->cuisine);
        }

        if ($this->city) {
            $query->where('city', 'like', "%{$this->city}%");
        }

        $restaurants = $query->limit(200)->get();
        $this->total = $restaurants->count();

        $features = [];
        foreach ($restaurants as $r) {
            $features[] = [
                'type' => 'Point',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$r->longitude, (float)$r->latitude]
                ],
                'properties' => [
                    'id' => $r->id,
                    'name' => $r->name,
                    'address' => $r->address,
                    'city' => $r->city,
                    'cuisine' => $r->cuisine_type,
                    'rating' => $r->average_rating,
                    // La photo de couverture est l'illustration renseignee en base ;
                    // le logo n'est qu'un repli (il est nul sur la quasi-totalite
                    // des fiches, d'ou les popups sans image).
                    'image' => $r->cover_image
                        ? asset('storage/'.$r->cover_image)
                        : ($r->logo ? asset('storage/'.$r->logo) : null),
                    'url' => route('restaurants.show', $r->slug),
                    'is_favorited' => auth()->check() ? $r->favoritedBy(auth()->id()) : false,
                ]
            ];
        }

        return [
            'features' => $features,
            'ids' => $restaurants->pluck('id')->toArray()
        ];
    }

    public function resetFilters()
    {
        $this->reset(['search', 'cuisine', 'city']);
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'map' ? 'list' : 'map';
    }

    public function toggleSidebar()
    {
        $this->isSidebarOpen = !$this->isSidebarOpen;
    }

    public function render()
    {
        $data = $this->getRestaurantsData();

        // Envoi des coordonnées à la carte via un dispatch d'événement fluide
        $this->dispatch('restaurantsUpdated', restaurants: $data['features']);

        // Récupération des menus uniquement pour le rendu HTML (évite de surcharger la session/l'état)
        $menusData = Menu::whereIn('restaurant_id', $data['ids'])
            ->with('restaurant')
            ->limit(48)
            ->get()
            ->map(function ($menu) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'description' => $menu->description,
                    'price' => number_format($menu->price, 0, ',', ' '),
                    'image' => $menu->image ? asset('storage/'.$menu->image) : null,
                    'restaurant_name' => $menu->restaurant->name,
                    'restaurant_slug' => $menu->restaurant->slug,
                    'category' => $menu->category,
                    'is_available' => $menu->is_available,
                ];
            });

        // Les types de cuisine viennent de la base : une liste écrite en dur
        // dans la vue finit toujours par diverger des valeurs réelles.
        $cuisines = Restaurant::published()
            ->whereNotNull('cuisine_type')
            ->distinct()
            ->orderBy('cuisine_type')
            ->pluck('cuisine_type');

        return view('livewire.restaurant-map', [
            'menus' => $menusData,
            'cuisines' => $cuisines,
        ])->extends('components.front.layouts.front');
    }
}