<?php

namespace Tests;

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        /*
         * AppServiceProvider attribue le rôle « client » à chaque utilisateur
         * créé. Sans les rôles en base, la moindre User::factory()->create()
         * lève « There is no role named `client` », ce qui mettait en échec
         * toute la suite d'authentification.
         */
        if (in_array(RefreshDatabase::class, class_uses_recursive($this), true)) {
            $this->seed(RolesAndPermissionsSeeder::class);
        }
    }
}
