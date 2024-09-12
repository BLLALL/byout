<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ToggleHomeFavouriteRequest extends FormRequest
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
            'data' => ['required', 'array'],
            'data.user_id' => ['required', 'exists:users,id'],
            'data.favorable_id' => ['required', 'integer'],
            'data.favorable_type' => ['required', 'string', 'in:Home,Hotel,Chalet'],
        ];
    }
}
