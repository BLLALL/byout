<?php

namespace App\Services;

use App\Models\PendingUpdates;
use Illuminate\Http\Request;

class PendingUpdateService extends UpdateEntityService
{
    public function createPendingUpdate($entity, array $data, String $imageColumn)
    {

        $changes = [];

        foreach ($data as $key => $value) {
            $originalValue = $entity->getOriginal($key);
            if ($originalValue != $value) {
                $changes[$key] = $value;
            }
        }


        if (request()->hasFile('new_images')) {
            $newImages = [];

            foreach (request()->file('new_images') as $image) {
                $imagePath = 'https://travelersres.com/' . $image->store($imageColumn, 'public');
                $newImages[] = $imagePath;
            }

            $changes[$imageColumn] = array_merge($entity->$imageColumn ?? [], $newImages);
        }

        if (request()->has('remove_images')) {
            $existingImages = $entity->$imageColumn ?? [];
            $changes[$imageColumn] = array_values(array_diff($existingImages, request()->input('remove_images')));
        }

        $pendingUpdates = $entity->pendingUpdates->first(); 

        if ($pendingUpdates) {
            $pendingUpdates->update([
                'changes' => $changes,
                'owner_id' => $entity->owner_id ?? $entity->hotel->owner_id,
            ]);
            $pendingUpdates->save();
        } else {
            $pendingUpdates = $entity->pendingUpdates()->create([
                'changes' => $changes,
                'owner_id' => $entity->owner_id ?? $entity->hotel->owner_id,
            ]);
        }

        return $pendingUpdates;
    }

    public function applyPendingUpdate(PendingUpdates $pendingUpdates)
    {
        $entity = $pendingUpdates->updatable;
        $changes = $pendingUpdates->changes;

        foreach ($changes as $key => $value) {
            $entity->{$key} = $value;
        }
        $entity->save();

        return $entity;
    }
}


