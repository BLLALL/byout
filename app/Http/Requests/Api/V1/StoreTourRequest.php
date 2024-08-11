<?php

namespace App\Http\Requests\Api\V1;

use App\Rules\ArrivalAfterDeparture;
use App\Rules\validSeatPosition;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTourRequest extends BaseTourRequest
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
            'data' => ['required', 'array'],
            'data.attributes' => ['required', 'array'],
            'data.attributes.price' => ['required', 'integer'],
            'data.attributes.source' => ['required', 'string'],
            'data.attributes.destination' => ['required', 'string'],
            'data.attributes.departure_time' => ['required',  'date_format:h:i A'],
            'data.attributes.arrival_time' => ['required', 'date_format:h:i A'],
            'data.attributes.tour_company_id' => ['required', 'integer', 'exists:tour_companies,id'],
            'data.attributes.seat_position' => ['required', new validSeatPosition],
            'data.attributes.traveller_gender' => ['required', Rule::in(['male', 'female'])]
        ];
    }
}
