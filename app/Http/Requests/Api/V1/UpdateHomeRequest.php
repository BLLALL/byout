<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHomeRequest extends FormRequest
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
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'price' => 'sometimes|integer',
            'area' => 'sometimes|integer',
            'bathrooms_no' => 'sometimes|integer',
            'bedrooms_no' => 'sometimes|integer',
            'living_room_no' => 'sometimes|integer',
            'kitchen_no' => 'sometimes|integer',
            'location' => 'sometimes|string',
            'new_images' => 'sometimes|array',
            'new_images.*' => 'image|max:2048',
            'remove_images' => 'sometimes|array',
            'remove_images.*' => 'string',
            'wifi' => 'sometimes|boolean',
            'coordinates' => 'sometimes|array|size:2',
            'coordinates.0' => 'sometimes|integer|between:-90,90',
            'coordinates.1' => 'sometimes|integer|between:-180,180',
            'rent_period' => 'sometimes|string',
            'owner_id' => 'sometimes|integer|exists:users,id',
            'available_from' => 'sometimes|date',
            'available_until' => 'sometimes|date',
            'is_available' => 'sometimes|boolean',
        ];
    }
}
