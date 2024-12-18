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
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->id();
            $table->integer('price');
            $table->integer('discount_price')->nullable();
            $table->string('currency')->default('SYP');
            $table->integer('area');
            $table->integer('bathrooms_no');
            $table->integer('bedrooms_no');
            $table->json('room_images')->nullable();
            $table->boolean('is_refundable')->default(false);
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->date('available_from');
            $table->date('available_until');
            $table->boolean('is_available')->default(true);
            $table->enum('room_type', [
                'room',
                'junior_suite',
                'suite',
                'executive_suite',
                'presidential_suite',
            ]);
            $table->string('capacity');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_rooms');
    }
};
