<?php

namespace App\DTOs;

use App\Http\Requests\Api\V1\RentEntityRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

readonly class RentalDTO
{
    public function __construct(
        public string $rentable_type,
        public string $rentable_id,
        public int    $user_id,
        public Carbon $check_in,
        public Carbon $check_out,
        public string $payment_method,
        public string $currency,
    )
    {
    }

    public static function create(RentEntityRequest $request): self
    {
        return new self(
            rentable_type: $request->input('rentable_type'),
            rentable_id: $request->input('rentable_id'),
            user_id: $request->input('user_id'),
            check_in: Carbon::parse($request->input('check_in')),
            check_out: Carbon::parse($request->input('check_out')),
            payment_method: $request->input('payment_method'),
            currency: $request->user()->preferred_currency,
        );
    }
}
