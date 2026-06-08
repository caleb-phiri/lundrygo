<?php
// database/migrations/2024_01_01_000003_create_subscription_plans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Weekly Basic, Weekly Standard, etc.
            $table->string('slug')->unique();
            $table->enum('period', ['weekly', 'monthly']);
            $table->integer('baskets_per_week')->nullable();
            $table->integer('baskets_per_month')->nullable();
            $table->string('basket_size'); // Small, Medium, Large
            $table->enum('service_type', ['wash_fold', 'wash_iron', 'both'])->default('wash_fold');
            $table->decimal('price', 10, 2);
            $table->integer('free_iron_upgrades')->default(0);
            $table->boolean('includes_blanket_cleaning')->default(false);
            $table->boolean('priority_service')->default(false);
            $table->text('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};