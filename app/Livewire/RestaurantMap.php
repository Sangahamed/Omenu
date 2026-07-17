<?php

namespace App\Livewire;

use App\Models\Restaurant;
use App\Models\Menu; 
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.front.layouts.front')]
class RestaurantMap extends Component
{
    public $search = '';
    public $cuisine = '';
    public $city = '';
    public $priceRange = '';
    
    // Propriétés de statut simples
    public $total = 0;
    public $viewMode = 'map';
    public $isSidebarOpen = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'cuisine' => ['except' => ''],
        'city' => ['except' => ''],
        'priceRange' => ['except' => ''],
        'viewMode' => ['except' => 'map'],
    ];

    public function mount()
    {
       
    }

    public function getRestaurantsData()
    {
        $query = Restaurant::query()
            ->where('is_active', true)
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

        if ($this->priceRange) {
            $query->where('price_range', $this->priceRange);
        }

        $restaurants = $query->limit(200)->get();
        $this->total = $restaurants->count();

        $features = [];
        foreach ($restaurants as $r) {
            $features[] = [
                'type' => 'Feature',
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
                    'price_range' => $r->price_range,
                    'rating' => $r->average_rating,
                    'image' => $r->logo ? asset('storage/'.$r->logo) : null,
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
        $this->reset(['search', 'cuisine', 'city', 'priceRange']);
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

        return view('livewire.restaurant-map', [
            'menus' => $menusData
        ]);
    }
}