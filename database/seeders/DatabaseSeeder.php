<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Rôles et Autorisations
        $this->call(RolesAndPermissionsSeeder::class);

        // 2. Compte Administrateur de test
        $admin = User::factory()->create([
            'name' => 'Admin OMenu',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'), // Restez sur un mot de passe simple en local
        ]);
        
        if (method_exists($admin, 'assignRole')) {
            $admin->assignRole('super-admin');
        }

        // 3. Création groupée des Restaurants et de leurs Menus (Optimisation des requêtes SQL)
        // La méthode has() évite la boucle foreach lente en base de données
        Restaurant::factory(30)
            ->has(Menu::factory()->count(5), 'menus')
            ->create();
    }
}