<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('rider_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('pickup_location_id')->constrained('locations')->onDelete('restrict');
            $table->foreignId('delivery_location_id')->constrained('locations')->onDelete('restrict');
            $table->foreignId('assigned_laundry_id')->nullable()->constrained('laundries')->onDelete('set null');
            
            // Enhanced status management
            $table->enum('status', [
                'pending', 'payment_pending', 'confirmed', 'processing',
                'rider_assigned', 'rider_en_route_pickup', 'at_pickup_location',
                'items_collected', 'in_transit_to_laundry', 'arrived_at_laundry',
                'at_laundry', 'washing', 'drying', 'ironing', 'folding',
                'quality_check', 'quality_check_failed', 'ready_for_delivery',
                'out_for_delivery', 'delivered', 'completed', 'cancelled',
                'refunded', 'disputed'
            ])->default('pending');
            
            // Order type and priority
            $table->enum('order_type', ['regular', 'express', 'scheduled'])->default('regular');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // Financial fields
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('coupon_discount', 12, 2)->default(0);
            $table->string('coupon_code')->nullable();
            $table->decimal('wallet_discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            
            // Weight tracking
            $table->decimal('estimated_weight', 10, 2)->nullable();
            $table->decimal('actual_weight', 10, 2)->nullable();
            $table->integer('total_items')->default(0);
            
            // Timeline tracking
            $table->timestamp('pickup_scheduled_at')->nullable();
            $table->timestamp('delivery_scheduled_at')->nullable();
            $table->timestamp('payment_confirmed_at')->nullable();
            $table->timestamp('order_confirmed_at')->nullable();
            $table->timestamp('rider_assigned_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('arrived_at_laundry_at')->nullable();
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('processing_completed_at')->nullable();
            $table->timestamp('ready_for_delivery_at')->nullable();
            $table->timestamp('out_for_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            // Estimated times
            $table->integer('estimated_pickup_minutes')->nullable();
            $table->integer('estimated_delivery_minutes')->nullable();
            $table->integer('estimated_processing_hours')->default(24);
            $table->timestamp('estimated_completion_at')->nullable();
            
            // Actual durations
            $table->integer('actual_pickup_minutes')->nullable();
            $table->integer('actual_processing_hours')->nullable();
            $table->integer('actual_delivery_minutes')->nullable();
            
            // Communication and instructions
            $table->text('special_instructions')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->text('dispute_reason')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Payment details
            $table->enum('payment_status', ['pending', 'processing', 'paid', 'partial', 'failed', 'refunded', 'chargeback'])->default('pending');
            $table->enum('payment_method', ['cash', 'card', 'wallet', 'bank_transfer', 'mobile_money'])->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->json('payment_metadata')->nullable();
            
            // Express delivery
            $table->boolean('is_express')->default(false);
            $table->decimal('express_fee', 12, 2)->default(0);
            
            // Rating and feedback
            $table->integer('rider_rating')->nullable();
            $table->integer('laundry_rating')->nullable();
            $table->integer('overall_rating')->nullable();
            $table->text('rider_review')->nullable();
            $table->text('laundry_review')->nullable();
            $table->text('overall_review')->nullable();
            
            // Tracking and notifications
            $table->json('tracking_updates')->nullable();
            $table->json('notification_log')->nullable();
            $table->boolean('is_notified_pickup')->default(false);
            $table->boolean('is_notified_delivery')->default(false);
            $table->boolean('is_sms_sent')->default(false);
            $table->boolean('is_email_sent')->default(false);
            
            // Flags
            $table->boolean('is_scheduled')->default(false);
            $table->boolean('requires_special_handling')->default(false);
            $table->boolean('is_insured')->default(false);
            $table->decimal('insurance_amount', 12, 2)->default(0);
            $table->boolean('is_priority')->default(false);
            $table->boolean('is_rush_order')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes - REMOVED FULLTEXT INDEX
            $table->index(['order_number', 'status', 'payment_status']);
            $table->index(['user_id', 'status', 'created_at']);
            $table->index(['rider_id', 'status']);
            $table->index(['pickup_location_id', 'delivery_location_id']);
            $table->index(['assigned_laundry_id', 'status']);
            $table->index(['order_type', 'priority']);
            $table->index(['payment_status', 'payment_method']);
            $table->index(['created_at', 'status']);
            $table->index(['pickup_scheduled_at', 'delivery_scheduled_at']);
            $table->index(['estimated_completion_at']);
            $table->index(['is_express', 'is_priority']);
            // Remove this line:
            // $table->fullText(['special_instructions', 'cancellation_reason']);
        });
        
        // Create order items table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('laundry_service_id')->constrained('laundry_services')->onDelete('restrict');
            $table->string('service_name');
            $table->decimal('unit_price', 12, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->json('selected_options')->nullable();
            $table->text('special_instructions')->nullable();
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('laundry_service_id');
        });
        
        // Create order status history table
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->string('previous_status')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('changed_at')->useCurrent();
            
            $table->index(['order_id', 'changed_at']);
            $table->index('status');
        });
        
        // Create order tracking table (without spatial)
        Schema::create('order_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 11, 7);
            $table->decimal('speed', 8, 2)->nullable();
            $table->decimal('bearing', 5, 2)->nullable();
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->string('location_label')->nullable();
            $table->timestamp('tracked_at')->useCurrent();
            
            $table->index(['order_id', 'tracked_at']);
            $table->index(['latitude', 'longitude']);
        });
        
        // Create order delivery proof table
        Schema::create('order_delivery_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('photo_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('recipient_name')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->timestamp('proof_taken_at')->nullable();
            $table->timestamps();
            
            $table->index('order_id');
        });
        
        // Create order disputes table
        Schema::create('order_disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('raised_by')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['damaged_items', 'missing_items', 'poor_quality', 'late_delivery', 'wrong_items', 'other']);
            $table->text('description');
            $table->json('evidence')->nullable();
            $table->enum('status', ['open', 'under_review', 'resolved', 'closed'])->default('open');
            $table->decimal('requested_refund_amount', 12, 2)->nullable();
            $table->decimal('approved_refund_amount', 12, 2)->nullable();
            $table->text('resolution_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['order_id', 'status']);
            $table->index('raised_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_disputes');
        Schema::dropIfExists('order_delivery_proofs');
        Schema::dropIfExists('order_tracking');
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};