<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Worksome\Exchange\Facades\Exchange;

class ExchangeRateController extends Controller
{
    public function getRates($fromCurrency, $toCurrency)
    {
        try {
            $rate =  Exchange::rates($fromCurrency, $toCurrency);
        } catch (\Exception $e) {
            Log::error('Error fetching exchange rate', $e);
        }
    }
}
