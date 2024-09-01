<?php

namespace App\Services;

use App\Models\Bus;
use App\Models\Tour;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TourService
{
    public function scheduleTour(Tour $tour)
    {
        $bus = Bus::findOrFail($tour->bus_id);



        $conflictingTours = Tour::where('bus_id', $tour->bus_id)
            ->where(function ($query) use ($tour) {
                $query->where('departure_time', '<', $tour->arrival_time)
                    ->where('arrival_time', '>', $tour->departure_time);
            })->get();
        Log::info('SQL Query:', DB::getQueryLog());

        Log::info($conflictingTours);

        $loggedTour = Tour::find(46);
        Log::info($loggedTour);

        if ($conflictingTours->isNotEmpty()) {
            throw new \Exception('Bus is already scheduled for another tour during this time');
        }
        $tour->status = 'scheduled';
        $tour->save();
    }

    public function startTour(Tour $tour)
    {
        $bus = Bus::findOrFail($tour->bus_id);
        $bus->status = "in use";
        $bus->save();

        $tour->status = 'in_progress';
        $tour->save();
    }

    public function endTour(Tour $tour)
    {
        $bus = Bus::find($tour->bus_id);

        $bus->status = "available";
        $bus->save();

        if ($tour->tour_type == 'fixed') {
            $tour->status = "scheduled";
            $this->createRecurringTour($tour, $tour->recurrence);
        } else
            $tour->status = "completed";
        $tour->save();

    }

    public function createRecurringTour(Tour $tour, int $recurrence)
    {
        $departureTime = Carbon::parse($tour->departure_time);
        $arrivalTime = Carbon::parse($tour->arrival_time);

        $departureTime = $departureTime->addDays($recurrence);
        $arrivalTime = $arrivalTime->addDays($recurrence);

        Tour::where('id', $tour->id)
            ->update([
                'departure_time' => $departureTime,
                'arrival_time' => $arrivalTime,
            ]);
    }

    public function removeRecurringTour(Tour $tour)
    {
        $tour->recurrence = null;
        $tour->status = 'completed';
    }
}
