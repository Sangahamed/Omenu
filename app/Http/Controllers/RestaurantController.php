<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use App\Notifications\RestaurantSubmitted;
use App\Support\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
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
            ->published()
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
    public function show(Request $request, $slug)
    {
        $restaurant = Restaurant::with('menus')->where('slug', $slug)->firstOrFail();

        // Une fiche non validee reste accessible a son proprietaire et aux
        // administrateurs (pour relecture), mais pas au public.
        if (! $restaurant->is_verified || ! $restaurant->is_active) {
            $user = $request->user();

            $canPreview = $user
                && ($user->id === $restaurant->user_id || $user->hasRole(['super-admin', 'admin']));

            abort_unless($canPreview, 404);
        }

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

        $restaurant = Restaurant::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'slug' => Restaurant::uniqueSlug($validated['name']),
            'country' => "Côte d'Ivoire",
            'is_active' => true,
            'is_verified' => false,
        ]);

        // Fait passer le client au statut restaurateur : c'est ce qui lui
        // donne accès à /restaurant/dashboard (protégé par role:restaurant).
        if (! $request->user()->hasRole('restaurant')) {
            $request->user()->assignRole('restaurant');
        }

        // Prévient les administrateurs qu'une fiche attend leur validation :
        // sans cela, la demande n'apparaissait nulle part dans le back-office.
        $admins = User::role(['super-admin', 'admin'])->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new RestaurantSubmitted($restaurant));
        }

        // Le tableau de bord restaurateur n'a pas de sens tant que la fiche
        // n'est pas validée : on dirige vers l'écran d'attente dédié.
        return redirect()->route('restaurant.pending')
            ->with('success', 'Votre restaurant "' . $restaurant->name . '" a été soumis. Notre équipe le valide sous 24 à 48 h.');
    }

    /**
     * Écran d'attente affiché tant que la fiche n'est pas validée.
     * GET /restaurant/en-attente
     */
    public function pending(Request $request)
    {
        $restaurant = Restaurant::where('user_id', $request->user()->id)->latest()->first();

        if (! $restaurant) {
            return redirect()->route('restaurants.create');
        }

        // Fiche déjà validée : l'écran d'attente n'a plus lieu d'être.
        if ($restaurant->is_verified) {
            return redirect()->route('restaurant.dashboard');
        }

        return view('restaurant.pending', compact('restaurant'));
    }

    /**
     * Affiche du restaurant : une page prête à imprimer avec le QR code qui
     * mène à la fiche publique (carte + commande / réservation).
     * GET /restaurants/{slug}/qr-code
     */
    public function qrCode($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

        $url = route('restaurants.show', $restaurant->slug);

        return view('restaurant.qrcode', [
            'restaurant' => $restaurant,
            'url' => $url,
            'qr' => QrCode::dataUri($url, 520),
        ]);
    }

    /**
     * Le même QR code en SVG brut, pour l'intégrer à un flyer ou un menu.
     * GET /restaurants/{slug}/qr-code.svg
     */
    public function qrCodeSvg(Request $request, $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

        $svg = QrCode::svg(route('restaurants.show', $restaurant->slug), 1024);

        $disposition = $request->boolean('download')
            ? 'attachment; filename="qr-'.Str::slug($restaurant->name).'.svg"'
            : 'inline';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => $disposition,
        ]);
    }
}
