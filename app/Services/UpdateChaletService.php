<?php

namespace App\Services;

use App\Models\Chalet;
use Illuminate\Http\Request;

class UpdateChaletService extends UpdateEntityService
{
    protected PendingUpdateService $pendingUpdateService;

    public function __construct(PendingUpdateService $pendingUpdateService)
    {
        $this->pendingUpdateService = $pendingUpdateService;
    }

    public function updateChalet(Chalet $chalet, Request $request)
    {
        $fillableAttributes = [
            'title', 'price', 'area',
            'location', 'wifi', 'coordinates',
            'air_conditioning', 'sea_view', 'distance_to_beach',
            'max_occupancy', 'rent_period', 'bathrooms_no', 
            'bedrooms_no', 'kitchen_no', 'living_room_no',
            'description', 'is_reserved', 'available_from', 'available_until', 'is_available'
        ];

        return $this->pendingUpdateService->createPendingUpdate($chalet, $request, $fillableAttributes, 'chalet_images');
    }
}
