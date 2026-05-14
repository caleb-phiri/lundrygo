<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null');
            
            // Payment identification
            $table->string('payment_method');
            $table->string('payment_method_type')->nullable(); // card, bank, wallet, cash, mobile_money
            $table->string('transaction_id')->unique();
            $table->string('invoice_id')->unique()->nullable();
            $table->string('receipt_number')->unique()->nullable();
            
            // Amount details with better precision
            $table->decimal('amount', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('fee_amount', 12, 2)->default(0); // Payment gateway fee
            $table->decimal('net_amount', 12, 2)->storedAs('amount - fee_amount'); // Net after fees
            $table->string('currency')->default('USD');
            
            // Status tracking
            $table->enum('status', [
                'pending', 
                'processing', 
                'authorized', 
                'captured', 
                'completed', 
                'failed', 
                'refunded', 
                'partially_refunded', 
                'disputed', 
                'chargeback',
                'voided'
            ])->default('pending');
            
            $table->string('status_reason')->nullable();
            $table->text('failure_reason')->nullable();
            
            // Payment gateway details
            $table->string('gateway')->nullable(); // stripe, paypal, razorpay, etc.
            $table->string('gateway_payment_id')->nullable();
            $table->string('gateway_customer_id')->nullable();
            $table->string('gateway_charge_id')->nullable();
            $table->json('gateway_response')->nullable();
            $table->json('gateway_webhook_data')->nullable();
            
            // Card details (encrypted or tokenized)
            $table->string('card_last_four')->nullable();
            $table->string('card_brand')->nullable(); // visa, mastercard, amex
            $table->string('card_expiry_month')->nullable();
            $table->string('card_expiry_year')->nullable();
            $table->string('card_holder_name')->nullable();
            $table->string('payment_token')->nullable(); // Tokenized payment method
            
            // Banking details
            $table->string('bank_name')->nullable();
            $table->string('bank_account_last_four')->nullable();
            $table->string('reference_number')->nullable();
            
            // Refund tracking
            $table->decimal('refunded_amount', 12, 2)->default(0);
            $table->string('refund_reason')->nullable();
            $table->text('refund_notes')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('refund_transaction_id')->nullable();
            $table->json('refund_details')->nullable();
            
            // Partial payment tracking
            $table->boolean('is_partial')->default(false);
            $table->integer('installment_number')->nullable();
            $table->integer('total_installments')->nullable();
            
            // Split payments (for multi-vendor)
            $table->boolean('is_split_payment')->default(false);
            $table->json('split_details')->nullable(); // How amount is split between parties
            
            // Timestamps
            $table->timestamp('authorized_at')->nullable();
            $table->timestamp('captured_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->timestamp('disputed_at')->nullable();
            
            // Additional tracking
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            
            // Receipt and document tracking
            $table->string('receipt_url')->nullable();
            $table->string('invoice_url')->nullable();
            $table->json('attachments')->nullable();
            
            // Webhook tracking
            $table->boolean('webhook_processed')->default(false);
            $table->timestamp('webhook_received_at')->nullable();
            $table->string('webhook_signature')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['order_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['transaction_id', 'gateway_payment_id']);
            $table->index(['payment_method', 'status']);
            $table->index(['gateway', 'gateway_payment_id']);
            $table->index(['status', 'paid_at']);
            $table->index(['is_partial', 'installment_number']);
            $table->index(['refunded_at', 'refunded_amount']);
            $table->index('invoice_id');
            $table->index('receipt_number');
            $table->index(['created_at', 'status']);
            
            // Composite indexes
            $table->index(['order_id', 'status', 'paid_at']);
            $table->index(['user_id', 'payment_method', 'status']);
            $table->index(['gateway', 'status', 'created_at']);
        });
        
        // Create payment methods table for stored payment methods
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // card, bank_account, mobile_money, wallet
            $table->string('provider'); // stripe, paypal, etc.
            $table->string('token')->unique(); // Payment method token
            $table->string('last_four')->nullable();
            $table->string('brand')->nullable();
            $table->string('expiry_month')->nullable();
            $table->string('expiry_year')->nullable();
            $table->string('holder_name')->nullable();
            $table->string('billing_address')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'is_default']);
            $table->index(['user_id', 'is_active']);
            $table->index('token');
        });
        
        // Create payment logs table for audit trail
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->string('action'); // created, authorized, captured, refunded, failed, webhook_received
            $table->string('status');
            $table->text('message')->nullable();
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['payment_id', 'action']);
            $table->index('created_at');
        });
        
        // Create refunds table for detailed refund tracking
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('refund_transaction_id')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('gateway_response')->nullable();
            $table->string('failure_reason')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            
            $table->index(['payment_id', 'status']);
            $table->index(['order_id', 'status']);
            $table->index('refund_transaction_id');
            $table->index('created_at');
        });
        
        // Create payment disputes table
        Schema::create('payment_disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('dispute_id')->unique(); // Gateway dispute ID
            $table->string('reason');
            $table->text('description');
            $table->decimal('dispute_amount', 12, 2);
            $table->enum('status', ['under_review', 'accepted', 'rejected', 'won', 'lost'])->default('under_review');
            $table->json('evidence')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['payment_id', 'status']);
            $table->index(['order_id', 'status']);
            $table->index('dispute_id');
        });
        
        // Create payment settlements table for batch settlements
        Schema::create('payment_settlements', function (Blueprint $table) {
            $table->id();
            $table->string('settlement_id')->unique();
            $table->date('settlement_date');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('total_fees', 12, 2);
            $table->decimal('net_amount', 12, 2);
            $table->json('payment_ids')->nullable(); // Array of payment IDs in this settlement
            $table->json('gateway_response')->nullable();
            $table->timestamps();
            
            $table->index('settlement_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settlements');
        Schema::dropIfExists('payment_disputes');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('payment_logs');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('payments');
    }
};