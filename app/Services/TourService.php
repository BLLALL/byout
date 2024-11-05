<?php

namespace App\Services;

use App\Models\Owner;
use App\Models\TourReservation;
use App\Models\Vehicle;
use App\Models\Tour;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TourService
{
    public function scheduleTour(Tour $tour)
    {
        $conflictingTours = Tour::where(function ($query) use ($tour) {
            $query->where('vehicle_id', $tour->vehicle_id)
                ->orWhere('driver_id', $tour->driver_id);
        })->where(function ($query) use ($tour) {
            $query->where('departure_time', '<', $tour->arrival_time)
                ->where('arrival_time', '>', $tour->departure_time);
        })->get();


        // Log::info('SQL Query:', DB::getQueryLog());

        // Log::info($conflictingTours);

        // $loggedTour = Tour::find(46);
        // Log::info($loggedTour);
        if ($conflictingTours->isNotEmpty()) {
            throw new Exception('There is a conflict in tours dates with the driver and vehicle you entered');
        }
        $tour->status = 'scheduled';    
        $tour->save();
    }

    public function startTour(Tour $tour)
    {
        $tour->status = 'in_progress';
        $tour->save();
    }

    public function endTour(Tour $tour)
    {
        Log::info('KK');
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

    public function generateReport(Request $request, Owner $owner)
    {
        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));
        $ownerId = $owner->id;
        
        $report = [
            'total_revenue' => $this->calculateTotalRevenue($startDate, $endDate, $ownerId),
            'tours_by_vehicle_type' => $this->getToursByVehicleType($startDate, $endDate, $ownerId),
            'recent_reservations' => $this->getRecentReservations($startDate, $endDate, $ownerId),
        ];

        return response()->json($report);
    }

    private function calculateTotalRevenue($startDate, $endDate, $ownerId)
{
    return Tour::where('owner_id', $ownerId)
        ->whereHas('tourReservations', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->withCount('tourReservations')
        ->get()
        ->sum(function ($tour) {
            $reservationCount = $tour->tour_reservations_count ?? 0;
            $price = $tour->price ?? 0;
            return $price * $reservationCount;
        });
}



    private function getToursByVehicleType($startDate, $endDate, $ownerId)
    {
        return DB::table('tours')
            ->join('vehicles', 'tours.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('tour_reservations', 'tours.id', '=', 'tour_reservations.tour_id')
            ->whereBetween('tour_reservations.created_at', [$startDate, $endDate])
            ->where('tours.owner_id', $ownerId)
            ->select(
                'vehicles.type',
                DB::raw('COUNT(DISTINCT tours.id) as tour_count'),
                DB::raw('COUNT(tour_reservations.id) as reservation_count'),
//                DB::raw('CAST(SUM(tour_reservations.id * tours.price) AS FLOAT) as total_revenue') // Multiplies number of reservations by the tour price
            )
            ->groupBy('vehicles.type')
            ->get();
    }

    private function getRecentReservations($startDate, $endDate, $ownerId)
    {
        return TourReservation::whereBetween('tour_reservations.created_at', [$startDate, $endDate])
            ->join('tours', 'tour_reservations.tour_id', '=', 'tours.id')
            ->where('tours.owner_id', $ownerId)
                    ->with(['user:id,name', 'tour'])
            ->latest('tour_reservations.created_at')
            ->get()
            ->map(function ($reservation) {
                return [
                    'user_name' => $reservation->user->name,
                    'departure_time' => $reservation->tour->departure_time,
                    'arrival_time' => $reservation->tour->arrival_time,
                    'reservation_date' => $reservation->created_at,
                    'tour_departure_time' => $reservation->tour->departure_time,
                    'tour_arrival_time' => $reservation->tour->arrival_time,
                    'amount_paid' => $reservation->tour->price,
                    'vehicle_type' => $reservation->tour->vehicle->type,
                ];
            });
    }
}
