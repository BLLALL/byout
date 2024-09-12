<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChaletRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:128'],
            'description' => ['sometimes', 'string', 'max:1024'],
            'price' => ['sometimes', 'integer',],
            'area' => ['sometimes', 'integer'],
            'bathrooms_no' => ['sometimes', 'integer'],
            'bedrooms_no' => ['sometimes', 'integer'],
            'chalet_images' => ['sometimes', 'array'],
            'chalet_images.*' => ['sometimes', 'image', 'mimes:png,jpg,jpeg'],
            'location' => ['sometimes', 'string'],
            'wifi' => ['sometimes', 'boolean'],
            'coordinates' => ['sometimes', 'array'],
            'coordinates.0' => ['sometimes', 'integer', 'between:-90,90'],
            'coordinates.1' => ['sometimes', 'integer', 'between:-180,180'],
            'rent_period' => ['sometimes', 'string'],
            'owner_id' => ['sometimes', 'integer', 'exists:users,id'],
            'air_conditioning' => ['sometimes', 'boolean'],
            'sea_view' => ['sometimes', 'boolean'],
            'distance_to_beach' => ['sometimes', 'integer'],
            'available_from' => ['sometimes', 'date'],
            'available_until' => ['sometimes', 'date'],
            'max_occupancy' => ['sometimes', 'string'],
            'is_available' => ['sometimes', 'boolean'],

        ];
    }
}
