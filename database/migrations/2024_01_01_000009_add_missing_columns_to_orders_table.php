<?php
// database/migrations/2024_01_01_000009_add_missing_columns_to_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add missing discount columns if they don't exist
            if (!Schema::hasColumn('orders', 'coupon_discount')) {
                $table->decimal('coupon_discount', 12, 2)->default(0)->after('discount');
            }
            
            if (!Schema::hasColumn('orders', 'wallet_discount')) {
                $table->decimal('wallet_discount', 12, 2)->default(0)->after('coupon_discount');
            }
            
            if (!Schema::hasColumn('orders', 'coupon_code')) {
                $table->string('coupon_code')->nullable()->after('wallet_discount');
            }
            
            if (!Schema::hasColumn('orders', 'service_fee')) {
                $table->decimal('service_fee', 12, 2)->default(0)->after('delivery_fee');
            }
            
            if (!Schema::hasColumn('orders', 'tax_amount')) {
                $table->decimal('tax_amount', 12, 2)->default(0)->after('service_fee');
            }
            
            if (!Schema::hasColumn('orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('delivered_at');
            }
            
            if (!Schema::hasColumn('orders', 'is_express')) {
                $table->boolean('is_express')->default(false)->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'coupon_discount',
                'wallet_discount',
                'coupon_code',
                'service_fee',
                'tax_amount',
                'completed_at',
                'is_express'
            ]);
        });
    }
};