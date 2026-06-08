<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRiderAssignedAtToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('orders', 'rider_assigned_at')) {
                $table->timestamp('rider_assigned_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('rider_assigned_at');
        });
    }
}