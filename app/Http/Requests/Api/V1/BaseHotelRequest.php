<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseHotelRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = [])
    {
        $attributeMap = array_merge([
            'data.name' => 'name',
            'data.location' => 'location',
            'data.hotel_images' => 'hotel_images',
            'data.wifi' => 'wifi',
            'data.coordinates' => 'coordinates',
            'data.hotel_rooms' => 'hotel_rooms',
            'data.user_id' => 'user_id'
        ], $otherAttributes);

        $attributesToUpdate = [];
        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }

        return $attributesToUpdate;
    }

}
