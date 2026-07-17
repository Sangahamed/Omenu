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
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // propriétaire
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->string('logo')->nullable();
        $table->string('cover_image')->nullable();
        $table->string('address');
        $table->string('city');
        $table->string('country')->default('Côte d\'Ivoire');
        $table->decimal('latitude', 10, 7);
        $table->decimal('longitude', 10, 7);
        $table->string('phone')->nullable();
        $table->string('email')->nullable();
        $table->json('opening_hours')->nullable(); // JSON
        $table->boolean('is_active')->default(true);
        $table->boolean('is_verified')->default(false);
        $table->float('average_rating')->default(0);
        $table->integer('total_orders')->default(0);
        $table->string('cuisine_type')->nullable();
        $table->string('price_range')->nullable(); // €, €€, €€€
        $table->timestamps();
        
        $table->index(['latitude', 'longitude']);
        $table->index('city');
        $table->index('cuisine_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
