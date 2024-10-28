<?php

namespace App\traits;

trait HasPendingUpdates
{

    public function requestUpdate(array $attributes) {
        return $this->pendingRequests()->create([
            'changes' => $attributes,
            'owner_id' => $this->owner_id,
        ]);
        
    }
    public function hasPendingRequests(array $attributes): mixed {
        return $this->pendingUpdates()->exists();
    }
}
