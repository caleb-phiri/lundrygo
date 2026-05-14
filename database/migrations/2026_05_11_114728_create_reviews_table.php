<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('rider_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('laundry_id')->nullable()->constrained('laundries')->onDelete('set null');
            
            // Review type and target
            $table->enum('review_type', ['order', 'rider', 'laundry', 'service', 'app'])->default('order');
            $table->string('review_target_type')->nullable(); // Polymorphic for future expansion
            $table->unsignedBigInteger('review_target_id')->nullable();
            
            // Rating scores (1-5)
            $table->integer('rating')->comment('1-5 stars overall rating');
            $table->integer('communication_rating')->nullable()->comment('1-5 stars');
            $table->integer('timeliness_rating')->nullable()->comment('1-5 stars');
            $table->integer('quality_rating')->nullable()->comment('1-5 stars');
            $table->integer('professionalism_rating')->nullable()->comment('1-5 stars');
            $table->integer('value_rating')->nullable()->comment('1-5 stars');
            $table->integer('packaging_rating')->nullable()->comment('1-5 stars');
            
            // Review content
            $table->text('review_text')->nullable();
            $table->string('title')->nullable(); // Short title for the review
            $table->json('pros')->nullable(); // Array of positive points
            $table->json('cons')->nullable(); // Array of negative points
            
            // Aspect ratings (flexible JSON)
            $table->json('aspects')->nullable()->comment('Ratings for specific aspects like cleanliness, punctuality, etc.');
            
            // Media attachments
            $table->json('photos')->nullable(); // Array of photo URLs
            $table->json('videos')->nullable(); // Array of video URLs
            $table->string('thumbnail')->nullable(); // Thumbnail image
            
            // Response from business/rider
            $table->text('response_text')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Verification and status
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->string('verified_by')->nullable(); // Admin, System, etc.
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_flagged')->default(false);
            $table->text('flag_reason')->nullable();
            
            // Helpfulness metrics
            $table->integer('helpful_count')->default(0);
            $table->integer('unhelpful_count')->default(0);
            $table->json('helpful_votes')->nullable(); // Track who voted
            
            // Purchase verification
            $table->boolean('is_verified_purchase')->default(false);
            $table->timestamp('purchased_at')->nullable(); // When the purchase occurred
            
            // Incentives
            $table->boolean('is_incentivized')->default(false); // Received incentive for review
            $table->string('incentive_type')->nullable(); // discount, points, free service
            $table->text('incentive_details')->nullable();
            
            // Anonymous reviews
            $table->boolean('is_anonymous')->default(false);
            $table->string('anonymous_name')->nullable(); // For public display
            
            // Location context
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('review_location')->nullable(); // City, state where review was made
            
            // Metadata
            $table->json('metadata')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['order_id', 'review_type']);
            $table->index(['user_id', 'created_at']);
            $table->index(['rider_id', 'rating']);
            $table->index(['laundry_id', 'rating']);
            $table->index(['review_type', 'rating', 'review_target_id']);
            $table->index(['is_published', 'is_verified']);
            $table->index(['is_verified_purchase', 'rating']);
            $table->index(['rating', 'created_at']);
            $table->index('is_featured');
            $table->index('helpful_count');
            $table->index('created_at');
            
            // Composite indexes
            $table->index(['review_type', 'review_target_id', 'is_published']);
            $table->index(['user_id', 'review_type', 'review_target_id']);
            $table->index(['laundry_id', 'is_published', 'rating']);
            $table->index(['rider_id', 'is_published', 'rating']);
            
            // Unique constraint to prevent multiple reviews per order/target
            $table->unique(['order_id', 'review_type', 'review_target_id'], 'unique_review_per_target');
        });
        
        // Create review helpful votes table
        Schema::create('review_helpful_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_helpful'); // true = helpful, false = not helpful
            $table->timestamps();
            
            $table->unique(['review_id', 'user_id']);
            $table->index(['review_id', 'is_helpful']);
        });
        
        // Create review report/flag table
        Schema::create('review_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->string('reason');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['review_id', 'status']);
            $table->index(['reported_by', 'created_at']);
            $table->index('status');
        });
        
        // Create review templates for quick responses
        Schema::create('review_response_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['rider', 'laundry', 'general'])->default('general');
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
        });
        
        // Create review analytics summary table
        Schema::create('review_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('target_type'); // rider, laundry, service
            $table->unsignedBigInteger('target_id');
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('verified_reviews')->default(0);
            $table->integer('five_star_count')->default(0);
            $table->integer('four_star_count')->default(0);
            $table->integer('three_star_count')->default(0);
            $table->integer('two_star_count')->default(0);
            $table->integer('one_star_count')->default(0);
            $table->json('aspect_averages')->nullable(); // Average ratings for different aspects
            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['target_type', 'target_id']);
            $table->index(['target_type', 'average_rating']);
            $table->index(['target_type', 'total_reviews']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_summaries');
        Schema::dropIfExists('review_response_templates');
        Schema::dropIfExists('review_reports');
        Schema::dropIfExists('review_helpful_votes');
        Schema::dropIfExists('reviews');
    }
};