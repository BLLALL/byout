<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverRequest extends BaseDriverRequest
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
            "license" => ['required', 'file', 'max:2048'],
            "license_expiry_date" => ['required', "date"],
            "is_smoker" => ['required', "boolean",],
            "name" => ['required', 'string', 'max:255'],
            "phone_number" => ["required", "numeric",],
            "profile_image" => ["sometimes", "image"],
        ];
    }
}
