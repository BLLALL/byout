<?php

namespace App\Services;

use App\Models\Home;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UpdateHomeService extends UpdateEntityService
{
    protected PendingUpdateService $pendingUpdateService;
    public function __construct(PendingUpdateService $pendingUpdateService)
    {
        $this->pendingUpdateService = $pendingUpdateService;
    }
    public function updateHome(Home $home, Request $request)
    {
        $fillableAttributes = [
            'title',
            'description',
            'price',
            'area',
            'bathrooms_no',
            'bedrooms_no',
            'location',
            'wifi',
            'coordinates',
            'rent_period',
            'living_room_no',
            'kitchen_no',
        ];

       return  $this->pendingUpdateService->createPendingUpdate($home, $request, $fillableAttributes, 'home_images');
    }
}
