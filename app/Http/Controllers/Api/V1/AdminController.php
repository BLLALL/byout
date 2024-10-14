<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ChaletResource;
use App\Http\Resources\Api\V1\HomeResource;
use App\Http\Resources\Api\V1\HotelResource;
use App\Http\Resources\Api\V1\OwnerResource;
use App\Models\Chalet;
use App\Models\Driver;
use App\Models\Home;
use App\Models\Hotel;
use App\Models\Owner;
use App\Models\Rental;
use App\Models\Tour;
use App\Models\TourReservation;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\OwnerDetailsService;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

    protected OwnerDetailsService $ownerDetailsService;

    public function __construct(OwnerDetailsService $ownerDetailsService)
    {
        $this->ownerDetailsService = $ownerDetailsService;
    }
    public function approveOwner(User $user)
    {
        $owner = Owner::where('user_id', $user->id)->firstOrFail();
        $owner->update(['status' => 'approved']);
        if($owner->hotel)
            {   $hotel = $owner->hotel;
                $hotel[0]->update(["pending" => false]);
                dd($hotel);
            }
        return new OwnerResource($owner);
    }

    public function rejectOwner(User $user)
    {
        $owner = Owner::where('user_id', $user->id)->firstOrFail();
        $owner->update(['status' => 'rejected']);
        return response()->json(['message' => 'Owner rejected successfully']);
    }

    public function approveHome(Home $home)
    {
        $home->update(['pending' => false]);
        return new HomeResource($home);
    }

    public function approveHotel(Hotel $hotel)
    {
        $hotel->update(['pending' => false]);
        return new HotelResource($hotel);
    }

    public function approveChalet(Chalet $chalet)
    {
        $chalet->update(['pending' => false]);
        return new ChaletResource($chalet);
    }

    public function rejectHome(Home $home)
    {
        try {
            $home->forceDelete();
            return response()->json([
                "message" => "Home deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => 'Failed to delete the home. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function rejectHotel(Hotel $hotel)
    {
        try {
            $hotel->forceDelete();
            return response()->json([
                "message" => "Hotel deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => 'Failed to delete the hotel. ' . $e->getMessage(),
            ], 500);
        }
    }
    public function rejectChalet(Chalet $chalet)
    {
        try {
            $chalet->forceDelete();
            return response()->json([
                "message" => "Chalet deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "message" => 'Failed to delete the chalet. ' . $e->getMessage(),
            ], 500);
        }
    }


    public function appData()
    {
        $owners = Owner::with('user')->get();
        $homeOwnersCount = Owner::whereRelation('user.roles', 'name', 'Home Owner')->count();
        $hotelOwnersCount = Owner::whereRelation('user.roles', 'name', 'Hotel Owner')->count();
        $chaletOwnersCount = Owner::whereRelation('user.roles', 'name', 'Chalet Owner')->count();
        $tourCompanyOwnersCount = Owner::whereRelation('user.roles', 'name', 'Tour Company Owner')->count();
        $homes = Home::all();
        $hotels = Hotel::all();
        $chalets = Chalet::all();
        $tours = Tour::all();

        $rentedHomes = Rental::where('rentable_type', 'App\Models\Home')
            ->where('check_in', '<=', now())
            ->where('check_out', '>=', now())
            ->count();

        $rentedHotelRooms = Rental::where('rentable_type', 'App\Models\HotelRooms')
            ->where('check_in', '<=', now())
            ->where('check_out', '>=', now())
            ->count();

        $rentedChalets = Rental::where('rentable_type', 'App\Models\Chalet')
            ->where('check_in', '<=', now())
            ->where('check_out', '>=', now())
            ->count();


        return [
            'usersCount' => User::count(),
            'busCount' => Vehicle::where('type', 'bus')->count(),
            'vanCount' => Vehicle::where('type', 'van')->count(),
            'carCount' => Vehicle::where('type', 'car')->count(),
            'driverCount' => Driver::count(),
            'completedTours' => Tour::where('status', 'completed')->count(),
            'inProgressTours' => Tour::where('status', 'in_progress')->count(),
            'scheduledTours' => Tour::where('status', 'scheduled')->count(),
            'ownersCount' => $owners->count(),
            'homeOwnersCount' => $homeOwnersCount,
            'hotelOwnersCount' => $hotelOwnersCount,
            'chaletOwnersCount' => $chaletOwnersCount,
            'tourCompanyOwnersCount' => $tourCompanyOwnersCount,
            'pendingOwners' => $ownersStatusCount['pending'] ?? 0,
            'approvedOwners' => $ownersStatusCount['approved'] ?? 0,
            'homesCount' => $homes->count(),
            'pendingHomes' => $homes->where('pending', true)->count(),
            'approvedHomes' => $homes->where('pending', false)->count(),
            'hotelsCount' => $hotels->count(),
            'pendingHotels' => $hotels->where('pending', true)->count(),
            'approvedHotels' => $hotels->where('pending', false)->count(),
            'chaletsCount' => $chalets->count(),
            'pendingChalets' => $chalets->where('pending', true)->count(),
            'approvedChalets' => $chalets->where('pending', false)->count(),
            'toursCount' => $tours->count(),
            'homesRentedAtThisMoment' => $rentedHomes,
            'hotelRoomsRentedAtThisMoment' => $rentedHotelRooms,
            'chaletsRentedAtThisMoment' => $rentedChalets,
        ];
    }

    public function getOwner($userId)
    {
        $user = User::findOrFail($userId);
        $owner = Owner::where('user_id', $userId)->firstOrFail();

        $ownerDetails = [
            'id' => $owner->id,
            'name' => $user->name,
            'email' => $user->email,
            'preferred_currency' => $user->preferred_currency,
            'organization' => $owner->organization,
            'identification_card' => $owner->identification_card,
            'licensing' => $owner->licensing,
            'affiliation_certificate' => $owner->affiliation_certificate,
            'commercial_register' => $owner->commercial_register,
            'transportation_company' => $owner->transportation_company,
            'status' => $owner->status,
            'created_at' => $owner->created_at,
            'updated_at' => $owner->updated_at,
        ];

        $roleMethod = $this->getRoleMethod($user);

        if (method_exists($this->ownerDetailsService, $roleMethod)) {
            $additionalDetails = $this->ownerDetailsService->$roleMethod($owner);
            $ownerDetails = array_merge($ownerDetails, $additionalDetails);
        }

        return response()->json($ownerDetails);
    }

    private function getRoleMethod(User $user): string
    {
        $roleMethods = [
            'Tour Company Owner' => 'getTourCompanyOwnerDetails',
            'Home Owner' => 'getHomeOwnerDetails',
            'Hotel Owner' => 'getHotelOwnerDetails',
            'Chalet Owner' => 'getChaletOwnerDetails',
        ];

        foreach ($roleMethods as $role => $method) {
            if ($user->hasRole($role)) {
                return $method;
            }
        }

        throw new \InvalidArgumentException('User does not have a valid owner role');
    }
}
