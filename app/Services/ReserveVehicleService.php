<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\TourReservation;
use App\Rules\validSeatPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReserveVehicleService
{

    public function createReservation(Request $request)
    {

        $reservationData = $request->only([
            'tour_id', 'user_id', 'seat_positions', 'traveller_gender'
        ]);

        $tour = Tour::find($reservationData['tour_id']);

        if (!$tour) {
            throw new \Exception('Tour entered is invalid');
        }

        $seatNumber = $tour->vehicle->seats_number;
        $seatStructure = $this->seatStructure($seatNumber);
        $seatPositions = $reservationData['seat_positions'];

        $invalidSeats = array_diff($seatPositions, $seatStructure);
        if (!empty($invalidSeats)) {
            return response()->json([
                "The seat(s) you tried to reserve is/are not valid for the seat structure for this vehicle type.",
                "Invalid seats" => array_values($invalidSeats),
                "The seat structure for this vehicle" => $seatStructure,
            ]);
        }

        $conflictingSeats = $tour->tourReservations()->whereIn('seat_positions', $seatPositions)->pluck('seat_positions')->toArray();
        if (!empty($conflictingSeats)) {
            return response()->json([
                'message' => 'There is/are seat(s) entered already reserved for this tour',
                'conflicting_seats' => $conflictingSeats
            ], 400);
        }
        $travellerGenders = $reservationData['traveller_gender'];
        try {
            DB::beginTransaction();

            $reservations = [];

            foreach ($seatPositions as $index => $seatPosition) {
                $reservations[] = new TourReservation([
                    'user_id' => $reservationData['user_id'],
                    'seat_positions' => $seatPosition,
                    'gender' => $travellerGenders[$index],
                ]);
                $tour->tourReservations()->saveMany($reservations);
            }
            DB::commit();

            return response()->json([
                'message' => 'Seats reserved successfully',
                'reserved_seats' => array_combine($seatPositions, $travellerGenders),
                'reservation_ids' => collect($reservations)->pluck('id'),
            ], 201);

        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to reserve seats',
                'error' => $exception->getMessage()
            ], 500);
        }
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
    }}
