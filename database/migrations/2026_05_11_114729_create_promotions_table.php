<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            
            // Basic information
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('short_description')->nullable();
            
            // Promotion type and value
            $table->enum('type', ['percentage', 'fixed_amount', 'free_delivery', 'buy_x_get_y', 'tiered_discount'])->default('percentage');
            $table->decimal('value', 12, 2)->nullable(); // Can be null for free_delivery or complex promotions
            $table->enum('discount_on', ['order_total', 'delivery_fee', 'specific_service', 'category'])->default('order_total');
            
            // Buy X Get Y specific fields
            $table->integer('buy_quantity')->nullable(); // Buy X items
            $table->integer('get_quantity')->nullable(); // Get Y items
            $table->decimal('get_discount_percentage', 5, 2)->nullable(); // Or get Z% off
            $table->foreignId('buy_service_id')->nullable()->constrained('laundry_services')->onDelete('set null');
            $table->foreignId('get_service_id')->nullable()->constrained('laundry_services')->onDelete('set null');
            
            // Minimum requirements
            $table->decimal('min_order_amount', 12, 2)->default(0);
            $table->integer('min_quantity')->default(0);
            $table->integer('min_items')->default(0);
            
            // Maximum limits
            $table->decimal('max_discount', 12, 2)->nullable();
            $table->decimal('max_discount_percent', 5, 2)->nullable(); // Maximum discount percentage
            $table->integer('max_uses_per_user')->nullable();
            $table->integer('max_uses_per_day')->nullable();
            
            // Usage tracking
            $table->integer('total_uses')->default(0);
            $table->integer('total_uses_today')->default(0);
            $table->date('last_used_date')->nullable();
            
            // Availability window
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->enum('time_restriction', ['anytime', 'weekdays_only', 'weekends_only', 'specific_hours'])->default('anytime');
            $table->time('available_from')->nullable(); // Specific start time
            $table->time('available_to')->nullable(); // Specific end time
            
            // Day restrictions
            $table->boolean('available_monday')->default(true);
            $table->boolean('available_tuesday')->default(true);
            $table->boolean('available_wednesday')->default(true);
            $table->boolean('available_thursday')->default(true);
            $table->boolean('available_friday')->default(true);
            $table->boolean('available_saturday')->default(true);
            $table->boolean('available_sunday')->default(true);
            
            // User targeting
            $table->enum('user_eligibility', ['all', 'new_users', 'returning_users', 'specific_users'])->default('all');
            $table->json('specific_user_ids')->nullable(); // Array of user IDs
            $table->json('excluded_user_ids')->nullable(); // Blacklisted users
            
            // First-time user specific
            $table->boolean('first_order_only')->default(false);
            $table->boolean('first_time_user')->default(false);
            
            // Service/category targeting
            $table->json('applicable_service_ids')->nullable(); // Array of service IDs
            $table->json('applicable_category_ids')->nullable(); // Array of category IDs
            $table->json('excluded_service_ids')->nullable();
            $table->json('excluded_category_ids')->nullable();
            
            // Location targeting
            $table->json('applicable_cities')->nullable();
            $table->json('applicable_zip_codes')->nullable();
            
            // Stacking rules
            $table->boolean('stackable')->default(false); // Can combine with other promotions
            $table->json('stackable_with')->nullable(); // Promotion IDs that can be stacked
            $table->boolean('apply_before_tax')->default(true);
            
            // Display and UI
            $table->string('banner_image')->nullable();
            $table->string('icon')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->string('color_theme')->nullable(); // For frontend highlighting
            $table->integer('sort_order')->default(0);
            
            // Auto-apply promotions
            $table->boolean('auto_apply')->default(false);
            $table->boolean('needs_coupon_code')->default(true);
            
            // Referral specific
            $table->boolean('is_referral')->default(false);
            $table->foreignId('referrer_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('referrer_reward', 12, 2)->nullable();
            $table->decimal('referee_reward', 12, 2)->nullable();
            
            // Status and visibility
            $table->enum('status', ['draft', 'active', 'paused', 'expired', 'cancelled'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_public')->default(true); // Show on public promotions page
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->string('verified_by')->nullable();
            
            // Admin notes
            $table->text('admin_notes')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->json('conditions')->nullable(); // Complex JSON conditions
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['code', 'status']);
            $table->index(['starts_at', 'expires_at']);
            $table->index(['status', 'starts_at', 'expires_at']);
            $table->index(['type', 'status']);
            $table->index(['is_featured', 'is_public']);
            $table->index(['min_order_amount', 'status']);
            $table->index(['user_eligibility', 'first_order_only']);
            $table->index('created_at');
            $table->index('total_uses');
            
            // Composite indexes
            $table->index(['status', 'starts_at', 'expires_at', 'is_active']);
        });
        
        Schema::create('promotion_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->decimal('original_amount', 12, 2);
            $table->decimal('discount_amount', 12, 2);
            $table->decimal('final_amount', 12, 2);
            $table->text('applied_rules')->nullable(); // Which rules were applied
            $table->json('applied_items')->nullable(); // Which items got discount
            $table->timestamp('used_at');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['promotion_id', 'user_id']);
            $table->index(['order_id', 'promotion_id']);
            $table->index(['promotion_id', 'used_at']);
            $table->index(['user_id', 'used_at']);
            $table->index('used_at');
            
            // Prevents duplicate usage on same order
            $table->unique(['promotion_id', 'order_id']);
        });
        
        // Create user promotion eligibility table
        Schema::create('user_promotion_eligibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->boolean('is_eligible')->default(true);
            $table->text('ineligibility_reason')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'promotion_id']);
            $table->index(['promotion_id', 'is_eligible']);
            $table->index(['user_id', 'usage_count']);
        });
        
        // Create promotion redemptions table for tracking code redemptions
        Schema::create('promotion_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('code');
            $table->enum('status', ['pending', 'success', 'failed', 'expired'])->default('pending');
            $table->text('error_message')->nullable();
            $table->string('ip_address')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('redeemed_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['promotion_id', 'status']);
            $table->index(['code', 'status']);
            $table->index('redeemed_at');
        });
        
        // Create promotion analytics table
        Schema::create('promotion_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('views')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('redemptions')->default(0);
            $table->decimal('total_discount', 12, 2)->default(0);
            $table->decimal('total_order_value', 12, 2)->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['promotion_id', 'date']);
            $table->index(['date', 'promotion_id']);
        });
        
        // Create promotion rules table for complex rule-based promotions
        Schema::create('promotion_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->string('rule_type'); // cart_condition, item_condition, user_condition, etc.
            $table->string('operator'); // equals, not_equals, greater_than, less_than, contains, etc.
            $table->string('field');
            $table->text('value');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['promotion_id', 'rule_type']);
            $table->index(['promotion_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_rules');
        Schema::dropIfExists('promotion_analytics');
        Schema::dropIfExists('promotion_redemptions');
        Schema::dropIfExists('user_promotion_eligibility');
        Schema::dropIfExists('promotion_usages');
        Schema::dropIfExists('promotions');
    }
};