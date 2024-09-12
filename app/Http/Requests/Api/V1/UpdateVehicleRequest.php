<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules()
    {
        return [
            'type' => ['sometimes', Rule::in(['car', 'van', 'bus'])],
            'model' => 'sometimes|string',
            'registration_number' => 'sometimes|string|unique:vehicles,registration_number,' . $this->vehicle->id,
            'vehicle_images' => 'sometimes|array',
            'vehicle_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB Max
            'seats_number' => [
                'sometimes',
                'integer',
                function ($attribute, $value, $fail) {
                    $type = $this->input('type', $this->vehicle->type);
                    if (!$this->isValidSeatNumber($type, $value)) {
                        $fail('The number of seats is not valid for the selected vehicle type.');
                    }
                },
            ],
            'status' => 'sometimes|in:available,unavailable',
            'has_wifi' => 'sometimes|boolean',
            'has_air_conditioner' => 'sometimes|boolean',
            'has_gps' => 'sometimes|boolean',
            'has_movie_screens' => 'sometimes|boolean',
            'has_entrance_camera' => 'sometimes|boolean',
            'has_passenger_camera' => 'sometimes|boolean',
            'has_bathroom' => 'sometimes|boolean',
        ];
    }

    private function isValidSeatNumber($type, $seats)
    {
        return match ($type) {
            'car' => in_array($seats, [3, 5]),
            'van' => in_array($seats, [7, 14]),
            'bus' => in_array($seats, [24, 30, 46, 49, 53, 67]),
            default => false,
        };
    }
}
