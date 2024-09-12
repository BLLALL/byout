<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChaletRequest extends BaseChaletRequest
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
            'title' => ['required', 'string', 'max:128'],
            'description' => ['required', 'string', 'max:1024'],
            'price' => ['required', 'numeric', 'min:0'],
            'area' => ['required', 'integer'],
            'bathrooms_no' => ['required', 'integer'],
            'bedrooms_no' => ['required', 'integer'],
            'chalet_images' => ['required', 'array'],
            'chalet_images.*' => ['required', 'image', 'mimes:png,jpg,jpeg'],
            'location' => ['required', 'string'],
            'wifi' => ['required', 'boolean'],
            'coordinates' => ['sometimes', 'array'],
            'coordinates.0' => ['required', 'integer', 'between:-90,90'],
            'coordinates.1' => ['required', 'integer', 'between:-180,180'],
            'rent_period' =>  ['required', 'string'],
            'owner_id' => ['required', 'integer', 'exists:users,id'],
            'air_conditioning' => ['required', 'boolean'],
            'sea_view' => ['required', 'boolean'],
            'distance_to_beach' => ['sometimes', 'integer'],
            'max_occupancy' => ['required', 'string'],
            'documents' => ['required', 'array'],
            'documents.*.type' => ['required_with:documents', 'string', Rule::in([
                'signatory_authorization',
                'property_ownership',
                'agreement_contract',
            ])],
            'documents.*.file' => ['required_with:documents', 'file', 'max:10240'],
            'available_from' => ['required', 'date'],
            'available_until' => ['required', 'date'],
        ];
    }
}
