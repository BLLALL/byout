<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type' => 'required|in:bus,van,car',
            'model' => 'required|string',
            'registration_number' => 'required|string|unique:vehicles',
            'vehicle_images' => 'required|array',
            'vehicle_images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'seats_number' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $type = $this->input('type');
                    if (!$this->isValidSeatNumber($type, $value)) {
                        $fail('The number of seats is not valid for the selected vehicle type.');
                    }
                },
            ],
            'status' => 'required|in:available,unavailable',
            'has_wifi' => 'boolean',
            'has_air_conditioner' => 'boolean',
            'has_gps' => 'boolean',
            'has_movie_screens' => 'boolean',
            'has_bathroom' => 'boolean|required_if:type,bus',
            'has_entrance_camera' => 'boolean|required_if:type,bus',
            'has_passenger_camera' => 'boolean|required_if:type,bus',
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
