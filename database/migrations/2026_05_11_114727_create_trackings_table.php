<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rider_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Status tracking
            $table->string('status');
            $table->string('previous_status')->nullable();
            $table->string('status_code')->nullable();
            $table->enum('status_type', [
                'order', 'payment', 'pickup', 'transport', 'processing', 
                'delivery', 'system', 'notification'
            ])->default('order');
            
            // Tracking details
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->json('details')->nullable();
            
            // Location tracking
            $table->string('location_name')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 11, 7)->nullable();
            // REMOVED: $table->point('coordinates')->nullable();
            $table->string('place_id')->nullable();
            
            // Time tracking
            $table->timestamp('tracked_at');
            $table->timestamp('expected_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('remaining_minutes')->nullable();
            
            // Polymorphic relationships
            $table->string('trackable_type')->nullable();
            $table->unsignedBigInteger('trackable_id')->nullable();
            
            // User engagement
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_notification_sent')->default(false);
            $table->timestamp('notification_sent_at')->nullable();
            $table->string('notification_channel')->nullable();
            
            // Media attachments
            $table->json('attachments')->nullable();
            $table->string('thumbnail')->nullable();
            
            // Metrics and analytics
            $table->json('metrics')->nullable();
            $table->decimal('accuracy', 8, 2)->nullable();
            
            // Flags
            $table->boolean('is_automatic')->default(false);
            $table->boolean('is_critical')->default(false);
            $table->boolean('requires_action')->default(false);
            $table->string('action_url')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes - REMOVED spatial index
            $table->index(['order_id', 'tracked_at']);
            $table->index(['order_id', 'status']);
            $table->index(['user_id', 'is_read']);
            $table->index(['rider_id', 'tracked_at']);
            $table->index(['status_type', 'tracked_at']);
            $table->index(['trackable_type', 'trackable_id']);
            $table->index(['latitude', 'longitude']);
            // REMOVED: $table->spatialIndex('coordinates');
            $table->index(['is_automatic', 'is_critical']);
            $table->index(['requires_action', 'is_read']);
            $table->index('tracked_at');
            $table->index('status_code');
            
            // Composite indexes
            $table->index(['order_id', 'status_type', 'tracked_at']);
            $table->index(['user_id', 'is_read', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trackings');
    }
};