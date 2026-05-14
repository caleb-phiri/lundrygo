<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Check and add basket_size_id if not exists
            if (!Schema::hasColumn('orders', 'basket_size_id')) {
                $table->foreignId('basket_size_id')
                    ->nullable()
                    ->after('delivery_location_id')
                    ->constrained('basket_sizes')
                    ->onDelete('set null');
            }
            
            // Check and add total_items if not exists
            if (!Schema::hasColumn('orders', 'total_items')) {
                $table->integer('total_items')->nullable()->after('basket_size_id');
            }
            
            // Check and add total_weight_kg if not exists
            if (!Schema::hasColumn('orders', 'total_weight_kg')) {
                $table->decimal('total_weight_kg', 10, 2)->nullable()->after('total_items');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'basket_size_id')) {
                $table->dropForeign(['basket_size_id']);
                $table->dropColumn('basket_size_id');
            }
            
            if (Schema::hasColumn('orders', 'total_items')) {
                $table->dropColumn('total_items');
            }
            
            if (Schema::hasColumn('orders', 'total_weight_kg')) {
                $table->dropColumn('total_weight_kg');
            }
        });
    }
};