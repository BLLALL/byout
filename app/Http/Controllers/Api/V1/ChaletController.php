<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\filters\ChaletFilter;
use App\Http\Requests\Api\V1\StoreChaletRequest;
use App\Http\Requests\Api\V1\UpdateChaletRequest;
use App\Http\Resources\Api\V1\ChaletResource;
use App\Models\Chalet;
use App\Services\UpdateChaletService;
use App\Services\CreateChaletService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\RentEntityRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\CurrencyRateExchangeService;
use App\Services\RentalService;
class ChaletController extends Controller
{

    protected UpdateChaletService $chaletService;

    protected CreateChaletService $createChaletService;

    protected CurrencyRateExchangeService $currencyRateExchangeService;

    protected RentalService $rentalService;
    public function __construct(UpdateChaletService $chaletService, CreateChaletService $createChaletService, CurrencyRateExchangeService $currencyRateExchangeService, RentalService $rentalService)
    {
        $this->chaletService = $chaletService;
        $this->createChaletService = $createChaletService;
        $this->currencyRateExchangeService = $currencyRateExchangeService;
        $this->rentalService = $rentalService;
    }

    public function index(ChaletFilter $filter)
    {
        $chalets = Chalet::filter($filter)->get();

        $userCurrency = Auth::user()->preferred_currency;

        $currencyRateExchangeService = $this->currencyRateExchangeService;

        $chalets = $chalets->map(function ($chalet) use ($userCurrency, $currencyRateExchangeService) {
            if ($chalet->currency != $userCurrency) {
                $money = $currencyRateExchangeService->convertPrice($chalet->currency, $userCurrency, $chalet->price);
                $chalet->price =  $money->getAmount()->toFloat();
                $chalet->currency = $money->getCurrency();
            }
            return $chalet;
        });
        return ChaletResource::collection($chalets);
    }

    public function show(Chalet $chalet): ChaletResource
    {
        $userCurrency = Auth::user()->preferred_currency;

        $currencyRateExchangeService = $this->currencyRateExchangeService;

        if ($chalet->currency != $userCurrency) {
            $money = $currencyRateExchangeService->convertPrice($chalet->currency, $userCurrency, $chalet->price);
            $chalet->price = $money->getAmount()->toFloat();
            $chalet->currency = $money->getCurrency();
        }
        return new ChaletResource($chalet);
    }

    public function rent(RentEntityRequest $request, Chalet $chalet)
    {
        try {
            $this->rentalService->createRental($chalet, $request);
            return response()->json([
                "message" => "Chalet rented successfully"
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Failed to rent chalet",
                "error" => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreChaletRequest $request)
    {
        if (Auth::user()->id == $request->input('owner_id') && Auth::user()->can('Post Chalets')) {
            $chalet = $this->createChaletService->createEntity($request);
            return new ChaletResource($chalet);
        } else {
            return response()->json(["You're not authorized to store chalets"], 403);
        }
    }

    public function update(UpdateChaletRequest $request, Chalet $chalet)
    {
        if (Auth::user()->id === $chalet->owner->user_id) {
            $this->chaletService->updateChalet($chalet, $request);
            return new ChaletResource($chalet);
        } else {
            return response()->json([
                "You are not authorized to update this resource."
            ]);
        }
    }

    public function destroy(Chalet $chalet)
    {
        if (Auth::user()->hasRole("Chalet Owner") && $chalet->owner->user_id == Auth::user()->id) {
            try {
                $chalet->delete();
                return response()->json([
                    "message" => "Chalet deleted successfully"
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    "message" => 'Failed to delete the chalet. ' . $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                "message" => 'You are not authorized to delete the chalet. ',
            ], 500);
        }
    }
}
