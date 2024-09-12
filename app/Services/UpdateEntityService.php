<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

abstract class UpdateEntityService
{
    public function updateEntity($entity, Request $request, array $fillableAttributes, string $imageColumn)
    {
        $data = $request->only($fillableAttributes);

        // Update basic attributes
        $entity->fill($data);

        // Handle new images
        if ($request->hasFile(key: 'new_images')) {
            $existingImages = $entity->{$imageColumn} ?? [];
            foreach ($request->file('new_images') as $image) {
                $imagePath = 'https://fayroz97.com/real-estate/' . $image->store($imageColumn, 'public');
                $existingImages[] = $imagePath;
            }
            $entity->{$imageColumn} = $existingImages;
        }

        // Handle image removal
        if ($request->has('remove_images')) {
            $existingImages = $entity->{$imageColumn} ?? [];

            $existingImages = array_diff($existingImages, $request->remove_images);

            foreach ($request->remove_images as $imageToRemove) {
                Storage::disk('public')->delete($imageToRemove);
            }

            $entity->{$imageColumn} = array_values($existingImages);
        }

        $entity->save();
    }
}
