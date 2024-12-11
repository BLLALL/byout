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
        Schema::create('room_beds', function (Blueprint $table) {
            $table->id();
            $table->enum('bed_type', [
                'single bed',
                'double bed',
                'large double bed',
                'extra large double bed',
                'sofa bed',
                'bunk bed',
            ]);
            $table->morphs('bedable');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_beds');
    }

};
