<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BaseTourRequest extends FormRequest
{


    public function mappedAttributes(array $otherAttributes = []) {
        $attributeMap = array_merge([
            'data.attributes.price' => 'price',
            'data.attributes.source' => 'source',
            'data.attributes.destination' => 'destination',
            'data.attributes.departure_time' => 'departure_time',
            'data.attributes.arrival_time' => 'arrival_time',
            'data.attributes.tour_company_id' => 'tour_company_id',
            'data.attributes.seat_position' => 'seat_position',
            'data.attributes.traveller_gender' => 'traveller_gender',
            
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
