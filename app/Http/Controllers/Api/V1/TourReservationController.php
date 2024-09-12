<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourReservation;
use App\Models\User;
use App\traits\apiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\Types\Collection;

class TourReservationController extends Controller
{
    use apiResponses;

    protected $seats = ['a1', 'a2', 'a3', 'a4',
        'b1', 'b2', 'b3', 'b4',
        'c1', 'c2', 'c3', 'c4',
        'd1', 'd2', 'd3', 'd4',
        'e1', 'e2', 'e3', 'e4',
        'f1', 'f2', 'f3', 'f4',
        'g1', 'g2', 'g3', 'g4',
        'h1', 'h2', 'h3', 'h4',
    ];

    public function __construct()
    {
        $this->seats = collect($this->seats);
    }

    public function reserve(Request $request)
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
