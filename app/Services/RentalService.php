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
use Illuminate\Support\Collection;


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
                $paymentData['amount'] = $money->getAmount()->toFloat();
                $paymentData['currency'] = $money->getCurrency()->getCurrencyCode();
                $this->paymentService->processPayment($rental, $paymentData);

                return $rental->load('payment');
            }
            throw new Exception("This " . class_basename($rentable) . " is not available for selected dates");
        });
    }

    private function isAvailable($rentable, Carbon $checkIn, Carbon $checkOut)
    {
        if (!$this->isWithinAvailableDates($rentable, $checkIn, $checkOut)) {
            return false;
        }

        return !$this->hasOverlappingRentals($rentable, $checkIn, $checkOut);
    }

    private function isWithinAvailableDates($rentable, Carbon $checkIn, Carbon $checkOut)
    {
        return $checkIn->gte($rentable->available_from) && $checkOut->lte($rentable->available_until);
    }

    private function hasOverlappingRentals($rentable, Carbon $checkIn, Carbon $checkOut)
    {
        return Rental::where('rentable_id', $rentable->id)
            ->where('rentable_type', get_class($rentable))
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    $q->whereBetween('check_in', [$checkIn, $checkOut])
                        ->orWhereBetween('check_out', [$checkIn, $checkOut]);
                })
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->exists();
    }

    public function getOwnerFinancialReport(string $ownerId, $startDate, $endDate)
    {
        $owner = $this->getOwnerByUserId($ownerId);
        $results = $this->getRentalDataForOwner($owner->id, $startDate, $endDate);
        return $this->formatFinancialResults($results);
    }

    private function getOwnerByUserId(string $userId)
    {
        return Owner::where('user_id', $userId)->firstOrFail();
    }

    private function getRentalDataForOwner(int $ownerId, $startDate, $endDate)
        {
            return DB::table('rentals')
                ->join('payments', 'rentals.id', '=', 'payments.rental_id')
                ->whereIn('rentals.rentable_type', ['App\Models\Home', 'App\Models\Chalet', 'App\Models\HotelRooms'])
                ->where('rentals.owner_id', operator: $ownerId)
                ->where($this->getRentalDateCondition($startDate, $endDate))
                ->select(
                    'rentals.rentable_type',

                    DB::raw('COUNT(*) as total_rentals'),
                    DB::raw('SUM(payments.amount) as total_revenue'),
                    'payments.currency'
                )
                ->groupBy('rentals.rentable_type', 'payments.currency')
                ->get();
    }

    public function getLastTransactions($ownerId)
    {
        $owner = $this->getOwnerByUserId($ownerId);
        $rents = Rental::with('user', 'rentable')->where('owner_id', $owner->id)->get();
        $transactions = new Collection();
        foreach ($rents as $rent) {
            $user = $rent->user;
            $rentable = $rent->rentable;
            $transactions->push([
                "user_id" => $user->id,
                "user_name" => $user->name,
                "rentable_title" => $rentable->title,
                "rentable_price" => $rentable->price,
                "rentable_currency" => $rentable->currency,
                "check_in" => $rent->check_in->format('Y-m-d'),
                "check_out" => $rent->check_out->format('Y-m-d'),
                "rented_at" => $rent->created_at,
            ]);
        }
        return $transactions->sortByDesc('created_at');
    }

    private function getRentalDateCondition($startDate, $endDate)
    {
        return function ($query) use ($startDate, $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('rentals.check_in', [$startDate, $endDate])
                    ->orWhereBetween('rentals.check_out', [$startDate, $endDate])
                    ->orWhere(function ($subQ) use ($startDate, $endDate) {
                        $subQ->where('rentals.check_in', '<=', $startDate)
                            ->where('rentals.check_out', '>=', $endDate);
                    });
            });
        };
    }

    private function formatFinancialResults($results)
    {
        return $results->map(function ($item) {
            $money = Money::of($item->total_revenue, $item->currency);
            $rentableType = $item->rentable_type === 'App\Models\HotelRooms' ? 'Hotel Room' : Str::afterLast($item->rentable_type, '\\');
            return [
                'rentable_type' => $rentableType,
                'total_rentals' => $item->total_rentals,
                'total_revenue' => $money->getAmount()->toInt(),
                'currency' => $item->currency,
            ];
        });
    }

    public function getOwnerFinancialReportByPeriod(string $ownerId, string $period)
    {
        $startDate = $this->getStartDateByPeriod($period)->format('Y-m-d');
        $endDate = now()->endOfDay()->format('Y-m-d');

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
