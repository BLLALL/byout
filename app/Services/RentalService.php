<?php

namespace App\Services;

use App\Models\Owner;
use App\Models\Rental;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\PaymentService;
use Brick\Money\Money;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RentalService
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function createRental(Model $rentable, Request $request)
    {
        $data = $request->input();

        $checkIn = Carbon::parse($data["check_in"]);
        $checkOut = Carbon::parse($data["check_out"]);
        return DB::transaction(function () use ($data, $rentable, $checkIn, $checkOut) {
            if ($this->isAvailable($rentable, $checkIn, $checkOut)) {
                $rental = Rental::create([
                    "rentable_id" => $rentable->id,
                    "rentable_type" => get_class($rentable),
                    'user_id' => $data['user_id'],
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'owner_id' => $rentable->owner_id ?? $rentable->hotel->owner_id,
                ]);
                $paymentData = [
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'payment_method' => $data['payment_method'],
                    'payment_status' => 'completed',
                ];
                $money = Money::of($paymentData['amount'], $paymentData['currency']);
                $paymentData['amount'] = $money->getMinorAmount()->toInt();
                $paymentData['currency'] = $money->getCurrency()->getCurrencyCode();
                $this->paymentService->processPayment($rental, $paymentData);

                return $rental->load('payment');
            }
            throw new Exception("This " . class_basename($rentable) . " is not available for selected dates");
        });
    }

    private function isAvailable($rentable, Carbon $checkIn, Carbon $checkOut)
    {

        if ($checkIn->lt($rentable->available_from) || $checkOut->gt($rentable->available_until)) {
            return false;
        }

        $overlappingRentals = Rental::where('rentable_id', $rentable->id)
            ->where('rentable_type', get_class($rentable))
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->count();


        return $overlappingRentals === 0;
    }

    public function getOwnerFinancialReport(string $ownerId, $startDate, $endDate)
    {
        $owner = Owner::where('user_id', $ownerId)->firstOrFail();
        $ownerId = $owner->id;
        $results = DB::table('rentals')
            ->join('payments', 'rentals.id', '=', 'payments.rental_id')
            ->whereIn('rentals.rentable_type', ['App\Models\Home', 'App\Models\Chalet', 'App\Models\HotelRooms'])
            ->where('rentals.owner_id', $ownerId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('rentals.check_in', [$startDate, $endDate])
                    ->orWhereBetween('rentals.check_out', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('rentals.check_in', '<=', $startDate)
                            ->where('rentals.check_out', '>=', $endDate);
                    });
            })
            ->select(
                'rentals.rentable_type',
                DB::raw('COUNT(*) as total_rentals'),
                DB::raw('SUM(payments.amount) as total_revenue'),
                'payments.currency'
            )
            ->groupBy('rentals.rentable_type', 'payments.currency')
            ->get();

        $formattedResults = $results->map(function ($item) {
            $money = Money::of($item->total_revenue, $item->currency);
            if ($item->rentable_type === 'App\Models\HotelRooms') {
                $item->rentable_type = 'Hotel Room';
            }
            return [
                'rentable_type' => Str::afterLast($item->rentable_type, '\\') ,
                'total_rentals' => $item->total_rentals,
                'total_revenue' => $money->getAmount(),
                'currency' => $item->currency,
            ];
        });

        return $formattedResults;
    }

    public function getOwnerFinancialReportByPeriod(string $ownerId, string $period)
    {
        $startDate = $this->getStartDateByPeriod($period);
        $endDate = now()->endOfDay();
        return $this->getOwnerFinancialReport($ownerId, $startDate, $endDate);
    }

    private function getStartDateByPeriod(string $period)
    {
        return match ($period) {
            'this-month' => now()->startOfMonth(),
            'last-month' => now()->subMonth()->startOfMonth(),
            'last-6-months' => now()->subMonths(6)->startOfMonth(),
            'this-year' => now()->startOfYear(),
            'last-year' => now()->subYear()->startOfYear(),
            default => throw new Exception('Invalid period ' . $period),
        };
    }
    public function getRental($id)
    {
        return Rental::find($id);
    }

    public function getRentalByUser($userId)
    {
        return Rental::where('user_id', $userId)->get();
    }

    public function getRentalByRentable($rentable)
    {
        return Rental::where('rentable_id', $rentable->id)
            ->where('rentable_type', get_class($rentable))
            ->get();
    }
    public function getRentalsByOwner($ownerId)
    {
        return Rental::whereHasMorph('rentable', ['App\Models\Home', 'App\Models\Chalet', 'App\Models\HotelRooms'], function ($query) use ($ownerId) {
            $query->whereHas('owner', function ($subQuery) use ($ownerId) {
                $subQuery->where('user_id', $ownerId);
            });
        })->get();
    }
}
