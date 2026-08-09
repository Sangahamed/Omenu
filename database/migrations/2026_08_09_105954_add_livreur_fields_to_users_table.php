<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_delivery_available')->default(false);
            $table->decimal('current_latitude', 10, 7)->nullable();
            $table->decimal('current_longitude', 10, 7)->nullable();
            $table->timestamp('last_location_update')->nullable();
            $table->string('vehicle_type')->nullable(); // vélo, moto, voiture
            $table->string('license_plate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_delivery_available',
                'current_latitude',
                'current_longitude',
                'last_location_update',
                'vehicle_type',
                'license_plate',
            ]);
        });
    }
};
