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
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number');
            $table->string('model');
            $table->string('bus_image')->nullable();
            $table->enum('status', ['available', 'in use', 'unavailable'])->default('available');
            $table->integer('seats_number');
            $table->boolean('has_wifi')->default(false);
            $table->boolean('has_bathroom')->default(false);
            $table->boolean('has_movie_screens')->default(false);
            $table->boolean('has_entrance_camera')->default(false);
            $table->boolean('has_passenger_camera')->default(false);
            $table->enum('ownership', ['driver_owned', 'company_owned']);
            $table->string('transportation_company')->nullable(); // Nullable if driver-owned

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
