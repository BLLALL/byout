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
        Schema::create('accommodation_amenities', function (Blueprint $table) {
            $table->id();
            $table->boolean('air_conditioner')->default(false);
            $table->boolean('washing_machine')->default(false);
            $table->boolean('dryer')->default(false);
            $table->boolean('breakfast')->default(false);
            $table->boolean('free_wifi')->default(false);
            $table->boolean('sea_view')->default(false);
            $table->boolean('mountain_view')->default(false);
            $table->boolean('city_view')->default(false);
            $table->boolean('terrace')->default(false);
            $table->boolean('balcony')->default(false);
            $table->boolean('heating')->default(false);
            $table->boolean('coffee_machine')->default(false);
            $table->boolean('free_parking')->default(false);
            $table->boolean('swimming_pools')->default(false);
            $table->boolean('restaurant')->default(false);
            $table->boolean('tv')->default(false);
            $table->boolean('reception_service')->default(false);
            $table->boolean('cleaning_service')->default(false);
            $table->boolean('garden')->default(false);
            $table->morphs('servable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodation_amenities');
    }
};
