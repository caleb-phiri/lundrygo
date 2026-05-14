<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->morphs('locationable');
            $table->enum('type', ['pickup', 'delivery', 'home', 'office', 'other'])->default('pickup');
            $table->text('full_address');
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 11, 7)->nullable();
            // Remove the point() column for SQLite
            // $table->point('coordinates')->nullable(); // NOT SUPPORTED IN SQLITE
            $table->string('landmark')->nullable();
            $table->text('delivery_instructions')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            // $table->spatialIndex('coordinates'); // Remove spatial index for SQLite
            $table->index(['latitude', 'longitude']);
            $table->index('type');
            $table->index('city');
            $table->index('postal_code');
            $table->index(['is_default', 'is_verified']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};