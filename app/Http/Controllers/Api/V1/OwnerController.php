<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\homeOwnerResource;
use App\Models\User;
use App\Services\RentalService;
use Illuminate\Http\Request;
use App\Models\Owner;
use Carbon\Carbon;

class OwnerController extends Controller
{
    protected $rentalService;

    public function __construct(RentalService $rentalService)
    {
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
        return response()->json($report);
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
        return response()->json($report);
    }
    
}
