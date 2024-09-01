<?php

namespace App\Services;


use App\Models\Home;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CreateHomeService
{
    public function createHome(Request $request)
    {

        $userId = (integer)$request->input('owner_id');
        $ownerId = (Owner::where('user_id', $userId)->first())->id;


        $images = [];
        if ($request->hasFile('home_images')) {
            foreach ($request->file('home_images') as $homeImage) {
                $imagePath = 'https://fayroz97.com/real-estate/' . $homeImage->store('home_images', 'public');
                $images[] = $imagePath;
            }
        }

        $home_details = array_merge([
            $request->only([
                'title', 'description', 'price',
                'area', 'bathrooms_no', 'bedrooms_no', 'location',
                'wifi', 'coordinates', 'rent_period'
            ]),
            'owner_id' => $ownerId,
            'home_images' => $images,
        ]);

        Log::info('Home Details: ' . json_encode($home_details));
        $home = Home::create(

        );


        if ($request->has('documents')) {
            foreach ($request->file('documents') as $document) {
                $filePath = 'https://fayroz97.com/real-estate/' . $document->store('documents/home', 'public');
                $home->documents()->create([
                    'document_type' => $document->type,
                    'file_path' => $filePath,
                ]);
            }
        }

        $home->save();

        return $home;
    }
}
