<?php

namespace App\Services;

use App\Models\Tour;
use App\Models\TourReservation;
use App\Rules\validSeatPosition;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReserveVehicleService
{

    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function createReservation(Request $request)
    {
        $reservationData = $request->only([
            'tour_id',
            'user_id',
            'seat_positions',
            'traveller_gender',
            'payment_method',
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
                "message" => "The seat(s) you tried to reserve is/are not valid for the seat structure for this vehicle type.",
                "invalid_seats" => array_values($invalidSeats),
                "seat_structure" => $seatStructure,
            ], 400);
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

            $reservations = collect();

            foreach ($seatPositions as $index => $seatPosition) {
                $reservation = $tour->tourReservations()->create([
                    'user_id' => $reservationData['user_id'],
                    'seat_positions' => $seatPosition,
                    'gender' => $travellerGenders[$index],
                ]);
                $reservations->push($reservation);
            }

            $totalAmount = $tour->price * count($seatPositions);

            $payment = null;
            if ($reservationData['payment_method'] == 'fatora') {
                // Process payment for the first reservation, but with the total amount
                $payment = $this->paymentService->processPayment($reservations->first(), [
                    'amount' => $totalAmount,
                    'currency' => $tour->currency,
                    'payment_method' => $reservationData['payment_method'],
                ]);

                // Associate the payment with all reservations
                foreach ($reservations as $reservation) {
                    $reservation->payment()->save($payment);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Seats reserved successfully',
                'reserved_seats' => array_combine($seatPositions, $travellerGenders),
                'reservation_ids' => $reservations->pluck('id'),
                'payment_id' => $payment?->payment_id,
                'amount' => $payment?->amount,
                'payment_url' => $payment?->payment_url,
                'currency' => $payment?->currency,

            ], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to reserve seats',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
}
