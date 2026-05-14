<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            // Recipient information
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('user_type')->default('customer'); // customer, rider, laundry, admin
            $table->string('notification_uuid')->unique(); // For idempotency
            
            // Notification content
            $table->string('type'); // order_update, payment, promotion, system, etc.
            $table->string('subtype')->nullable(); // order_confirmed, payment_success, etc.
            $table->string('title');
            $table->text('message');
            $table->text('short_message')->nullable(); // For push notifications
            $table->json('data')->nullable(); // Additional data payload
            
            // Action buttons
            $table->string('action_text')->nullable();
            $table->string('action_url')->nullable();
            $table->string('action_type')->nullable(); // webview, deeplink, external
            
            // Channels
            $table->boolean('send_email')->default(false);
            $table->boolean('send_sms')->default(false);
            $table->boolean('send_push')->default(false);
            $table->boolean('send_in_app')->default(true);
            $table->boolean('send_webhook')->default(false);
            
            // Delivery status tracking
            $table->enum('email_status', ['pending', 'sent', 'delivered', 'failed', 'bounced'])->default('pending');
            $table->enum('sms_status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');
            $table->enum('push_status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');
            $table->enum('in_app_status', ['pending', 'sent', 'read'])->default('pending');
            
            // Delivery timestamps
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamp('sms_sent_at')->nullable();
            $table->timestamp('push_sent_at')->nullable();
            $table->timestamp('in_app_sent_at')->nullable();
            
            // External IDs for tracking
            $table->string('email_message_id')->nullable();
            $table->string('sms_message_id')->nullable();
            $table->string('push_message_id')->nullable();
            
            // Read status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('read_device')->nullable(); // web, ios, android
            
            // Priority and importance
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->boolean('is_critical')->default(false);
            
            // Scheduling
            $table->boolean('is_scheduled')->default(false);
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('sent_at')->nullable();
            
            // Expiry
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_expired')->default(false);
            
            // Grouping
            $table->string('group_id')->nullable(); // Group related notifications
            $table->integer('group_count')->default(1);
            
            // User interaction
            $table->boolean('is_clicked')->default(false);
            $table->timestamp('clicked_at')->nullable();
            $table->string('clicked_action')->nullable();
            
            // Dismissal
            $table->boolean('is_dismissed')->default(false);
            $table->timestamp('dismissed_at')->nullable();
            
            // Templates
            $table->string('template_id')->nullable();
            $table->json('template_variables')->nullable();
            
            // Source
            $table->string('source')->nullable(); // system, admin, auto, api
            $table->foreignId('triggered_by')->nullable()->constrained('users')->onDelete('set null');
            
            // A/B Testing
            $table->string('variant')->nullable(); // A, B, C for A/B testing
            $table->json('ab_test_data')->nullable();
            
            // Analytics
            $table->json('analytics')->nullable(); // Open rates, click rates, etc.
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->json('delivery_logs')->nullable(); // Track delivery attempts
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'is_read', 'created_at']);
            $table->index(['user_id', 'user_type']);
            $table->index(['type', 'created_at']);
            $table->index(['priority', 'is_read']);
            $table->index(['is_scheduled', 'scheduled_for']);
            $table->index('notification_uuid');
            $table->index('group_id');
            $table->index(['status', 'created_at']);
            $table->index('expires_at');
            $table->index('template_id');
            
            // Composite indexes
            $table->index(['user_id', 'type', 'is_read']);
            $table->index(['user_id', 'priority', 'created_at']);
            $table->index(['is_scheduled', 'scheduled_for', 'status']);
        });
        
        // Create notification preferences table
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('user_type')->default('customer');
            
            // Channel preferences
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(true);
            $table->boolean('push_enabled')->default(true);
            $table->boolean('in_app_enabled')->default(true);
            
            // Category-specific preferences
            $table->json('order_notifications')->nullable(); // {email: true, sms: true, push: true}
            $table->json('payment_notifications')->nullable();
            $table->json('promotion_notifications')->nullable();
            $table->json('system_notifications')->nullable();
            $table->json('rider_notifications')->nullable();
            
            // Quiet hours
            $table->boolean('quiet_hours_enabled')->default(false);
            $table->time('quiet_hours_start')->nullable();
            $table->time('quiet_hours_end')->nullable();
            $table->json('quiet_hours_days')->nullable(); // ['monday', 'tuesday', ...]
            
            // Digest settings
            $table->boolean('digest_enabled')->default(false);
            $table->enum('digest_frequency', ['daily', 'weekly', 'never'])->default('never');
            $table->time('digest_time')->nullable();
            
            // Device tokens
            $table->json('device_tokens')->nullable(); // Push notification tokens
            $table->json('email_addresses')->nullable(); // Multiple emails
            $table->json('phone_numbers')->nullable(); // Multiple phone numbers
            
            // Unsubscribe
            $table->boolean('is_unsubscribed')->default(false);
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('unsubscribe_reason')->nullable();
            
            $table->timestamps();
            
            $table->unique(['user_id', 'user_type']);
            $table->index(['user_id', 'is_unsubscribed']);
        });
        
        // Create notification templates table
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_id')->unique();
            $table->string('name');
            $table->string('type');
            $table->string('subtype')->nullable();
            $table->string('channel'); // email, sms, push, in_app
            $table->string('subject')->nullable(); // For emails
            $table->text('title_template'); // With placeholders like {{user_name}}
            $table->text('message_template');
            $table->text('short_message_template')->nullable();
            $table->string('action_text_template')->nullable();
            $table->string('action_url_template')->nullable();
            $table->integer('priority')->default(1);
            $table->boolean('is_active')->default(true);
            $table->json('default_data')->nullable();
            $table->json('conditions')->nullable(); // When to use this template
            $table->timestamps();
            
            $table->index(['type', 'channel', 'is_active']);
            $table->index('template_id');
        });
        
        // Create notification logs table for audit trail
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->string('channel'); // email, sms, push, in_app
            $table->string('status');
            $table->text('response')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('attempt_count')->default(1);
            $table->timestamp('logged_at')->useCurrent();
            
            $table->index(['notification_id', 'channel']);
            $table->index(['status', 'logged_at']);
        });
        
        // Create notification queue table for scheduled notifications
        Schema::create('notification_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->timestamp('scheduled_at');
            $table->enum('status', ['pending', 'processing', 'sent', 'failed'])->default('pending');
            $table->integer('retry_count')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'scheduled_at']);
            $table->index(['scheduled_at', 'status']);
        });
        
        // Create notification statistics table
        Schema::create('notification_statistics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('type');
            $table->string('channel');
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('read_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->decimal('delivery_rate', 5, 2)->default(0);
            $table->decimal('read_rate', 5, 2)->default(0);
            $table->decimal('click_rate', 5, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['date', 'type', 'channel']);
            $table->index(['date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_statistics');
        Schema::dropIfExists('notification_queue');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('notifications');
    }
};