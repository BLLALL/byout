<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\filters\HomeFilter;
use App\Http\Requests\Api\V1\storeHomeRequest;
use App\Http\Requests\Api\V1\UpdateHomeRequest;
use App\Http\Resources\Api\V1\HomeResource;
use App\Http\Resources\Api\V1\PendingUpdateResource;
use App\Models\Home;
use App\Services\CreateHomeService;
use App\Services\CurrencyRateExchangeService;
use App\Services\RentalService;
use App\Services\UpdateHomeService;
use App\traits\apiResponses;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    use apiResponses;

    protected UpdateHomeService $updateHomeService;
    protected CreateHomeService $createHomeService;
    protected CurrencyRateExchangeService $currencyRateExchangeService;
    protected RentalService $rentalService;

    public function __construct(UpdateHomeService $updateHomeService, CreateHomeService $createHomeService, CurrencyRateExchangeService $currencyRateExchangeService, RentalService $rentalService)
    {
        $this->updateHomeService = $updateHomeService;
        $this->createHomeService = $createHomeService;
        $this->currencyRateExchangeService = $currencyRateExchangeService;
        $this->rentalService = $rentalService;
    }

    /**
     * Get Homes
     *
     * @group Managing Homes
     * @queryParam sort string Data field to sort by. Separate multiple parameters with commas. Denote descending order with a minus sign.
     * Example: title, -created_at
     * @queryParam price integer Data field to filter homes by their price u can use comma to filter by range. Example: 2000,100000
     * @queryParam title string Data field to search for homes by their title. Example:Lorem
     * @queryParam description string Data field to search for homes by their description. Example:Lorem Ipsum
     *
     */

    public function index(HomeFilter $filter)
    {
        $homes = Home::filter($filter)->get();
        $userCurrency = Auth::user()->preferred_currency;
        $homes = $this->currencyRateExchangeService->convertEntityPrice($homes, $userCurrency);
        $homes = $homes->filter(function ($home) {
            return $this->filterByPrice($home);
        });
        return HomeResource::collection($homes);
    }


    /**
     * Create a Home
     *
     * @group Managing Homes
     *
     *
     */
    public function store(storeHomeRequest $request)
    {
        if (Auth::user()->can('Post Homes') && Auth::user()->id == $request->owner_id) {
            $home = $this->createHomeService->createEntity($request);
            return new HomeResource($home);
        } else {
            return $this->error([
                "You aren't authorized to store this home"
            ], 403);
        }
    }

    /**
     * show a specific Home
     *
     *Display an individual home
     *
     * @group Managing Homes
     */
    public function show(Home $home)
    {
        $userCurrency = Auth::user()->preferred_currency;

        if ($home->currency != $userCurrency) {
            $currencyRateExchangeService = $this->currencyRateExchangeService;
            $money = $currencyRateExchangeService->convertPrice($home->currency, $userCurrency, $home->price);
            $home->price = $money->getAmount()->toFloat();
            $home->currency = $money->getCurrency();
        }

        return new HomeResource($home);
    }

    /**
     * Update a specific home.
     * @group Managing Homes
     */
    public function update(UpdateHomeRequest $request, Home $home)
    {
        if (Auth::user()->can('Post Homes')) {
            $pendingUpdate = $this->updateHomeService->updateHome($home, $request);

            return response()->json([
                'message' => 'Update request submitted for approval',
                'pending_update' => new PendingUpdateResource($pendingUpdate)
            ]);
        } else {
            return response()->json([
                "You are not authorized to update this resource."
            ]);
        }
    }

    /**
     * Remove a specific home.
     * @group Managing Homes
     */
    public function destroy(Home $home)
    {
        if ($home->owner->user_id == Auth::user()->id) {
            try {
                $home->isAvailable = false;
                $home->delete();
                return response()->json([
                    "message" => "Home deleted successfully"
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    "message" => 'Failed to delete the home. ' . $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                "message" => 'You are not authorized to delete the home. ',
            ], 500);
        }
    }

    private function filterAndConvertRooms(Collection $homes, string $userCurrency)
    {
        return $homes->map(fn($home) => $this->convertRoomPrice($home, $userCurrency))
            ->filter(fn($home) => $this->filterByPrice($home));
    }

    private function convertRoomPrice(Home $home, string $userCurrency): Home
    {
        if ($home->currency != $userCurrency) {
            $money = $this->currencyRateExchangeService->convertPrice($home->currency, $userCurrency, $home->price);
            $home->price = $money->getAmount()->toFloat();
            $home->currency = $money->getCurrency();
        }
        return $home;
    }

    private function filterByPrice(Home $home): bool
    {
        $priceFilter = request('price');
        if (!$priceFilter) return true;

        $prices = explode(',', $priceFilter);
        if (count($prices) > 1)
            return $home->price >= $prices[0] && $home->price <= $prices[1];

        return $home->price <= $prices[0];
    }

}
