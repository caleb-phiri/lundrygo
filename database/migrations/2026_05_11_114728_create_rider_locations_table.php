<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rider_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id')->constrained('users')->onDelete('cascade');
            
            // Location coordinates with higher precision
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 11, 7);
            // Remove spatial columns for SQLite
            // $table->point('coordinates')->nullable();
            // $table->point('previous_coordinates')->nullable();
            $table->string('place_id')->nullable();
            $table->string('location_name')->nullable();
            
            // Movement tracking
            $table->decimal('speed', 8, 2)->nullable();
            $table->decimal('bearing', 6, 2)->nullable();
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->decimal('altitude', 10, 2)->nullable();
            $table->decimal('vertical_accuracy', 8, 2)->nullable();
            
            // Distance traveled
            $table->decimal('distance_traveled_today', 12, 2)->default(0);
            $table->decimal('distance_traveled_shift', 12, 2)->default(0);
            $table->decimal('total_distance_traveled', 12, 2)->default(0);
            
            // Status flags
            $table->boolean('is_online')->default(false);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_on_delivery')->default(false);
            $table->boolean('is_on_break')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_emergency')->default(false);
            
            // Ride status
            $table->enum('ride_status', [
                'idle', 
                'going_to_pickup', 
                'arrived_for_pickup', 
                'going_to_delivery', 
                'arrived_for_delivery', 
                'returning'
            ])->default('idle');
            
            // Current assignment
            $table->foreignId('current_order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->foreignId('current_laundry_id')->nullable()->constrained('laundries')->onDelete('set null');
            
            // Battery and device info
            $table->integer('battery_level')->nullable();
            $table->boolean('is_charging')->default(false);
            $table->string('device_id')->nullable();
            $table->string('device_model')->nullable();
            $table->string('os_version')->nullable();
            $table->string('app_version')->nullable();
            
            // Network info
            $table->string('network_type')->nullable();
            $table->string('ip_address')->nullable();
            
            // Geofencing and boundaries
            $table->boolean('is_in_service_area')->default(true);
            $table->boolean('is_at_laundry')->default(false);
            $table->boolean('is_at_customer')->default(false);
            $table->string('current_zone')->nullable();
            
            // Performance metrics
            $table->decimal('response_time_seconds', 6, 2)->nullable();
            $table->decimal('idle_duration_minutes', 8, 2)->default(0);
            $table->timestamp('last_assigned_at')->nullable();
            $table->timestamp('last_completed_at')->nullable();
            
            // Timing
            $table->timestamp('last_updated_at')->useCurrent();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('shift_started_at')->nullable();
            $table->timestamp('break_started_at')->nullable();
            
            // Tracking history
            $table->json('location_snapshot')->nullable();
            $table->json('metadata')->nullable();
            
            // Heartbeat and connection
            $table->timestamp('last_heartbeat_at')->useCurrent();
            $table->integer('missed_heartbeats')->default(0);
            $table->boolean('connection_quality')->default(true);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['rider_id', 'is_online', 'last_updated_at']);
            $table->index(['rider_id', 'is_available']);
            $table->index(['is_online', 'is_available', 'is_on_delivery']);
            $table->index(['latitude', 'longitude']);
            $table->index('ride_status');
            $table->index('current_order_id');
            $table->index(['is_online', 'last_heartbeat_at']);
            $table->index('last_updated_at');
            $table->index('shift_started_at');
            $table->index(['is_in_service_area', 'current_zone']);
        });
        
        // Create rider location history table (without spatial)
        Schema::create('rider_location_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id')->constrained('users')->onDelete('cascade');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 11, 7);
            $table->decimal('speed', 8, 2)->nullable();
            $table->decimal('bearing', 6, 2)->nullable();
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->string('ride_status')->nullable();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->integer('battery_level')->nullable();
            $table->timestamp('tracked_at')->useCurrent();
            
            $table->index(['rider_id', 'tracked_at']);
            $table->index(['rider_id', 'ride_status', 'tracked_at']);
            $table->index('tracked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_location_history');
        Schema::dropIfExists('rider_locations');
    }
};