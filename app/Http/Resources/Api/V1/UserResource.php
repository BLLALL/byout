<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'phone_number' => $this->phone_number,
            'age' => $this->age,
            'marital_status' => $this->marital_status,
            'current_job' => $this->current_job,
            'profile_image' => $this->profile_image,
            'preferred_currency' => $this->preferred_currency,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'token' => $this->token,
            'role' => $this->roles->pluck('name')->first(),
            'hotel' =>  ($this?->owner?->hotel)->count() ? ($this->owner->hotel)->first() : null,
        ];
    }
}
