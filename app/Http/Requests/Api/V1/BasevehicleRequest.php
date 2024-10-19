<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Owner;
use Illuminate\Foundation\Http\FormRequest;

class BasevehicleRequest extends FormRequest
{
    public function mappedAttributes(array $otherAttributes = [])
    {
        $attributeMap = array_merge([
            
            'registration_number' => 'registration_number',
            'model' => 'model',
            'vehicle_images' => 'vehicle_images',
            'seats_number' => 'seats_number',
            'has_wifi' => 'has_wifi',
            'has_bathroom' => 'has_bathroom',
            'has_movie_screens' => 'has_movie_screens',
            'has_entrance_camera' => 'has_entrance_camera',
            'has_passenger_camera' => 'has_passenger_camera',
            'has_air_conditioner' => 'has_air_conditioner',
        ], $otherAttributes);

        $attributesToUpdate = [];
        if ($this->isMethod('post')) {
            $userId = $this->input('owner_id');
            if ($userId) {
                $attributesToUpdate['owner_id'] = (Owner::where('user_id', $userId)->first())->id;
            }
        }
        if ($this->hasFile('vehicle_images')) {
            $imagePath = [];
            $vehicle_images = $this->file('vehicle_images');
            foreach ($vehicle_images as $vehicle_image) {
                $imagePath[] = 'https://travelersres.com/' . $vehicle_image->store('vehicle_images', 'public');
            }

        }
        foreach ($attributeMap as $key => $attribute) {
            if ($this->has($key)) {
                if ($attribute === 'vehicle_images') {
                    $attributesToUpdate[$attribute] = $imagePath;
                    continue;
                }
                $attributesToUpdate[$attribute] = $this->input($key);
            }
        }

        return $attributesToUpdate;
    }
}
