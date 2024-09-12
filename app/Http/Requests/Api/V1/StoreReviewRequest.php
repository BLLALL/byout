<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreReviewRequest extends FormRequest
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
            "data.rating" => ['required', 'integer', 'min:1', 'max:5'],
            "data.type" => ['required', 'string', 'in:Home,Hotel,Chalet'],
            "data.user_id" => ['required', 'integer', 'exists:users,id'],
            "data.reviewable_id" => ["required", "integer", "exists:" . $this->getTable() . ",id"],
        ];
    }

    public function getTable()
    {
        $type = $this->input('data.type');
        return Str::plural(strtolower($type));
    }
}
