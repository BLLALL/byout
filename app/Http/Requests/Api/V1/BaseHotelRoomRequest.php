<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class BaseHotelRoomRequest extends FormRequest
{


    public function mappedAttributes(array $otherAttributes = [])
    {
        $attributeMap = array_merge([
            'title' => 'title',
            'price' => 'price',
            'area' => 'area',
            'is_reserved' => 'is_reserved',
            'bathrooms_no' => 'bathrooms_no',
            'bedrooms_no' => 'bedrooms_no',
            'room_images' => 'room_images',
            'hotel_id' => 'hotel_id',
            'available_from' => 'available_from',
            'available_until' => 'available_until',
        ], $otherAttributes);

        $attributesToUpdate = [];

        if ($this->hasFile('room_images')) {
            $imagePath = [];
            $room_images = $this->file('room_images');
            foreach ($room_images as $room_image) {
                $imagePath[] = 'https://fayroz97.com/real-estate/' . $room_image->store('room_images', 'public');
            }
        }

        foreach ($attributeMap as $key => $attribute) {
            if (array_key_exists($key, $this->all())) {
                $value = $this->input($key);

                // Type casting based on expected types
                switch ($key) {
                    case 'price':
                    case 'area':
                        $value = (float) $value;
                        break;
                    case 'is_reserved':
                        $value = (bool) $value;
                        break;
                    case 'bathrooms_no':
                    case 'bedrooms_no':
                        $value = (int) $value;
                        break;
                }

                if ($attribute === 'room_images' && isset($imagePath)) {
                    $attributesToUpdate[$attribute] = $imagePath;
                } else {
                    $attributesToUpdate[$attribute] = $value;
                }
            }
        }

        return $attributesToUpdate;
    }
}
