<?php

namespace App\Services;

use AshAllenDesign\LaravelExchangeRates\Classes\ExchangeRate;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Worksome\Exchange\Facades\Exchange;

class CurrencyRateExchangeService
{
    protected Exchange $exchange;

    public function __construct()
    {
        $this->exchange = new Exchange();
    }

    public function convertEntityPrice($entities, string $userCurrency)
    {
        $entities->map(function ($entity) use ($userCurrency) {
            if ($entity->currency != $userCurrency) {
                $money = $this->convertPrice($entity->currency, $userCurrency, $entity->price);
                $entity->price = $money->getAmount()->toFloat();
                $entity->currency = $money->getCurrency();
            }
            return $entity;
        });
        return $entities;
    }

    public function convertPrice($fromCurrency, $toCurrency, $price): Money
    {
        $exchangeRates = app(ExchangeRate::class);
        $rate = $exchangeRates->exchangeRate($fromCurrency, $toCurrency);
        return Money::of($rate * $price, $toCurrency, roundingMode: RoundingMode::HALF_UP);
    }
}
