<?php

namespace App\Http\Requests\Api\V1;


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
            'data' => 'required|array',
            'data.title' => 'required|string',
            'data.description' => 'required|string',
            'data.price' => 'required|integer',
            'data.area' => 'required|integer',
            'data.bathrooms_no' => 'required|integer',
            'data.bedrooms_no' => 'required|integer',
            'data.location' => 'required|string',
            'data.home_images' => 'sometimes|array',
            'data.wifi' => 'required|boolean',
            'data.coordinates' => 'required|array|size:2',
            'data.coordinates.0' => 'required|numeric|between:-90,90',
            'data.coordinates.1' => 'required|numeric|between:-180,180',
            'data.rent_period' => 'required|string',
            'data.user_id' => 'required|integer|exists:users,id'
        ];
    }
}
