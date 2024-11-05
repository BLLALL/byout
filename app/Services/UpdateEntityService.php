<?php

namespace App\Services;

use Brick\Money\Money;
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
                $imagePath = 'https://travelersres.com/' . $image->store($imageColumn, 'public');
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

        $this->setCurrency($entity,  $entity->owner);
        $entity->save();
    }

    public function setCurrency(array &$data, $owner)
    {
        if (isset($data['price'])) {
            $money = Money::of($data['price'], $owner->user->preferred_currency);
            $data['price'] = $money->getMinorAmount()->toInt();
            $data['currency'] = $owner->user->preferred_currency;
        }


        if (isset($data['discount_price'])) {
            $money = Money::of($data['discount_price'], $owner->user->preferred_currency);
            $data['discount_price'] = $money->getMinorAmount()->toInt();
            $data['currency'] = $owner->user->preferred_currency;
        }
    }
}
