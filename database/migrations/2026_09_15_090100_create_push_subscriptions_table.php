<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // L'endpoint fourni par le navigateur identifie l'abonnement de
            // maniere unique : c'est la cle naturelle pour eviter les doublons
            // quand le meme navigateur se reabonne.
            $table->text('endpoint');
            $table->string('endpoint_hash', 64)->unique();

            $table->string('public_key')->nullable();  // p256dh
            $table->string('auth_token')->nullable();  // auth
            $table->string('content_encoding')->default('aesgcm');
            $table->string('user_agent')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
