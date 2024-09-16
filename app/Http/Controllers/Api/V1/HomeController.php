<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\filters\HomeFilter;
use App\Http\Requests\Api\V1\RentEntityRequest;
use App\Http\Requests\Api\V1\storeHomeRequest;
use App\Http\Requests\Api\V1\UpdateHomeRequest;
use App\Http\Resources\Api\V1\HomeResource;
use App\Http\Resources\Api\V1\RentalResource;
use App\Models\Home;
use App\Models\Owner;
use App\Services\CreateHomeService;
use App\Services\UpdateHomeService;
use App\traits\apiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Laravel\Prompts\error;
use App\Services\CurrencyRateExchangeService;
use App\Services\RentalService;
use Brick\Money\Money;
use Carbon\Carbon;
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;

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
        $exchangeRates = app(ExchangeRate::class);

        return $exchangeRates->currencies();

        $homes = Home::filter($filter)->get();

        $userCurrency = Auth::user()->preferred_currency;

        $currencyRateExchangeService = $this->currencyRateExchangeService;

        $homes = $homes->map(function ($home) use ($userCurrency, $currencyRateExchangeService) {
            if ($home->currency != $userCurrency) {
                $money = Money::ofMinor($home->price, $home->currency);
                $money = $currencyRateExchangeService->convertPrice($home->currency, $userCurrency, $money->getAmount()->toFloat());
                $home->price = $money->getMinorAmount()->toInt();
                $home->currency = $money->getCurrency()->getCurrencyCode();
            }
            return $home;
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
        if (Auth::user()->id === $home->owner->user_id) {
            $this->updateHomeService->updateHome($home, $request);
            return new HomeResource($home);
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
        if (Auth::user()->hasRole("Home Owner") && $home->owner->user_id == Auth::user()->id) {
            try {
                $home->isAvailable = false;
                $home->save();
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
}
