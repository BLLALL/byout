<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class updateTourRequest extends FormRequest
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
            'tour_type' => ['sometimes', Rule::in(['fixed', 'individual'])],
            'source' => ['sometimes', 'string'],
            'destination' => ['sometimes', 'string', 'max:255'],
            'recurrence' => ['nullable', 'sometimes', 'integer', 'required_if:tour_type,fixed'],
            'transportation_company' => ['sometimes', 'integer', 'exists:tour_companies,id'],
            "departure_time" => ["sometimes", "date"],
            'arrival_time' => ["sometimes", "date", "after:departure_time"],
            'vehicle_id' => ['sometimes', 'integer', 'exists:vehicles,id'],
            'driver_id' => [ 'sometimes', 'exists:drivers,id'],
        ];
    }
}
