<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->user_id,
            "name" => $this->user->name,
            "email" => $this->user->email,
            "phone_number" => $this->user->phone_number,
            "age" => $this->user->age,
            "marital_status" => $this->user->marital_status,
            "current_job" => $this->user->current_job,
            "profile_image" => $this->user->profile_image,
            "role" => $this->role,
            "token" => $this->token,
            "organization" => $this->organization,
            "identification_card" => $this->identification_card,
            "licensing" => $this->licensing,
            "affiliation_certificate" => $this->affiliation_certificate,
            "commercial_register" => $this->commercial_register,
        ];
    }
}
