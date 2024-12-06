<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateHotelRoomRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'price' => ['sometimes', 'numeric'],
            'discount_price' =>[ 'sometimes' ,'numeric'],
            'area' => ['sometimes', 'integer'],
            'bathrooms_no' => ['sometimes', 'integer'],
            'bedrooms_no' => ['sometimes', 'integer'],
            'room_images' => ['sometimes', 'array'],
            'room_images.*' => ['image', 'mimes:jpg,png,jpeg'],
            'hotel_id' => ['sometimes', 'integer', 'exists:hotels,id'],
        ];
    }
}
