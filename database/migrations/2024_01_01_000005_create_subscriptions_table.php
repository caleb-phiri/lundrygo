<?php
// database/migrations/2024_01_01_000005_create_subscriptions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('subscription_plans');
            $table->string('subscription_number')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('remaining_baskets')->default(0);
            $table->integer('used_iron_upgrades')->default(0);
            $table->boolean('blanket_cleaning_used')->default(false);
            $table->enum('status', ['active', 'paused', 'expired', 'cancelled'])->default('active');
            $table->decimal('amount_paid', 10, 2);
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};