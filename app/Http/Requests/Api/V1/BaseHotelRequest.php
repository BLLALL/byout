<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Owner;
use Illuminate\Foundation\Http\FormRequest;

class BaseHotelRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = [])
    {
        $attributeMap = array_merge([
            'name' => 'name',
            'location' => 'location',
            'hotel_images' => 'hotel_images',
            'wifi' => 'wifi',
            'hotel_rooms' => 'hotel_rooms',
        ], $otherAttributes);

        $attributesToUpdate = [];
        $userId = $this->input('owner_id');

        if ($userId) {
            $attributesToUpdate['owner_id'] = (Owner::where('user_id', $userId)->first())->id;
        }
        if ($this->hasFile('hotel_images')) {
            $imagePath = [];
            $hotel_images = $this->file('hotel_images');
            foreach ($hotel_images as $hotel_image) {
                $imagePath[] = 'https://fayroz97.com/real-estate/' . $hotel_image->store('hotel_images', 'public');
            }
        }

        $coords = [];
        $coords[0] = (double)$this->input('coordinates.0');
        $coords[1] = (double)$this->input('coordinates.1');

        $attributesToUpdate['coordinates'] = $coords;

        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                if ($attribute === 'hotel_images') {
                    $attributesToUpdate[$attribute] = $imagePath;
                    continue;
                }
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }

        return $attributesToUpdate;
    }

}
