<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('basket_sizes')) {
            Schema::create('basket_sizes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('icon')->nullable();
                $table->decimal('base_price', 10, 2);
                $table->decimal('price_multiplier', 10, 2)->default(1);
                $table->integer('max_weight_kg')->nullable();
                $table->integer('estimated_pieces')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('basket_sizes');
    }
};