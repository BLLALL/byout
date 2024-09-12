<?php

namespace App\Http\Requests\Api\V1;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', ],
            'price' => ['sometimes', 'integer'],
            'area' => ['sometimes', 'integer'],
            'bathrooms_no' => ['sometimes', 'integer'],
            'bedrooms_no' => ['sometimes', 'integer'],
            'room_images' => ['sometimes', 'array'],
            'room_images.*' => ['image', 'mimes:jpg,png,jpeg'],
            'is_reserved' => ['sometimes', 'boolean'],
            'hotel_id' => ['sometimes', 'integer', 'exists:hotels,id'],
        ];
    }
}
