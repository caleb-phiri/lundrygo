<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrackingColumnsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add missing tracking columns if they don't exist
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable();
            }
            if (!Schema::hasColumn('orders', 'order_confirmed_at')) {
                $table->timestamp('order_confirmed_at')->nullable();
            }
            if (!Schema::hasColumn('orders', 'picked_up_at')) {
                $table->timestamp('picked_up_at')->nullable();
            }
            if (!Schema::hasColumn('orders', 'processing_started_at')) {
                $table->timestamp('processing_started_at')->nullable();
            }
            if (!Schema::hasColumn('orders', 'processing_completed_at')) {
                $table->timestamp('processing_completed_at')->nullable();
            }
            if (!Schema::hasColumn('orders', 'out_for_delivery_at')) {
                $table->timestamp('out_for_delivery_at')->nullable();
            }
            if (!Schema::hasColumn('orders', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
        });
    }
    
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivered_at',
                'order_confirmed_at',
                'picked_up_at',
                'processing_started_at',
                'processing_completed_at',
                'out_for_delivery_at',
                'completed_at'
            ]);
        });
    }
}