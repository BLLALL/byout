<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScheduleTourRequest extends FormRequest
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
            'tour_type' => ['required', Rule::in(['fixed', 'individual'])],
            'source' => ['required', 'string'],
            'destination' => ['required', 'string', 'max:255'],
            'recurrence' => ['nullable', 'sometimes', 'integer'],
            'transportation_company' => ['sometimes', 'integer', 'exists:tour_companies,id'],
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'driver_id' => [ 'required', 'exists:drivers,id'],
        ];
    }
}
