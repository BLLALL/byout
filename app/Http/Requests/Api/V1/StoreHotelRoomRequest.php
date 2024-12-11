<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'price' => ['required', 'numeric', 'min:0'],
            'area' => ['required', 'integer'],
            'bathrooms_no' => ['required', 'integer'],
            'room_images' => ['array'],
            'room_images.*' => ['required_with:room_images', 'image', 'mimes:jpg,png,jpeg'],
            'hotel_id' => ['required', 'integer', 'exists:hotels,id'],
            'available_from' => ['required', 'date'],
            'available_until' => ['required', 'date'],
            'capacity' => ['required', 'integer', 'min:1'],
            'room_type' => ['required', 'string', Rule::in([
                'room',
                'junior_suite',
                'suite',
                'executive_suite',
                'presidential_suite',
            ])],
            'beds' => ['required', 'array'],
            'beds.*' => ['required_with:beds', 'array'],
            'beds.*.bed_type' => ['required_with:beds.*', 'string', Rule::in([
                'single bed',
                'double bed',
                'large double bed',
                'extra large double bed',
                'sofa bed',
                'bunk bed',
            ])],
            'amenities' => ['sometimes', 'array'],
            'amenities.*' => ['required_with:amenities', Rule::in(
                $allowedAmenities = [
                    'air_conditioner',
                        'free_wifi',
                        'sea_view',
                        'balcony',
                        'tv',
                        'breakfast',
                        'washing_machine',
                        'dryer',
                        'mountain_view',
                        'city_view',
                        'terrace',
                        'heating',
                        'coffee_machine',
                        'free_parking',
                        'swimming_pools',
                        'restaurant',
                        'reception_service',
                        'cleaning_service',
                        'garden',
                        ]),]
        ];
    }
}
