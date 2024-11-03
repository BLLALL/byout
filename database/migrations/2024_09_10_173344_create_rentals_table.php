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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->morphs('rentable');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->datetime('check_in');
            $table->datetime('check_out');
            $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade');
            $table->string( 'status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
