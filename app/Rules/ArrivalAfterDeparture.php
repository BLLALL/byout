<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ArrivalAfterDeparture implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    protected $departureTime;

    public function __construct($departureTime)
    {
        $this->departureTime = $departureTime;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $departure = Carbon::createFromFormat('h:i A', $this->departureTime);
        $arrival = Carbon::createFromFormat('h:i A', $value);

        if($departure->isAfter($arrival)) {
            $fail('The arrival time must be after departure time.');
        }
    }
}
