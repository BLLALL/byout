<?php

namespace App\Services;

use App\Models\Home;
use Illuminate\Http\Request;
use App\traits\HandlesDiscount;
use Illuminate\Support\Facades\Storage;

class UpdateHomeService extends UpdateEntityService
{
    use HandlesDiscount;
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
            'discount_price',
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
        $data = $request->only($fillableAttributes);
        $this->handlePriceUpdate($home, $data);
        $this->setCurrency($data, $home->owner);

        return  $this->pendingUpdateService->createPendingUpdate($home, $data, 'home_images');
    }
}
