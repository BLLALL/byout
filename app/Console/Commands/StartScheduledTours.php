<?php

namespace App\Console\Commands;

use App\Models\Tour;
use App\Services\TourService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StartScheduledTours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tour:start';

    protected $description = 'start scheduled tours';

    /**
     * The console command description.
     *
     * @var string
     */

    /**
     * Execute the console command.
     */
    public function handle(TourService $tourService)
    {

        $now = now();
        $toursToStart = Tour::where('status', 'scheduled')
            ->where('departure_time', "<=",  $now)
            ->get();

        foreach ($toursToStart as $tour) {
            $tourService->startTour($tour);
            $this->info("Started tour ID: {$tour->id}");
        }
    }
}
