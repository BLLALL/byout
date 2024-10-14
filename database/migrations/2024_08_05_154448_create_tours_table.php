    <?php

    use Carbon\Carbon;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration {

        /**
        * Run the migrations.
        */
        public function up(): void
        {
            Schema::create('tours', function (Blueprint $table) {
                $table->id();
                $table->softDeletes();
                $table->unsignedInteger('price');
                $table->string('currency')->default('SYP');
                $table->enum('tour_type', ['fixed', 'individual']);
                $table->string('source');
                $table->string('destination');
                $table->dateTime('departure_time')->index();
                $table->dateTime('arrival_time')->index();
                $table->string('time_difference');
                $table->integer('recurrence')->nullable();
                $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled']);
                $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
                $table->foreignId('owner_id')->constrained('owners')->onDelete('cascade');
                $table->foreignId('driver_id')->constrained();
                $table->string('transportation_company')->nullable();
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
        */

        public function down(): void
        {
            Schema::dropIfExists('tours');
        }
    };
