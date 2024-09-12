<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class storeBusRequest extends BaseBusRequest
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
            "registration_number" => ['required', "string", "max:255"],
            "model" => ['required', "string", "max:255"],
            "bus_images" => ['required', 'array'],
            "bus_images.*" => ['required', 'max:2048'],
            'seats_number' => ['required', 'integer', 'max:255'],
            'has_wifi' => ['required', 'boolean' ],
            'has_air_conditioner' => ['required', 'boolean'],
            'has_bathroom' => ['required', 'boolean'],
            'has_movie_screens' => ['required', 'boolean'],
            'has_entrance_camera' => ['required', 'boolean'],
            'has_passenger_camera' => ['required', 'boolean'],
        ];
    }
}
