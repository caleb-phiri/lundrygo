<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // For SQLite, we need to recreate the table to change foreign keys
        // First, check if we're using SQLite
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        
        if ($driver === 'sqlite') {
            // SQLite doesn't support dropping foreign keys directly
            // So we'll disable foreign key checks and handle carefully
            DB::statement('PRAGMA foreign_keys = OFF');
        }
        
        // Drop existing foreign key if it exists
        try {
            Schema::table('laundry_services', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
            });
        } catch (\Exception $e) {
            // Foreign key might not exist
        }
        
        // Ensure category_id is the correct type
        Schema::table('laundry_services', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->change();
        });
        
        // Add foreign key to categories table (not laundry_categories)
        Schema::table('laundry_services', function (Blueprint $table) {
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('set null');
        });
        
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        }
    }

    public function down()
    {
        try {
            Schema::table('laundry_services', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
            });
        } catch (\Exception $e) {
            // Foreign key might not exist
        }
    }
};