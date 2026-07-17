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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_person_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            $table->string('status')->default('pending'); 
            
            $table->string('payment_method')->nullable(); 
            $table->string('payment_id')->nullable(); // ID de la transaction
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded
            
            $table->text('delivery_address');
            $table->text('delivery_instructions')->nullable();
            $table->string('customer_phone');
            $table->string('customer_name');
            
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('restaurant_id');
            $table->index('user_id');
            $table->foreignId('parent_id')->nullable()->constrained('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
