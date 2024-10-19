<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ReserveTourRequest;
use App\Models\Tour;
use App\Models\TourReservation;
use App\Models\User;
use App\Services\ReserveCarService;
use App\Services\ReserveVanService;
use App\Services\ReserveBusService;

use App\Services\ReserveVehicleService;
use App\traits\apiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\Types\Collection;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

class TourReservationController extends Controller
{
    use apiResponses;

    protected ReserveCarService $reserveCarService;
    protected ReserveVanService $reserveVanService;
    protected ReserveBusService $reserveBusService;

    public function __construct(ReserveCarService $reserveCarService, ReserveVanService $reserveVanService, ReserveBusService $reserveBusService)
    {
        $this->reserveCarService = $reserveCarService;
        $this->reserveVanService = $reserveVanService;
        $this->reserveBusService = $reserveBusService;

    }

    public function reserve(ReserveTourRequest $request)
    {
        $tour = Tour::find($request->tour_id);

        $vehicle = $tour->vehicle;
        return $this->reserveVehicle($vehicle, $request);
    }

    private function reserveVehicle($vehicle, $request)
    {
        return match($vehicle->type) {
            "bus" => $this->reserveBusService->createReservation($request),
            "van" => $this->reserveVanService->createReservation($request),
            "car" => $this->reserveCarService->createReservation($request),
        };
    }

    //not used anymore
    public function past_Reserve(Request $request)
    {
        $validated = $request->validate([
            'seat_positions' => ['required', 'array'],
            'seat_positions.*' => ['required', 'string', 'size:2', Rule::in($this->seats)],
            'tour_id' => ['required', 'integer', 'exists:tours,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'traveller_gender' => ['required', 'array', 'size:' . count($request->seat_positions)],
            'traveller_gender.*' => ['required', 'string', Rule::in(['male', 'female'])],
            
        ]);

        $seatPositions = $validated['seat_positions'];
        $travellerGenders = $validated['traveller_gender'];
        $tour = Tour::findOrFail($validated['tour_id']);

        // Check for existing reservations

        $existingReservations = $tour->tourReservations()
            ->whereIn('seat_positions', $seatPositions)
            ->get();


        if ($existingReservations->isNotEmpty()) {
            $conflictingSeats = collect($seatPositions)->filter(function ($seat) use ($existingReservations) {
                return $existingReservations->pluck('seat_positions')->flatten()->contains($seat);
            });

            return $this->error([
                'message' => 'Some seats are already reserved for this tour',
                'conflicting_seats' => $conflictingSeats->values()->all()
            ], 400);
        }

        try {
            DB::beginTransaction();

            $reservations = [];
            foreach ($seatPositions as $index => $seatPosition) {
                $reservations[] = new TourReservation([
                    'user_id' => $validated['user_id'],
                    'seat_positions' => $seatPosition,
                    'gender' => $travellerGenders[$index],
                ]);
            }



            $tour->tourReservations()->saveMany($reservations);

           

            DB::commit();

            return response()->json([
                'message' => 'Seats reserved successfully',
                'reserved_seats' => array_combine($seatPositions, $travellerGenders),
                'reservation_ids' => collect($reservations)->pluck('id'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([
                'message' => 'Failed to reserve seats',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAvailableSeats($tour_id)
    {
        $tour = Tour::findOrFail($tour_id);

        $reserved_seats = $tour->tourReservations()
            ->get()
            ->pluck('seat_positions')
            ->flatten()
            ->unique();

        $available_seats = collect($this->seats)->diff($reserved_seats);

        return response()->json([
            "available_seats" => $available_seats->values()
        ]);
    }


    public function getReservedSeats($tourId)
    {
        $tour = Tour::find($tourId);
        if(!$tour) return "Tour u entered not valid";
        $reserved_seats = $tour->tourReservations
            ->flatMap(function ($reservation) {
                $seats = $reservation->seat_positions;
                return collect($seats)->mapWithKeys(function ($seat) use ($reservation) {
                    return [$seat => [
                        'gender' => $reservation->gender,
                        'user_id' => $reservation->user_id,
                        'position' => $reservation->seat_positions,
                    ]];
                }); 
            }); 
        if($reserved_seats->isEmpty()) return null;

        return response()->json([
            'reserved_seats' => $reserved_seats,
        ]);
    }
}
