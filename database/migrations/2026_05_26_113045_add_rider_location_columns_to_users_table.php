<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRiderLocationColumnsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'current_latitude')) {
                $table->decimal('current_latitude', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('users', 'current_longitude')) {
                $table->decimal('current_longitude', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('users', 'last_location_update')) {
                $table->timestamp('last_location_update')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['current_latitude', 'current_longitude', 'last_location_update']);
        });
    }
}