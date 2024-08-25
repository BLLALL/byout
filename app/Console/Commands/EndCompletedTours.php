<?php

namespace App\Console\Commands;

use App\Models\Tour;
use App\Services\TourService;
use Illuminate\Console\Command;

class EndCompletedTours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tour:end';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'End scheduled tours';

    /**
     * Execute the console command.
     */
    public function handle(TourService $tourService)
    {
        $now = now();
        $fiveMinutesAgo = $now->copy()->subMinutes(5);
        $toursToEnd = Tour::where('status', 'in_progress')
            ->whereBetween('arrival_time', [$fiveMinutesAgo, $now])
            ->get();
        foreach ($toursToEnd as $tour) {
            $tourService->endTour($tour);
            $this->info("Started tour ID: {$tour->id}");
        }
    }


}
