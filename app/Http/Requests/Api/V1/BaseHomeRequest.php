<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Owner;
use Illuminate\Foundation\Http\FormRequest;

class BaseHomeRequest extends FormRequest
{

    public function mappedAttributes(array $otherAttributes = []) {
        $attributeMap = array_merge([
            'title' => 'title',
            'description' => 'description',
            'price' => 'price',
            'area' => 'area',
            'bathrooms_no' => 'bathrooms_no',
            'bedrooms_no' => 'bedrooms_no',
            'location' => 'location',
            'home_images' => 'home_images',
            'wifi' => 'wifi',
            'coordinates' => 'coordinates',
            'rent_period' => 'rent_period',
        ], $otherAttributes);

        $attributesToUpdate = [];

        $userId = $this->input('owner_id');

        if ($userId) {
            $attributesToUpdate['owner_id'] = (Owner::where('user_id', $userId)->first())->id;
        }

        if ($this->hasFile('home_images')) {
            $imagePath = [];
            $home_images = $this->file('home_images');
            foreach ($home_images as $home_image) {
                $imagePath[] = 'https://fayroz97.com/real-estate/' . $home_image->store('home_images', 'public');
            }

        }

        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                if ($attribute === 'home_images') {
                    $attributesToUpdate[$attribute] = $imagePath;
                    continue;
                }
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }

        return $attributesToUpdate;
    }
}
