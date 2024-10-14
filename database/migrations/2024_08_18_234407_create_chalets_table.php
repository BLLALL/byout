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
        Schema::create('chalets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->unsignedBigInteger('price');
            $table->string('currency')->default('SYP');
            $table->integer('area');
            $table->integer('bathrooms_no');
            $table->integer('bedrooms_no');
            $table->json('chalet_images');
            $table->string('location');
            $table->integer('avg_rating')->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->decimal('popularity_score', 8, 4)->default(0)->index();
            $table->boolean('wifi')->default(false);
            $table->json('coordinates');
            $table->boolean('air_conditioning')->default(false);
            $table->boolean('sea_view')->default(false);
            $table->integer('distance_to_beach')->nullable();
            $table->string('max_occupancy');
            $table->string('rent_period');
            $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('pending')->default(true);
            $table->boolean('is_available')->default(true);
            $table->date('available_from');
            $table->date('available_until');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chalets');
    }
};
