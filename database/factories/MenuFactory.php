<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Restaurant; // <-- CORRECTION : Importation manquante indispensable
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    /**
     * Le modèle correspondant à la factory.
     *
     * @var string
     */
    protected $model = Menu::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => ucfirst($this->faker->words(2, true)), // Ex: "Salade César", plus réaliste pour l'UI
            'description' => $this->faker->realText(100),   // Génère du texte plus représentatif pour tester les designs UI/UX
            'category' => $this->faker->randomElement(['Entrée', 'Plat', 'Dessert', 'Boisson']),
            'price' => $this->faker->numberBetween(2000, 15000), // Cohérent pour des prix en FCFA
            'old_price' => $this->faker->optional(0.3)->numberBetween(16000, 20000), // 30% de chance d'avoir une promotion
            'image' => null,
            'ingredients' => $this->faker->words(5), // Casté en array automatiquement par le modèle
            'allergens' => $this->faker->optional(0.4)->randomElements(['Gluten', 'Lactose', 'Arachides', 'Soja'], 2),
            'calories' => $this->faker->numberBetween(120, 950),
            'preparation_time' => $this->faker->numberBetween(10, 45),
            'is_available' => $this->faker->boolean(90), // 90% de disponibilité pour tester l'affichage UI du tag "Indisponible"
            'popularity' => $this->faker->numberBetween(0, 100),
        ];
    }
}