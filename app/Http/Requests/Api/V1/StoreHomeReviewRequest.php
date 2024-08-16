<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreHomeReviewRequest extends FormRequest
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
    public function rules() : array
    {
        return [
            'data.rating' => ['required', 'integer', 'in:1,2,3,4,5'],
            'data.user_id' => ['required', 'integer', 'exists:users,id'],
            'data.id' => ['required', 'integer', 'exists:homes,id'],
        ];
    }
}
