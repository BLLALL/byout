<?php

namespace App\Services;

use App\Models\Chalet;
use Illuminate\Http\Request;
use App\traits\HandlesDiscount;
use Log;

class UpdateChaletService extends UpdateEntityService
{
    use HandlesDiscount;
    protected PendingUpdateService $pendingUpdateService;

    public function __construct(PendingUpdateService $pendingUpdateService)
    {
        $this->pendingUpdateService = $pendingUpdateService;
    }

    public function updateChalet(Chalet $chalet, Request $request)
    {
        $fillableAttributes = [
            'title', 'price','discount_price', 'area',
            'location', 'wifi', 'coordinates',
            'air_conditioning', 'sea_view', 'distance_to_beach',
            'max_occupancy', 'rent_period', 'bathrooms_no', 
            'bedrooms_no', 'kitchen_no', 'living_room_no',
            'description', 'is_reserved', 'available_from', 'available_until', 'is_available'
        ];

        $data = $request->only($fillableAttributes);
        $this->handlePriceUpdate($chalet, $data);  
        $this->setCurrency($data, $chalet->owner);

        return $this->pendingUpdateService->createPendingUpdate($chalet, $data, 'chalet_images');
    }
}
