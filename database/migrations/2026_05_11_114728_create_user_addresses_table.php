<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Address identification
            $table->string('label')->default('Home');
            $table->string('address_type')->default('other');
            $table->string('place_id')->nullable();
            
            // Address components
            $table->text('full_address');
            $table->string('street_address')->nullable();
            $table->string('apartment_suite')->nullable();
            $table->string('floor')->nullable();
            $table->string('intercom')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('US');
            $table->string('county')->nullable();
            $table->string('neighborhood')->nullable();
            
            // Location coordinates
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 11, 7)->nullable();
            // Remove spatial column for SQLite
            
            // Delivery instructions
            $table->text('delivery_instructions')->nullable();
            $table->text('access_instructions')->nullable();
            $table->string('preferred_time_start')->nullable();
            $table->string('preferred_time_end')->nullable();
            $table->enum('preferred_day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable();
            
            // Contact information
            $table->string('contact_person_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->string('email')->nullable();
            
            // Status flags
            $table->boolean('is_default')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_residential')->default(true);
            $table->boolean('is_commercial')->default(false);
            $table->boolean('has_elevator')->default(false);
            $table->boolean('requires_approval')->default(false);
            
            // Security and access
            $table->string('security_code')->nullable();
            $table->text('landmarks')->nullable();
            
            // Usage statistics
            $table->integer('delivery_count')->default(0);
            $table->integer('pickup_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            
            // Rating and feedback
            $table->decimal('rider_rating', 2, 1)->nullable();
            $table->text('rider_notes')->nullable();
            
            // Geofencing and boundaries
            $table->boolean('is_in_service_area')->default(true);
            $table->string('service_zone')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->json('custom_fields')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'is_default']);
            $table->index(['user_id', 'is_active']);
            $table->index(['latitude', 'longitude']);
            $table->index('place_id');
            $table->index('postal_code');
            $table->index('city');
            $table->index('is_verified');
            $table->index(['address_type', 'is_active']);
            $table->index('last_used_at');
            $table->index('delivery_count');
            
            // Unique constraint for default address
            $table->unique(['user_id', 'is_default'], 'unique_default_address')
                ->where('is_default', 1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};