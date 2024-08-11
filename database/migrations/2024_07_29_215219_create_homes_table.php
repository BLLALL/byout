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
        Schema::create('homes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->integer('price');
            $table->integer('area');
            $table->integer('bathrooms_no');
            $table->integer('bedrooms_no');
            $table->json('home_images');
            $table->string('location');
            $table->integer('avg_rating')->nullable();
            $table->unsignedInteger('rating_count')->nullable();
            $table->boolean('wifi')->default(false);
            $table->json('coordinates');
            $table->string('rent_period');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homes');
    }
};
