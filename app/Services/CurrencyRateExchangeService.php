<?php

namespace App\Services;

use Worksome\Exchange\Facades\Exchange;

class CurrencyRateExchangeService
{
    public function getExchangeRate($fromCurrency, $toCurrency)
    