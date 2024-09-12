<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverRequest extends BaseDriverRequest
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
            "license" => ['sometimes', 'file', 'max:2048'],
            "license_expiry_date" => ['sometimes', "date"],
            "is_smoker" => ['sometimes', "boolean",],
            "name" => ['sometimes', 'string', 'max:255'],
            "email" => ['sometimes', 'email'],
            "password" => ["sometimes", "string", "max:255"],
            "phone_number" => ["sometimes", "numeric",],
            "profile_image" => ["sometimes", "image"],
        ];
    }
}
