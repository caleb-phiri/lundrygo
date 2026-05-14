<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add basket_size_id to laundry_services if not exists
        if (!Schema::hasColumn('laundry_services', 'basket_size_id')) {
            Schema::table('laundry_services', function (Blueprint $table) {
                $table->foreignId('basket_size_id')
                    ->nullable()
                    ->after('category_id')
                    ->constrained('basket_sizes')
                    ->onDelete('set null');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('laundry_services', 'basket_size_id')) {
            Schema::table('laundry_services', function (Blueprint $table) {
                $table->dropForeign(['basket_size_id']);
                $table->dropColumn('basket_size_id');
            });
        }
    }
};