<?php

namespace App\Http\Requests\Api\V1;


use Illuminate\Validation\Rule;

class storeHomeRequest extends BaseHomeRequest
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
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'area' => ['required', 'integer'],
            'bathrooms_no' => ['required', 'integer'],
            'bedrooms_no' => ['required', 'integer'],
            'location' => ['required', 'string'],
            'home_images' => ['sometimes', 'array'],
            'home_images.*' => ['file', 'max:2048'],
            'living_room_no' => ['required', 'integer'],
            'kitchen_no' => ['required', 'integer'],
            'wifi' => ['required', 'boolean'],
            'coordinates' => ['required' ,'array', 'size:2'],
            'coordinates.0' => ['required' ,'integer' ,'between:-90,90'],
            'coordinates.1' => ['required' ,'integer','between:-180,180'],
            'rent_period' => ['required' ,'string'],
            'owner_id' => ['required' ,'integer' ,'exists:users,id'],
            'available_from' => ['required', 'date'],
            'available_until' => ['required', 'date'],
            'documents' => ['required', 'array'],
            'documents.*.type' => ['required_with:documents', 'string', Rule::in([
                'signatory_authorization',
                'property_ownership',
                'agreement_contract',
            ])],
            'documents.*.file' => ['required_with:documents', 'file', 'max:10240'], // 10MB max
            
        ];
    }
}
