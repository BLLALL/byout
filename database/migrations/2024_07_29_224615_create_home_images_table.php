<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('home_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
//            $table->string('caption')->nullable();
            $table->enum('type', ['exterior', 'interior', 'floor_plan', 'other'])->default('other');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_images');
    }
};
