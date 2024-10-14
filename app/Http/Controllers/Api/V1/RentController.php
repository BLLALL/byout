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
use App\Services\FatoraService;
use Brick\Money\Money;
use Brick\Math\RoundingMode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RentController extends Controller
{
    protected RentalService $rentalService;

    protected FatoraService $fatoraService;
    
    public function __construct(RentalService $rentalService, FatoraService $fatoraService)
    {
        $this->rentalService = $rentalService;
        $this->fatoraService = $fatoraService;
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
            $numberOfNights = $checkIn->diffInDays($checkOut) + 1;
            $amount = $rentable->price * $numberOfNights;
            $money = Money::ofMinor($amount, $rentable->currency, roundingMode: RoundingMode::HALF_UP);
            $request->merge([
                'amount' => $money->getAmount()->toFloat(),
                'currency' => $money->getCurrency()->getCurrencyCode(),
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


    public function getReservedDates(Request $request) {
        $rentableType = $request->input('rentable_type');
        $rentableId = $request->input('rentable_id');
        $rentable = $this->getRentableModel($rentableType, $rentableId);

        if (!$rentable) {
            return response()->json(['message' => 'Invalid rentable type or ID'], 400);
        }
        
        $reservedDates = $rentable->rentals->map(function ($rental) {
            return [
                'check_in' => $rental->check_in->format('Y-m-d'),
                'check_out' => $rental->check_out->format('Y-m-d')
            ];
        });
        return response()->json([
            'reserved_dates' => $reservedDates
        ]);
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
