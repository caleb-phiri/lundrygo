<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Disable foreign key checks for SQLite
        DB::statement('PRAGMA foreign_keys = OFF');
        
        // Drop existing orders table
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        
        // Create orders table with correct foreign keys
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('rider_id')->nullable();
            $table->unsignedBigInteger('pickup_address_id');
            $table->unsignedBigInteger('delivery_address_id');
            $table->string('status')->default('pending');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->integer('total_items')->default(0);
            $table->timestamp('pickup_scheduled_at')->nullable();
            $table->timestamp('delivery_scheduled_at')->nullable();
            $table->text('special_instructions')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending');
            $table->boolean('is_express')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            // Add foreign keys only to tables that exist
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('rider_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('pickup_address_id')->references('id')->on('user_addresses')->onDelete('restrict');
            $table->foreign('delivery_address_id')->references('id')->on('user_addresses')->onDelete('restrict');
        });
        
        // Create order items table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('service_id');
            $table->string('service_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('laundry_services')->onDelete('restrict');
        });
        
        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down()
    {
        DB::statement('PRAGMA foreign_keys = OFF');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        DB::statement('PRAGMA foreign_keys = ON');
    }
};