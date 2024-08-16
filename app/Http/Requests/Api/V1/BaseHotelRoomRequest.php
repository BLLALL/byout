<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseHotelRoomRequest extends FormRequest
{


    public function mappedAttributes(array $otherAttributes = [])
    {
        $attributeMap = array_merge([
            'data.price' => 'price',
            'data.area' => 'area',
            'data.is_reserved' => 'is_reserved',
            'data.bathrooms_no' => 'bathrooms_no',
            'data.bedrooms_no' => 'bedrooms_no',
            'data.hotel_id' => 'hotel_id',
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
