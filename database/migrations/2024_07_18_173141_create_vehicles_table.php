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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['bus', 'van', 'car']);
            $table->string('model');
            $table->string('registration_number');
            $table->json('vehicle_images');
            $table->integer('seats_number');
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->boolean('has_wifi')->default(false);
            $table->boolean('has_air_conditioner')->default(false);
            $table->boolean('has_gps')->default(false);
            $table->boolean('has_movie_screens')->default(false);
            $table->boolean('has_entrance_camera')->default(false);
            $table->boolean('has_passenger_camera')->default(false);
            $table->boolean('has_bathroom')->default(false);
            $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
