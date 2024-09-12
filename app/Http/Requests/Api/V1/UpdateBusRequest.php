<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBusRequest extends BaseBusRequest
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
            "registration_number" => ['sometimes', "string", "max:255"],
            "model" => ['sometimes', "string", "max:255"],
            "bus_images" => ['sometimes', 'array'],
            "bus_images.*" => ['sometimes', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'seats_number' => ['sometimes', 'integer', 'max:255'],
            'has_wifi' => ['sometimes', 'boolean' ],
            'has_bathroom' => ['sometimes', 'boolean'],
            'has_air_conditioner' => ['sometimes', 'boolean'],
            'has_movie_screens' => ['sometimes', 'boolean'],
            'has_entrance_camera' => ['sometimes', 'boolean'],
            'has_passenger_camera' => ['sometimes', 'boolean'],
        ];
    }
}
