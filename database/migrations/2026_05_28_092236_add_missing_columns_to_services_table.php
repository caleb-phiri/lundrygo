<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToServicesTable extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            // Add unit column if it doesn't exist
            if (!Schema::hasColumn('services', 'unit')) {
                $table->string('unit')->default('item')->after('price');
            }
            
            // Add service_type column if it doesn't exist
            if (!Schema::hasColumn('services', 'service_type')) {
                $table->enum('service_type', ['standard', 'weekly', 'monthly', 'express'])->default('standard')->after('description');
            }
            
            // Add billing_period column if it doesn't exist
            if (!Schema::hasColumn('services', 'billing_period')) {
                $table->enum('billing_period', ['one-time', 'weekly', 'monthly'])->default('one-time')->after('service_type');
            }
            
            // Add basket_size column if it doesn't exist
            if (!Schema::hasColumn('services', 'basket_size')) {
                $table->integer('basket_size')->nullable()->after('billing_period');
            }
            
            // Add baskets_per_week column if it doesn't exist
            if (!Schema::hasColumn('services', 'baskets_per_week')) {
                $table->integer('baskets_per_week')->nullable()->after('basket_size');
            }
            
            // Add baskets_per_month column if it doesn't exist
            if (!Schema::hasColumn('services', 'baskets_per_month')) {
                $table->integer('baskets_per_month')->nullable()->after('baskets_per_week');
            }
            
            // Add ironing_included column if it doesn't exist
            if (!Schema::hasColumn('services', 'ironing_included')) {
                $table->boolean('ironing_included')->default(false)->after('baskets_per_month');
            }
            
            // Add priority_service column if it doesn't exist
            if (!Schema::hasColumn('services', 'priority_service')) {
                $table->boolean('priority_service')->default(false)->after('ironing_included');
            }
            
            // Add bedding_cleaning column if it doesn't exist
            if (!Schema::hasColumn('services', 'bedding_cleaning')) {
                $table->boolean('bedding_cleaning')->default(false)->after('priority_service');
            }
            
            // Add turnaround_hours column if it doesn't exist
            if (!Schema::hasColumn('services', 'turnaround_hours')) {
                $table->integer('turnaround_hours')->default(24)->after('bedding_cleaning');
            }
            
            // Add is_popular column if it doesn't exist
            if (!Schema::hasColumn('services', 'is_popular')) {
                $table->boolean('is_popular')->default(false)->after('turnaround_hours');
            }
            
            // Add is_best_value column if it doesn't exist
            if (!Schema::hasColumn('services', 'is_best_value')) {
                $table->boolean('is_best_value')->default(false)->after('is_popular');
            }
            
            // Add display_order column if it doesn't exist
            if (!Schema::hasColumn('services', 'display_order')) {
                $table->integer('display_order')->default(0)->after('is_best_value');
            }
            
            // Add is_active column if it doesn't exist
            if (!Schema::hasColumn('services', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('display_order');
            }
        });
    }

    public function down()
    {
        // We won't implement down() to avoid SQLite issues
        // Just comment or leave empty
    }
}