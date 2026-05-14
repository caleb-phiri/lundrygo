<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('customer')->after('email');
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('phone')->nullable()->after('is_active');
            $table->text('address')->nullable()->after('phone');
            $table->string('profile_image')->nullable()->after('address');
            $table->timestamp('last_login_at')->nullable()->after('profile_image');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active', 'phone', 'address', 'profile_image', 'last_login_at', 'last_login_ip']);
        });
    }
};