<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelRequest extends BaseHotelRequest
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
            'data.name' => 'required|string',
            'data.location' => 'required|string',
            'data.wifi' => 'boolean',
            'data.coordinates' => 'required|array|size:2',
            'data.coordinates.0' => 'required|numeric|between:-90,90',
            'data.coordinates.1' => 'required|numeric|between:-180,180',
            'data.hotel_images' => 'required|array',
            'data.hotel_rooms' => 'required|integer',
            'data.user_id' => 'required|integer|exists:users,id',
            'data.rooms' => 'array|sometimes',
            'data.rooms.*.price' => 'required|integer',
            'data.rooms.*.area' => 'required|integer',
            'data.rooms.*.bathrooms_no' => 'required|integer',
            'data.rooms.*.bedrooms_no' => 'required|integer',
            'data.rooms.*.is_reserved' => 'boolean',
        ];
    }
}
