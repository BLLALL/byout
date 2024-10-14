<?php

namespace App\Http\Resources\Api\V1;

use Brick\Math\RoundingMode;
use Brick\Money\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $money = Money::ofMinor($this->payment->amount, $this->payment->currency, roundingMode: RoundingMode::HALF_UP);
        $amount = $money->getAmount()->toFloat();
        $currency = $money->getCurrency()->getCurrencyCode();
        return [
            "id" => $this->id,
            "rentable_id" => $this->rentable_id,
            "rentable_type" => class_basename($this->rentable_type),
            "user_id" => $this->user_id,
            "check_in" => $this->check_in->format('Y-m-d'),
            "check_out" => $this->check_out->format('Y-m-d'),
            "owner_id" => $this->owner->user_id,
            "customer_id" => $this->user_id,
            "payment_amount" => $amount,
            "payment_currency" => $currency,
            "payment_method" => $this->payment->payment_method,
            "payment_status" => $this->payment->payment_status,
            //
            "payment_id" => $this->payment->payment_id,
            "payment_url" => $this->payment->payment_url,
        ];
    }
}
