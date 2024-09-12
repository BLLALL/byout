<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class homeOwnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->user->name,
            'id' => $this->user_id,
            'role' => $this->user->roles->pluck('name'),
            'organization' => $this->organization,
            'identification_card' => $this->identification_card,
            'licensing' => $this->licensing,
            'affiliation_certificate' => $this->affiliation_certificate,
            'commercial_register' => $this->commercial_register,
        ];
    }
}
