<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Worksome\Exchange\Facades\Exchange;
use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use Brick\Math\RoundingMode;
use Brick\Money\Money;

class CurrencyRateExchangeService
{
    protected $exchange;

    public function __construct()
    {
        $this->exchange = new Exchange();
    }


    public function convertPrice($fromCurrency, $toCurrency, $price)
    {
        $exchangeRates = app(ExchangeRate::class);

        $rate = $exchangeRates->exchangeRate($fromCurrency, $toCurrency);

        Log::info('Extracted rates:', ['rate' => $rate]);

        return Money::of($rate * $price, $toCurrency, roundingMode: RoundingMode::UP);
    }
}
