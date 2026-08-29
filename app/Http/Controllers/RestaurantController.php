<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RestaurantController extends Controller
{
    /**
     * Liste publique des restaurants (recherche + filtres simples).
     * GET /restaurants
     */
    public function index(Request $request)
    {
        $restaurants = Restaurant::query()
            ->where('is_active', true)
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = $request->string('q');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('cuisine_type', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('city'), fn ($query) => $query->where('city', $request->string('city')))
            ->when($request->filled('cuisine_type'), fn ($query) => $query->where('cuisine_type', $request->string('cuisine_type')))
            ->orderByDesc('average_rating')
            ->paginate(12)
            ->withQueryString();

        return view('restaurant.index', compact('restaurants'));
    }

    /**
     * Fiche restaurant + sa carte.
     * GET /restaurants/{slug}
     */
    public function show($slug)
    {
        $restaurant = Restaurant::with('menus')->where('slug', $slug)->firstOrFail();

        return view('restaurant.show', compact('restaurant'));
    }

    /**
     * Formulaire d'inscription d'un restaurant par son propriétaire.
     * GET /restaurant/create
     */
    public function create()
    {
        $this->authorize('create', Restaurant::class);

        return view('restaurant.create');
    }

    /**
     * Enregistre le nouveau restaurant, rattaché à l'utilisateur connecté.
     * POST /restaurant
     */
    public function store(Request $request)
    {
        $this->authorize('create', Restaurant::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'cuisine_type' => 'nullable|string|max:255',
            'price_range' => 'nullable|string|max:10',
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $i = 1;
        while (Restaurant::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$i}";
            $i++;
        }

        $restaurant = Restaurant::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'slug' => $slug,
            'country' => "Côte d'Ivoire",
            'is_active' => true,
            'is_verified' => false,
        ]);

        // Fait passer le client au statut restaurateur : c'est ce qui lui
        // donne accès à /restaurant/dashboard (protégé par role:restaurant).
        if (! $request->user()->hasRole('restaurant')) {
            $request->user()->assignRole('restaurant');
        }

        return redirect()->route('restaurant.dashboard')
            ->with('success', 'Votre restaurant "' . $restaurant->name . '" a été créé. Il sera vérifié prochainement par notre équipe.');
    }
}
