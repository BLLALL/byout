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
                $table->unsignedInteger('price');
                $table->string('source');
                $table->string('destination');
                $table->dateTime('departure_time');
                $table->dateTime('arrival_time');
                $table->string('time_difference');
                $table->foreignId('tour_company_id');
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
