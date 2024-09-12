<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => ['string', 'max:100'],
            'email' => ['email', 'string'],
            'profile_image' => ['image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'phone_number' => ['integer', 'max:11'],
            'age' => ['integer', 'max:100', 'min:10'],
            'marital_status' => ['string', Rule::in(["single", "married", "divorced"])],
            'current_job' => [ 'string'],
        ];
    }
}
