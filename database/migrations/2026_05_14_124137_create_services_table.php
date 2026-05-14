<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->integer('estimated_time')->nullable()->comment('Estimated time in minutes');
            $table->string('image')->nullable();
            $table->timestamps();
            
            $table->index('category_id');
            $table->index('is_active');
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};