<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseHomeRequest extends FormRequest
{

    public function mappedAttributes(array $otherAttributes = []) {
        $attributeMap = array_merge([
            'data.title' => 'title',
            'data.description' => 'description',
            'data.price' => 'price',
            'data.area' => 'area',
            'data.bathrooms_no' => 'bathrooms_no',
            'data.bedrooms_no' => 'bedrooms_no',
            'data.location' => 'location',
            'data.home_images' => 'home_images',
            'data.wifi' => 'wifi',
            'data.coordinates' => 'coordinates',
            'data.rent_period' => 'rent_period',
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
