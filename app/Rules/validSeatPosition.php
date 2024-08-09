<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class validSeatPosition implements ValidationRule
{

    protected $validPositions;

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function __construct()
    {
        $this->validPositions = [
            'a1', 'a2', 'a3', 'a4',
            'b1', 'b2', 'b3', 'b4',
            'c1', 'c2', 'c3', 'c4',
            'd1', 'd2', 'd3', 'd4',
            'e1', 'e2', 'e3', 'e4',
            'f1', 'f2', 'f3', 'f4',
            'g1', 'g2', 'g3', 'g4',
            'h1', 'h2', 'h3', 'h4',
        ];
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!in_array(strtolower($value), $this->validPositions)) {
            $fail('The :attribute must be a valid seat position (from a1 to h4).');
        }
    }
}
