<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterOwnerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone_number' => ['required', 'numeric'],
            'identification_card' => ['required', 'file', 'max:2048'],
            'licensing' => ['required', 'file', 'max:2048'],
            'affiliation_certificate' => ['required', 'file', 'max:2048'],
            'commercial_register' => ['required', 'file', 'max:2048'],
            'role' => ['required', 'string', Rule::in(['Home Owner', 'Hotel Owner', 'Tour Company Owner', 'Chalet Owner'])],
        ];
    }
}
