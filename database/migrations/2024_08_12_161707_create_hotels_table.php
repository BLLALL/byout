<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->boolean('wifi')->default(true);
            $table->json('coordinates');
            $table->json('hotel_images');
            $table->integer('avg_rating')->nullable();
            $table->unsignedInteger('rating_count')->nullable();
            $table->decimal('popularity_score', 8, 4)->nullable()->index();
            $table->integer('hotel_rooms');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
