<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\homeOwnerResource;
use App\Models\User;
use App\Services\RentalService;
use App\Services\TourService;
use Illuminate\Http\Request;
use App\Models\Owner;
use Carbon\Carbon;

class OwnerController extends Controller
{
    protected $rentalService, $tourService;

    public function __construct(RentalService $rentalService, TourService $tourService)
    {
        $this->tourService = $tourService;
        $this->rentalService = $rentalService;
    }

    /**
     * Get Home Owners
     * @group Managing Owners
     */
    public function homeOwners()
    {
        $homeOwners = (User::role('Home Owner')->with(['owner', 'roles'])->get()->map(function ($user) {
            return $user->owner;
        })->filter());
        return homeOwnerResource::collection($homeOwners);
    }

    /**
     * Get Owner Financial Report
     * @group Managing Owners
     */
    public function getOwnerFinancialReport(Request $request, Owner $owner)
    {
        $ownerId = $owner->id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $report = $this->rentalService->getOwnerFinancialReport($ownerId, $startDate, $endDate);
        $reportWithTransactions = $report->map(function ($rentable) use ($ownerId) {
            $transactions = $this->rentalService->getLastTransactions($ownerId);
            $rentable['latest_transactions'] = $transactions;
            return $rentable;
        });

        return response()->json(["data" => $reportWithTransactions]);
    }

    /**
     * Get Owner Financial Report By Period
     * @group Managing Owners
     */
    public function getOwnerFincialReportByPeriod(Request $request, Owner $owner)
    {
        $ownerId = $owner->id;
        $period = $request->input('period');
        $report = $this->rentalService->getOwnerFinancialReportByPeriod($ownerId, $period);
        return $report ? response()->json(["data" => $report]) : response()->json(["message" => "No report found for this period"], 404);
    }

    public function getTourFinancialReport(Request $request)
    {
        $authenticatedUserID = auth()->user()->id;
        $owner = Owner::where('user_id', $authenticatedUserID)->first();
        return $this->tourService->generateReport($request, $owner);
    }
}
