<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RentEntityRequest;
use App\Models\Chalet;
use App\Models\Home;
use App\Models\HotelRooms;
use App\Services\RentalService;
use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\RentalResource;
use Carbon\Carbon;

class RentController extends Controller
{
    protected RentalService $rentalService;

    public function __construct(RentalService $rentalService)
    {
        $this->rentalService = $rentalService;
    }

    /**
     * Rent a specific entity.
     * @group Managing Rentals
     */
    public function rent(RentEntityRequest $request)        
    {
        $rentableType = $request->input('rentable_type');
        $rentableId = $request->input('rentable_id');
        $rentable = $this->getRentableModel($rentableType, $rentableId);

        if (!$rentable) {
            return response()->json(['message' => 'Invalid rentable type or ID'], 400);
        }

        try {
            $checkIn = Carbon::parse($request->input('check_in'));
            $checkOut = Carbon::parse($request->input('check_out'));
            $numberOfNights = $checkIn->diffInDays($checkOut);
            $amount = $rentable->price * $numberOfNights;

            $request->merge([
                'amount' => $amount,
                'currency' => $rentable->currency
            ]);

            $rental = $this->rentalService->createRental($rentable, $request);
            return new RentalResource($rental);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to rent " . strtolower($rentableType),
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    private function getRentableModel($type, $id)
    {
        return match ($type) {
            'Home' => Home::find($id),
            'Chalet' => Chalet::find($id),
            'Hotel Room' => HotelRooms::find($id),
            default => null,
        };
    }
}
