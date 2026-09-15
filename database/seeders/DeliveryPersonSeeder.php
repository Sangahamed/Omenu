<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Comptes livreurs de démonstration.
 *
 * Le rôle « delivery » et l'espace livreur existaient depuis le Sprint 4, mais
 * aucun compte ne le portait : le tableau de bord et le suivi GPS n'avaient
 * jamais pu être ouverts.
 */
class DeliveryPersonSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Mêmes identifiants que les autres comptes de démonstration.
     */
    public const PASSWORD = 'password';

    protected array $people = [
        [
            'name' => 'Koffi Livreur',
            'email' => 'livreur1@omenu.ci',
            'vehicle_type' => 'moto',
            'license_plate' => 'AB-1234-CI',
            // Cocody, point de départ plausible dans Abidjan.
            'current_latitude' => 5.3600,
            'current_longitude' => -3.9900,
            'is_delivery_available' => true,
        ],
        [
            'name' => 'Aya Coursière',
            'email' => 'livreur2@omenu.ci',
            'vehicle_type' => 'scooter',
            'license_plate' => 'CD-5678-CI',
            // Treichville.
            'current_latitude' => 5.2930,
            'current_longitude' => -4.0080,
            'is_delivery_available' => false,
        ],
    ];

    public function run(): void
    {
        foreach ($this->people as $person) {
            $user = User::firstOrCreate(
                ['email' => $person['email']],
                [
                    'name' => $person['name'],
                    'password' => Hash::make(self::PASSWORD),
                    'email_verified_at' => now(),
                ]
            );

            $user->forceFill([
                'vehicle_type' => $person['vehicle_type'],
                'license_plate' => $person['license_plate'],
                'current_latitude' => $person['current_latitude'],
                'current_longitude' => $person['current_longitude'],
                'last_location_update' => now(),
                'is_delivery_available' => $person['is_delivery_available'],
            ])->save();

            // syncRoles plutôt qu'assignRole : un compte créé par le hook
            // User::created reçoit déjà le rôle « client ».
            $user->syncRoles(['delivery']);

            $this->command?->info("Livreur : {$person['email']} / ".self::PASSWORD);
        }
    }
}
