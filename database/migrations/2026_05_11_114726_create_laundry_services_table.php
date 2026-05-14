<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable(); // For UI customization
            $table->boolean('is_active')->default(true);
            $table->boolean('is_available_24x7')->default(false); // 24/7 service availability
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('sort_order');
        });

        Schema::create('laundry_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('laundry_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable(); // For listings/cards
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->enum('price_type', ['per_piece', 'per_kg', 'per_load', 'fixed'])->default('per_piece');
            $table->decimal('min_quantity', 8, 2)->default(1); // Minimum pieces/kg for this service
            $table->decimal('max_quantity', 8, 2)->nullable(); // Maximum pieces/kg
            $table->string('unit')->default('piece'); // piece, kg, load
            $table->integer('estimated_hours')->default(24);
            $table->integer('min_estimated_hours')->nullable(); // For range
            $table->integer('max_estimated_hours')->nullable(); // For range
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('requires_pressing')->default(false);
            $table->boolean('requires_dry_cleaning')->default(false);
            $table->string('image')->nullable();
            $table->json('gallery')->nullable(); // Multiple images
            $table->json('tags')->nullable(); // e.g., ["eco-friendly", "express"]
            $table->json('metadata')->nullable(); // Additional flexible data
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('price');
            $table->index('price_type');
            $table->index('estimated_hours');
            $table->index(['category_id', 'is_active']);
            $table->index(['is_active', 'is_featured', 'sort_order']);
        });
        
        // Create pivot table for service add-ons/options
        Schema::create('laundry_service_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('laundry_services')->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // checkbox, radio, select
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        
        // Create service pricing tiers (volume discounts)
        Schema::create('laundry_service_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('laundry_services')->onDelete('cascade');
            $table->decimal('min_quantity', 8, 2);
            $table->decimal('max_quantity', 8, 2)->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_service_tiers');
        Schema::dropIfExists('laundry_service_options');
        Schema::dropIfExists('laundry_services');
        Schema::dropIfExists('laundry_categories');
    }
};