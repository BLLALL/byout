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
            'data.attributes' => 'required|array',
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.price' => 'required|integer',
            'data.attributes.area' => 'required|integer',
            'data.attributes.bathrooms_no' => 'required|integer',
            'data.attributes.bedrooms_no' => 'required|integer',
            'data.attributes.location' => 'required|string',
            'data.attributes.user_id' => 'required|integer|exists:users,id'
        ];
    }
}
