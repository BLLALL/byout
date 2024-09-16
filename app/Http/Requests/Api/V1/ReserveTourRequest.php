<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReserveTourRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'seat_positions' => ['required', 'array'],
            'seat_positions.*' => ['required', 'string', 'size:2'],
            'tour_id' => ['required', 'integer', 'exists:tours,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'traveller_gender' => ['required', 'array', 'size:' . count($this->seat_positions)],
            'traveller_gender.*' => ['required', 'string', Rule::in(['male', 'female'])],
        ];
    }
}
