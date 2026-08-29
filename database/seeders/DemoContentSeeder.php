<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Jeu de données de démonstration : 10 restaurants abidjanais et 42 plats,
 * illustrés par les photos téléchargées dans storage/app/public.
 *
 * Les établissements sont fictifs : inventer des prix et des notes pour des
 * restaurants réels serait trompeur.
 */
class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $horaires = [
            'lundi' => '11:00-22:00', 'mardi' => '11:00-22:00',
            'mercredi' => '11:00-22:00', 'jeudi' => '11:00-22:00',
            'vendredi' => '11:00-23:30', 'samedi' => '11:00-23:30',
            'dimanche' => '12:00-21:00',
        ];

        foreach ($this->restaurants() as $index => $data) {
            $owner = User::create([
                'name' => $data['owner'],
                'email' => 'resto' . ($index + 1) . '@omenu.ci',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            if (method_exists($owner, 'assignRole')) {
                $owner->assignRole('restaurant');
            }

            $restaurant = Restaurant::create([
                'user_id' => $owner->id,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'description' => $data['description'],
                'logo' => null,
                'cover_image' => 'restaurants/' . $data['image'] . '.jpg',
                'address' => $data['address'],
                'city' => 'Abidjan',
                'country' => "Côte d'Ivoire",
                'latitude' => $data['lat'],
                'longitude' => $data['lng'],
                'phone' => $data['phone'],
                'email' => 'contact@' . Str::slug($data['name']) . '.ci',
                'opening_hours' => $horaires,
                'is_active' => true,
                'is_verified' => $index < 7,
                'average_rating' => $data['rating'],
                'total_orders' => $data['orders'],
                'cuisine_type' => $data['cuisine'],
                'price_range' => $data['price_range'],
            ]);

            foreach ($data['menus'] as $menu) {
                Menu::create([
                    'restaurant_id' => $restaurant->id,
                    'name' => $menu[0],
                    'description' => $menu[1],
                    'category' => $menu[2],
                    'price' => $menu[3],
                    'old_price' => $menu[4],
                    'image' => 'menus/' . $menu[5] . '.jpg',
                    'ingredients' => $menu[6],
                    'allergens' => $menu[7],
                    'calories' => $menu[8],
                    'preparation_time' => $menu[9],
                    'is_available' => true,
                    'popularity' => $menu[10],
                ]);
            }
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function restaurants(): array
    {
        return [
            [
                'name' => 'Le Maquis de la Lagune',
                'owner' => 'Koffi Aristide',
                'description' => "Maquis traditionnel au bord de la lagune Ébrié. Poisson braisé et attiéké préparés au feu de bois, dans une ambiance familiale depuis 1998.",
                'address' => 'Boulevard de Marseille, Zone 3',
                'lat' => 5.3120, 'lng' => -4.0080,
                'phone' => '+225 27 21 35 42 10',
                'cuisine' => 'Ivoirien', 'price_range' => '€€',
                'rating' => 4.6, 'orders' => 1284,
                'image' => 'resto-lagune',
                'menus' => [
                    ['Attiéké poisson braisé', "Tilapia braisé au feu de bois, attiéké frais, tomates et oignons marinés.", 'Plat', 4500, 5000, 'attieke-poisson', ['Attiéké', 'Tilapia', 'Tomate', 'Oignon', 'Piment'], ['Poisson'], 620, 25, 98],
                    ['Garba classique', "Le grand classique abidjanais : thon frit et semoule de manioc, piment et oignon.", 'Plat', 1500, null, 'garba', ['Attiéké', 'Thon', 'Piment', 'Oignon'], ['Poisson'], 540, 12, 95],
                    ['Sauce claire au poisson', "Bouillon léger et relevé, poisson frais, servi avec riz blanc.", 'Plat', 3500, null, 'sauce-claire', ['Poisson', 'Aubergine', 'Gombo', 'Piment'], ['Poisson'], 430, 30, 72],
                    ['Alloco piment', "Bananes plantains mûres frites, sauce piment maison à l'oignon.", 'Entrée', 1000, null, 'alloco', ['Banane plantain', 'Huile', 'Piment', 'Oignon'], [], 320, 10, 88],
                    ['Jus de bissap', "Infusion glacée d'hibiscus, légèrement sucrée, servie fraîche.", 'Boisson', 800, null, 'jus-bissap', ['Hibiscus', 'Sucre', 'Menthe'], [], 90, 5, 64],
                ],
            ],
            [
                'name' => 'Chez Tantie Cocody',
                'owner' => 'Aya N\'Guessan',
                'description' => "Cuisine ivoirienne de maison. Foutou, sauce graine et kedjenou mijotés comme au village, au cœur de Cocody.",
                'address' => 'Rue des Jardins, Cocody II Plateaux',
                'lat' => 5.3600, 'lng' => -3.9950,
                'phone' => '+225 27 22 41 08 76',
                'cuisine' => 'Ivoirien', 'price_range' => '€€',
                'rating' => 4.8, 'orders' => 2107,
                'image' => 'resto-cocody',
                'menus' => [
                    ['Foutou banane sauce graine', "Foutou de banane plantain pilé, sauce graine de palme au poisson fumé.", 'Plat', 4000, 4500, 'foutou-banane', ['Banane plantain', 'Graine de palme', 'Poisson fumé'], ['Poisson'], 780, 35, 96],
                    ['Foutou igname', "Igname pilée à la main, accompagnée d'une sauce arachide onctueuse.", 'Plat', 4200, null, 'foutou-igname', ['Igname', 'Arachide', 'Viande'], ['Arachide'], 810, 35, 84],
                    ['Kedjenou de poulet', "Poulet mijoté à l'étouffée dans sa canari, légumes et épices douces.", 'Plat', 5000, null, 'kedjenou-poulet', ['Poulet', 'Tomate', 'Oignon', 'Aubergine'], [], 590, 45, 92],
                    ['Sauce arachide', "Sauce à la pâte d'arachide, viande de bœuf tendre, riz blanc.", 'Plat', 3800, null, 'sauce-arachide', ['Arachide', 'Bœuf', 'Tomate'], ['Arachide'], 720, 30, 79],
                    ['Placali sauce gombo', "Pâte de manioc fermenté et sauce gombo filante au poisson.", 'Plat', 3500, null, 'placali-gombo', ['Manioc', 'Gombo', 'Poisson'], ['Poisson'], 650, 30, 68],
                ],
            ],
            [
                'name' => 'Maquis du Bonheur',
                'owner' => 'Yao Serge',
                'description' => "Maquis de quartier, braisés à toute heure. Poulet et poisson au charbon, ambiance musique live le week-end.",
                'address' => 'Rue Princesse, Yopougon',
                'lat' => 5.3380, 'lng' => -4.0710,
                'phone' => '+225 27 23 45 91 32',
                'cuisine' => 'Grillades', 'price_range' => '€',
                'rating' => 4.3, 'orders' => 1642,
                'image' => 'resto-maquis',
                'menus' => [
                    ['Poulet braisé entier', "Poulet fermier mariné 12 h, braisé au charbon, servi avec alloco.", 'Plat', 6000, 7000, 'poulet-braise', ['Poulet', 'Ail', 'Gingembre', 'Cube'], [], 890, 40, 94],
                    ['Tilapia braisé', "Tilapia entier farci aux épices, braisé et servi avec attiéké.", 'Plat', 4500, null, 'tilapia-braise', ['Tilapia', 'Épices', 'Citron'], ['Poisson'], 520, 30, 87],
                    ['Brochettes de bœuf', "Trois brochettes de bœuf mariné, oignons grillés et sauce piment.", 'Entrée', 2000, null, 'brochettes-boeuf', ['Bœuf', 'Oignon', 'Épices'], [], 380, 20, 76],
                    ['Igname frite', "Cossettes d'igname frites, croustillantes, sauce tomate pimentée.", 'Entrée', 1200, null, 'igname-frite', ['Igname', 'Huile', 'Sel'], [], 410, 15, 71],
                    ['Jus de gingembre', "Gingembre frais pressé, citron et un soupçon de sucre de canne.", 'Boisson', 700, null, 'jus-gingembre', ['Gingembre', 'Citron', 'Sucre'], [], 70, 5, 58],
                ],
            ],
            [
                'name' => 'Le Plateau Gourmet',
                'owner' => 'Marie-Claire Kouassi',
                'description' => "Table contemporaine au Plateau. Cuisine ivoirienne revisitée et classiques internationaux, service rapide le midi.",
                'address' => 'Avenue Chardy, Plateau',
                'lat' => 5.3250, 'lng' => -4.0230,
                'phone' => '+225 27 20 31 55 04',
                'cuisine' => 'Fusion', 'price_range' => '€€€',
                'rating' => 4.5, 'orders' => 963,
                'image' => 'resto-plateau',
                'menus' => [
                    ['Poulet DG', "Poulet sauté, plantains dorés et légumes croquants, sauce maison.", 'Plat', 6500, null, 'poulet-dg', ['Poulet', 'Plantain', 'Poivron', 'Carotte'], [], 760, 35, 89],
                    ['Riz gras', "Riz cuisiné dans son bouillon de viande, légumes fondants.", 'Plat', 3500, null, 'riz-gras', ['Riz', 'Bœuf', 'Tomate', 'Carotte'], [], 680, 30, 82],
                    ['Salade César', "Cœur de romaine, poulet grillé, copeaux de parmesan, croûtons.", 'Entrée', 3000, null, 'salade-cesar', ['Laitue', 'Poulet', 'Parmesan', 'Croûtons'], ['Gluten', 'Lait', 'Œuf'], 340, 15, 61],
                    ['Tiramisu', "Mascarpone, biscuit imbibé de café, cacao amer.", 'Dessert', 2500, null, 'tiramisu', ['Mascarpone', 'Café', 'Cacao', 'Œuf'], ['Lait', 'Œuf', 'Gluten'], 420, 10, 74],
                    ['Café expresso', "Arabica torréfié localement, servi serré.", 'Boisson', 1000, null, 'cafe-expresso', ['Café'], [], 5, 3, 45],
                ],
            ],
            [
                'name' => 'Street Food Treichville',
                'owner' => 'Ibrahim Touré',
                'description' => "Cuisine de rue authentique près du marché. Portions généreuses, prix doux, service continu de 7 h à minuit.",
                'address' => 'Avenue 16, Treichville',
                'lat' => 5.2930, 'lng' => -4.0090,
                'phone' => '+225 27 21 24 67 88',
                'cuisine' => 'Street food', 'price_range' => '€',
                'rating' => 4.2, 'orders' => 2854,
                'image' => 'resto-treichville',
                'menus' => [
                    ['Attiéké poulet', "Attiéké fin, cuisse de poulet grillée, sauce tomate crue.", 'Plat', 2000, null, 'attieke-poulet', ['Attiéké', 'Poulet', 'Tomate'], [], 610, 15, 91],
                    ['Shawarma poulet', "Galette garnie de poulet mariné, crudités et sauce à l'ail.", 'Plat', 2500, 3000, 'shawarma', ['Poulet', 'Pain', 'Ail', 'Crudités'], ['Gluten', 'Lait'], 640, 12, 90],
                    ['Club sandwich', "Pain de mie toasté, poulet, œuf, salade et frites en accompagnement.", 'Plat', 3000, null, 'club-sandwich', ['Pain', 'Poulet', 'Œuf', 'Salade'], ['Gluten', 'Œuf'], 700, 15, 66],
                    ['Frites maison', "Pommes de terre fraîches taillées et frites à la commande.", 'Entrée', 1000, null, 'frites-maison', ['Pomme de terre', 'Huile', 'Sel'], [], 380, 12, 73],
                    ['Salade de fruits', "Ananas, papaye, mangue et pastèque de saison.", 'Dessert', 1500, null, 'salade-fruits', ['Ananas', 'Papaye', 'Mangue', 'Pastèque'], [], 150, 8, 55],
                ],
            ],
            [
                'name' => 'Pizzeria Marcory',
                'owner' => 'Luca Bertrand',
                'description' => "Pâte levée 48 h et four à bois. Pizzas napolitaines et pâtes fraîches, livraison dans tout Marcory.",
                'address' => 'Boulevard VGE, Marcory',
                'lat' => 5.3010, 'lng' => -3.9880,
                'phone' => '+225 27 21 76 33 19',
                'cuisine' => 'Italien', 'price_range' => '€€',
                'rating' => 4.7, 'orders' => 1476,
                'image' => 'resto-marcory',
                'menus' => [
                    ['Pizza Margherita', "Tomate San Marzano, mozzarella fior di latte, basilic frais.", 'Plat', 5000, null, 'pizza-margherita', ['Farine', 'Tomate', 'Mozzarella', 'Basilic'], ['Gluten', 'Lait'], 850, 20, 93],
                    ['Spaghetti bolognaise', "Sauce mijotée trois heures, bœuf haché, parmesan râpé.", 'Plat', 4500, null, 'spaghetti-bolognaise', ['Pâtes', 'Bœuf', 'Tomate', 'Parmesan'], ['Gluten', 'Lait'], 720, 25, 80],
                    ['Crêpe au chocolat', "Crêpe fine, chocolat noir fondu, éclats de noisette.", 'Dessert', 2000, null, 'crepe-chocolat', ['Farine', 'Œuf', 'Lait', 'Chocolat'], ['Gluten', 'Œuf', 'Lait', 'Fruits à coque'], 390, 10, 69],
                    ['Salade de fruits frais', "Fruits de saison découpés à la commande.", 'Dessert', 1800, null, 'salade-fruits', ['Fruits de saison'], [], 140, 8, 48],
                ],
            ],
            [
                'name' => 'Le Grill de Yopougon',
                'owner' => 'Bakary Coulibaly',
                'description' => "Spécialiste des viandes au grill et des plats mijotés d'Afrique de l'Ouest. Terrasse ombragée de 60 couverts.",
                'address' => 'Carrefour Ananeraie, Yopougon',
                'lat' => 5.3450, 'lng' => -4.0820,
                'phone' => '+225 27 23 51 42 07',
                'cuisine' => 'Africain', 'price_range' => '€€',
                'rating' => 4.4, 'orders' => 1198,
                'image' => 'resto-yopougon',
                'menus' => [
                    ['Mafé de bœuf', "Ragoût à la pâte d'arachide, bœuf fondant, patate douce et riz.", 'Plat', 4000, null, 'mafe', ['Bœuf', 'Arachide', 'Patate douce'], ['Arachide'], 750, 40, 83],
                    ['Poulet yassa', "Poulet mariné au citron confit et aux oignons fondus, riz blanc.", 'Plat', 4200, 4800, 'yassa-poulet', ['Poulet', 'Oignon', 'Citron', 'Moutarde'], ['Moutarde'], 690, 35, 88],
                    ['Thiéboudienne', "Riz au poisson sénégalais, légumes mijotés et sauce tomate.", 'Plat', 4500, null, 'thieboudienne', ['Riz', 'Poisson', 'Chou', 'Carotte', 'Manioc'], ['Poisson'], 780, 45, 81],
                    ['Ndolé', "Feuilles de ndolé, arachide et crevettes, servi avec plantain.", 'Plat', 5000, null, 'ndole', ['Ndolé', 'Arachide', 'Crevette'], ['Arachide', 'Crustacés'], 640, 40, 70],
                    ['Banane plantain sautée', "Plantains mûrs sautés, oignons caramélisés.", 'Entrée', 1200, null, 'banane-plantain', ['Plantain', 'Oignon', 'Huile'], [], 300, 12, 62],
                ],
            ],
            [
                'name' => 'Riviera Fine Dining',
                'owner' => 'Nadège Bamba',
                'description' => "Table gastronomique de la Riviera. Produits locaux sublimés, carte renouvelée chaque saison, réservation conseillée.",
                'address' => 'Riviera Golf, Cocody',
                'lat' => 5.3700, 'lng' => -3.9600,
                'phone' => '+225 27 22 47 90 15',
                'cuisine' => 'Gastronomique', 'price_range' => '€€€',
                'rating' => 4.9, 'orders' => 587,
                'image' => 'resto-riviera',
                'menus' => [
                    ['Sushi mix (12 pièces)', "Sélection du chef : saumon, thon et crevette, riz vinaigré.", 'Plat', 12000, null, 'sushi-mix', ['Riz', 'Saumon', 'Thon', 'Crevette', 'Algue'], ['Poisson', 'Crustacés', 'Soja'], 520, 25, 77],
                    ['Couscous royal', "Semoule fine, agneau, merguez et légumes confits au bouillon.", 'Plat', 8500, null, 'couscous-royal', ['Semoule', 'Agneau', 'Merguez', 'Légumes'], ['Gluten'], 880, 45, 67],
                    ['Kedjenou revisité', "Poulet fermier basse température, jus corsé, légumes glacés.", 'Plat', 9000, null, 'kedjenou-poulet', ['Poulet', 'Légumes', 'Épices'], [], 610, 50, 72],
                    ['Tiramisu au café local', "Revisite au café ivoirien torréfié maison.", 'Dessert', 3500, null, 'tiramisu', ['Mascarpone', 'Café', 'Cacao'], ['Lait', 'Œuf', 'Gluten'], 430, 15, 63],
                ],
            ],
            [
                'name' => 'Bassam Beach Resto',
                'owner' => 'Fatou Diarra',
                'description' => "Pieds dans le sable à Grand-Bassam. Poissons du jour, fruits de mer et cocktails face à l'océan.",
                'address' => 'Route de Grand-Bassam, km 12',
                'lat' => 5.2050, 'lng' => -3.7380,
                'phone' => '+225 27 21 30 12 45',
                'cuisine' => 'Fruits de mer', 'price_range' => '€€',
                'rating' => 4.6, 'orders' => 1035,
                'image' => 'resto-bassam',
                'menus' => [
                    ['Soupe de poisson', "Bouillon corsé aux poissons de roche, relevé au piment frais.", 'Entrée', 3000, null, 'soupe-poisson', ['Poisson', 'Tomate', 'Piment', 'Ail'], ['Poisson'], 280, 25, 75],
                    ['Attiéké poisson du jour', "Pêche du matin braisée, attiéké et sauce tomate crue.", 'Plat', 5500, null, 'attieke-poisson', ['Attiéké', 'Poisson', 'Tomate'], ['Poisson'], 640, 30, 86],
                    ['Sauce graine au poisson fumé', "Sauce de graine de palme onctueuse, poisson fumé, riz.", 'Plat', 4500, null, 'sauce-graine', ['Graine de palme', 'Poisson fumé', 'Riz'], ['Poisson'], 730, 40, 78],
                    ['Akpessi d\'igname', "Igname mijotée à l'huile rouge, poisson et légumes du jardin.", 'Plat', 3500, null, 'akpessi', ['Igname', 'Huile rouge', 'Poisson'], ['Poisson'], 590, 30, 59],
                    ['Jus de bissap glacé', "Hibiscus infusé, menthe fraîche, servi sur glace.", 'Boisson', 1000, null, 'jus-bissap', ['Hibiscus', 'Menthe', 'Sucre'], [], 95, 5, 57],
                ],
            ],
            [
                'name' => 'Café Zone 4',
                'owner' => 'Éric Adjé',
                'description' => "Café-restaurant de quartier. Petit-déjeuner, formules déjeuner et pâtisseries maison, wifi et espace de travail.",
                'address' => 'Rue Paul Langevin, Zone 4',
                'lat' => 5.2980, 'lng' => -3.9970,
                'phone' => '+225 27 21 25 88 63',
                'cuisine' => 'Café', 'price_range' => '€€',
                'rating' => 4.5, 'orders' => 1721,
                'image' => 'resto-zone4',
                'menus' => [
                    ['Burger maison', "Bœuf haché du jour, cheddar affiné, oignons confits, frites.", 'Plat', 5000, 5500, 'burger-maison', ['Bœuf', 'Pain', 'Cheddar', 'Oignon'], ['Gluten', 'Lait', 'Œuf'], 920, 20, 92],
                    ['Poulet pané croustillant', "Filets de poulet panés, sauce blanche, frites maison.", 'Plat', 4000, null, 'poulet-pane', ['Poulet', 'Chapelure', 'Épices'], ['Gluten', 'Œuf'], 780, 20, 85],
                    ['Kabato', "Pâte de mil traditionnelle, sauce de saison relevée.", 'Plat', 2500, null, 'kabato', ['Mil', 'Sauce', 'Épices'], [], 520, 25, 51],
                    ['Sauce gnagnan', "Aubergines amères mijotées, poisson fumé, riz blanc.", 'Plat', 3000, null, 'gnagnan', ['Aubergine', 'Poisson fumé', 'Tomate'], ['Poisson'], 460, 30, 54],
                    ['Café expresso', "Double ristretto, torréfaction locale.", 'Boisson', 1000, null, 'cafe-expresso', ['Café'], [], 5, 3, 60],
                ],
            ],
        ];
    }
}
