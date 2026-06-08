<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscriptionFieldsToServicesTable extends Migration
{
    public function up()
    {
        // Check if the services table exists
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2);
                $table->string('unit')->default('item'); // kg, basket, item, week, month
                $table->enum('service_type', ['standard', 'weekly', 'monthly', 'express'])->default('standard');
                $table->enum('billing_period', ['one-time', 'weekly', 'monthly'])->default('one-time');
                $table->integer('basket_size')->nullable(); // in kg
                $table->integer('baskets_per_week')->nullable();
                $table->integer('baskets_per_month')->nullable();
                $table->boolean('ironing_included')->default(false);
                $table->boolean('priority_service')->default(false);
                $table->boolean('bedding_cleaning')->default(false);
                $table->integer('turnaround_hours')->default(24);
                $table->boolean('is_popular')->default(false);
                $table->boolean('is_best_value')->default(false);
                $table->integer('display_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            // Add columns if they don't exist
            if (!Schema::hasColumn('services', 'unit')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->string('unit')->default('item')->after('price');
                });
            }
            
            if (!Schema::hasColumn('services', 'service_type')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->enum('service_type', ['standard', 'weekly', 'monthly', 'express'])->default('standard')->after('description');
                });
            }
            
            if (!Schema::hasColumn('services', 'billing_period')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->enum('billing_period', ['one-time', 'weekly', 'monthly'])->default('one-time')->after('service_type');
                });
            }
            
            if (!Schema::hasColumn('services', 'basket_size')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->integer('basket_size')->nullable()->after('billing_period');
                });
            }
            
            if (!Schema::hasColumn('services', 'baskets_per_week')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->integer('baskets_per_week')->nullable()->after('basket_size');
                });
            }
            
            if (!Schema::hasColumn('services', 'baskets_per_month')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->integer('baskets_per_month')->nullable()->after('baskets_per_week');
                });
            }
            
            if (!Schema::hasColumn('services', 'ironing_included')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->boolean('ironing_included')->default(false)->after('baskets_per_month');
                });
            }
            
            if (!Schema::hasColumn('services', 'priority_service')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->boolean('priority_service')->default(false)->after('ironing_included');
                });
            }
            
            if (!Schema::hasColumn('services', 'bedding_cleaning')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->boolean('bedding_cleaning')->default(false)->after('priority_service');
                });
            }
            
            if (!Schema::hasColumn('services', 'turnaround_hours')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->integer('turnaround_hours')->default(24)->after('bedding_cleaning');
                });
            }
            
            if (!Schema::hasColumn('services', 'is_popular')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->boolean('is_popular')->default(false)->after('turnaround_hours');
                });
            }
            
            if (!Schema::hasColumn('services', 'is_best_value')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->boolean('is_best_value')->default(false)->after('is_popular');
                });
            }
            
            if (!Schema::hasColumn('services', 'display_order')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->integer('display_order')->default(0)->after('is_best_value');
                });
            }
            
            if (!Schema::hasColumn('services', 'is_active')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->boolean('is_active')->default(true)->after('display_order');
                });
            }
        }
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $columns = [
                'unit', 'service_type', 'billing_period', 'basket_size', 'baskets_per_week',
                'baskets_per_month', 'ironing_included', 'priority_service', 'bedding_cleaning',
                'turnaround_hours', 'is_popular', 'is_best_value', 'display_order', 'is_active'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('services', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}