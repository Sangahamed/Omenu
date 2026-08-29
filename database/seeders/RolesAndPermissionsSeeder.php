<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        Permission::create(['name' => 'view restaurants']);
        Permission::create(['name' => 'create restaurants']);
        Permission::create(['name' => 'edit restaurants']);
        Permission::create(['name' => 'delete restaurants']);
        Permission::create(['name' => 'manage orders']);
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'view analytics']);

        // Rôles
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(['view restaurants', 'edit restaurants', 'manage users']);

        $restaurant = Role::create(['name' => 'restaurant']);
        $restaurant->givePermissionTo(['view restaurants', 'edit restaurants', 'manage orders']);

        $delivery = Role::create(['name' => 'delivery']);
        $delivery->givePermissionTo(['view restaurants', 'manage orders']);

        $client = Role::create(['name' => 'client']);
        $client->givePermissionTo(['view restaurants']);
    }
}