<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreHotelRoomRequest extends BaseHotelRoomRequest
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
            'title' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'area' => ['required', 'integer'],
            'bathrooms_no' => ['required', 'integer'],
            'bedrooms_no' => ['required', 'integer'],
            'room_images' => ['required', 'array'],
            'room_images.*' => ['required_with:room_images', 'image', 'mimes:jpg,png,jpeg'],
            'is_reserved' => ['required', 'boolean'],
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],
            'available_from' => ['required', 'date'],
            'available_until' => ['required', 'date'],
        ];
    }
}
