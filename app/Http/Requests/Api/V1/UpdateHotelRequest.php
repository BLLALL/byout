<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHotelRequest extends FormRequest
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
            'name' => 'sometimes|string',
            'location' => 'sometimes|string',
            'wifi' => 'boolean',
            'coordinates' => 'sometimes|array|size:2',
            'coordinates.0' => 'sometimes|numeric|between:-90,90',
            'coordinates.1' => 'sometimes|numeric|between:-180,180',
            'hotel_images' => 'sometimes|array',
            'hotel_images.*' => 'image|max:2048',
            'hotel_rooms' => 'sometimes|integer',
            'owner_id' => 'sometimes|integer|exists:users,id',
        ];
    }
}
