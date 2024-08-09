<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseHomeRequest extends FormRequest
{

    public function mappedAttributes(array $otherAttributes = []) {
        $attributeMap = array_merge([
            'data.attributes.title' => 'title',
            'data.attributes.description' => 'description',
            'data.attributes.price' => 'price',
            'data.attributes.area' => 'area',
            'data.attributes.bathrooms_no' => 'bathrooms_no',
            'data.attributes.bedrooms_no' => 'bedrooms_no',
            'data.attributes.location' => 'location',
            'data.attributes.user_id' => 'user_id'
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
