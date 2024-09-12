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
            // User rules
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8', //|confirmed',
            'phone_number' => 'required|numeric',


            // Owner rules
            'organization' => ['string', 'max:128'],
            'identification_card' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'licensing' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'affiliation_certificate' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'commercial_register' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'role' => 'required|string|in:Home Owner,Hotel Owner,Tour Company Owner,Chalet Owner',

        ];
    }
}
