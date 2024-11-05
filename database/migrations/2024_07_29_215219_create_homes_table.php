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
            $table->softDeletes();  
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('price');
            $table->integer('discount_price')->nullable();
            $table->string('currency')->default('SYP');
            $table->integer('area');
            $table->integer('bathrooms_no');
            $table->integer('bedrooms_no');
            $table->integer('living_room_no')->default(1);
            $table->integer('kitchen_no')->default(1);
            $table->json('home_images');
            $table->string('license')->nullable();
            $table->string('location');
            $table->integer('avg_rating')->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->decimal('popularity_score', 8, 4)->default(0)->index();
            $table->boolean('wifi')->default(false);
            $table->json('coordinates');
            $table->string('rent_period');
            $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade');
            $table->boolean('pending')->default(true);
            $table->dateTime('available_from');
            $table->dateTime('available_until');
            $table->boolean('is_available')->default(true);
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
