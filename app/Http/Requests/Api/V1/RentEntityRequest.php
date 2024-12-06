<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class RentEntityRequest extends FormRequest
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
            "rentable_type" => ["required", "string", "in:Home,Hotel Room,Chalet"],
            "rentable_id" => ["required_without:room_ids", "integer"],
            "room_ids" => ["required_if:rentable_type,Hotel Room", "array"],
            "room_ids.*" => ["integer", "exists:hotel_rooms,id"],
            "user_id" => ["required", "integer", "exists:users,id"],
            "check_in" => ["required", "date"],
            "check_out" => ["required", "date", "after_or_equal:check_in"],
            "payment_method" => ["required", "string", "in:credit_card,debit_card,paypal,fatora,bank_transfer,other"],
        ];
    }
}
