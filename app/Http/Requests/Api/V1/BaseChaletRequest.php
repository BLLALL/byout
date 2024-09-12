<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Owner;
use Illuminate\Foundation\Http\FormRequest;

class BaseChaletRequest extends FormRequest
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
            'chalet_images' => 'chalet_images',
            'wifi' => 'wifi',
            'coordinates' => 'coordinates',
            'rent_period' => 'rent_period',
            'air_conditioning' => 'air_conditioning',
            'sea_view' => 'sea_view',
            'distance_to_beach' => 'distance_to_beach',
            'available_until' => 'available_until',
            'max_occupancy' => 'max_occupancy',
        ], $otherAttributes);

        $attributesToUpdate = [];

        $userId = $this->input('owner_id');

        if($userId) {
            $attributesToUpdate['owner_id'] = (Owner::where('user_id', $userId)->first())?->id;

        }

        if ($this->hasFile('chalet_images')) {
            $imagePath = [];
            $chalet_images = $this->file('chalet_images');
            foreach ($chalet_images as $chalet_image) {
                $imagePath[] = 'https://fayroz97.com/real-estate/' . $chalet_image->store('chalet_images', 'public');
            }

        }

        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                if($attribute === 'chalet_images') {
                    $attributesToUpdate[$attribute] = $imagePath;
                    continue;
                }
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }

        return $attributesToUpdate;
    }
}
