<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name' => 'required|string',
            'location' => 'required|string',
            'wifi' => 'boolean',
            'coordinates' => 'required|array|size:2',
            'coordinates.0' => 'required|numeric|between:-90,90',
            'coordinates.1' => 'required|numeric|between:-180,180',
            'hotel_images' => 'required|array',
            'hotel_images.*' => 'file|max:2048',
            'owner_id' => 'required|integer|exists:users,id',
            'rooms' => 'sometimes|array',
            'rooms.*.title' => 'required|string',
            'rooms.*.price' => 'required|numeric',
            'rooms.*.area' => 'required|numeric',
            'rooms.*.bathrooms_no' => 'required|integer',
            'rooms.*.bedrooms_no' => 'required|integer',
            'rooms.*.room_images' => 'required|array',
            'rooms.*.room_images.*' => 'required|file|max:2048',
            'rooms.*.is_reserved' => 'boolean',
            'documents' => ['required', 'array'],
            'documents.*.type' => ['required_with:documents', 'string', Rule::in([
                'signatory_authorization',
                'property_ownership',
                'agreement_contract',
                'hotel_license',
            ])],
            'documents.*.file' => ['required_with:documents', 'file', 'max:10240'],

        ];
    }
}
