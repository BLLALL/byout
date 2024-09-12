<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\filters\VehicleFilter;
use App\Http\Requests\Api\V1\StoreVehicleRequest;
use App\Http\Requests\Api\V1\UpdateVehicleRequest;
use App\Http\Resources\Api\V1\VehicleResource;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\UpdateVehicleService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    protected UpdateVehicleService $updateVehicleService;

    public function __construct(UpdateVehicleService $updateVehicleService)
    {
        $this->updateVehicleService = $updateVehicleService;
    }

    public function index(VehicleFilter $filter)
    {
        $ownerId = request('owner_id');

        if ($ownerId) {
            $user = User::findOrFail($ownerId);

            if (!$user->hasRole('Tour Company Owner') || $user->id != Auth::user()->id) {
                return response()->json([
                    'message' => 'Unauthorized access'
                ], 403);
            }
        }

        return VehicleResource::collection(Vehicle::filter($filter)->get());
    }

    public function show(Vehicle $vehicle)
    {
        return new VehicleResource($vehicle);
    }


    public function store(StoreVehicleRequest $request)
    {
        $ownerId = $request->input('owner_id');
        $authUserId = Auth::user()->id;
        if (Auth::user()->hasRole('Tour Company Owner') && $ownerId == $authUserId) {
            $validatedData =  $this->handlingFiles($request);

            $vehicle = Vehicle::create($validatedData);

            return new VehicleResource($vehicle);
        } else {
            return response()->json([
                "message" => "You're not authorized to store this resource"
            ], 403);
        }
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        if (Auth::user()->hasRole("Tour Company Owner")) {
            $this->updateVehicleService->updateVehicle($vehicle, $request);
            return new VehicleResource($vehicle);
        } else {
            return response()->json([
                "message" => "You're not authorized to update this resource"
            ], 403);
        }
    }

    public function destroy(Vehicle $vehicle)
    {
        $user = Auth::user();
        if ($user->hasRole("Tour Company Owner") && $vehicle->owner->user_id == $user->id) {
            try {
                $vehicle->delete();
                return response()->json([
                    "message" => "Vehicle deleted successfully"
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    "message" => 'Failed to delete the vehicle. ' . $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                "message" => 'You are not authorized to delete the vehicle.',
            ], 403);
        }
    }

    public function getAvailableVehicles()
    {
        return VehicleResource::collection(Vehicle::where('status', 'available')->get());
    }

    public function getInUseVehicles()
    {
        return VehicleResource::collection(Vehicle::where('status', 'unavailable')->get());
    }

    /**
     * @param UpdateVehicleRequest $request
     * @return void
     */
    public function handlingFiles(Request $request) : mixed
    {
        $validatedData = $request->validated();
        if ($request->hasFile('vehicle_images')) {
            $imageUrls = [];
            foreach ($request->file('vehicle_images') as $image) {
                $path = $image->store('vehicle_images', 'public');
                $imageUrls[] = 'https://fayroz97.com/real-estate/' . $path;
            }
            $validatedData['vehicle_images'] = $imageUrls;
        }
        return $validatedData;
    }
}
