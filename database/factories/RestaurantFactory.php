<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->company,
            'slug' => $this->faker->slug,
            'description' => $this->faker->paragraph,
            'logo' => null,
            'cover_image' => null,
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'country' => 'Côte d\'Ivoire',
            'latitude' => $this->faker->latitude(5.2, 5.5),
            'longitude' => $this->faker->longitude(-4.2, -3.8),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'opening_hours' => json_encode(['monday' => '08:00-22:00', 'tuesday' => '08:00-22:00']),
            'is_active' => true,
            'is_verified' => false,
            'average_rating' => $this->faker->randomFloat(1, 0, 5),
            'total_orders' => $this->faker->numberBetween(0, 100),
            'cuisine_type' => $this->faker->randomElement(['italien', 'fast-food', 'africain', 'chinois']),
            'price_range' => $this->faker->randomElement(['€', '€€', '€€€']),
        ];
    }
}