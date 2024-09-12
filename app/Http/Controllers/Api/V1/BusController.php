<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\filters\BusFilter;
use App\Http\Requests\Api\V1\storeBusRequest;
use App\Http\Requests\Api\V1\UpdateBusRequest;
use App\Http\Resources\Api\V1\BusResource;
use App\Models\Bus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BusController extends Controller
{
    public function index(BusFilter $filter)
    {
        $user = Auth::user();
        $ownerId = request('owner_id');
        if ($ownerId) {
            if (!$user->hasRole('Tour Company Owner')) {
                return response()->json([
                    'unauthorized access'
                ], 403);
            }
        }
        if (!($user->id == $ownerId))
            return response()->json(['error' => 'You do not have permission to access this owner\'s buses'], 403);

        return BusResource::collection(Bus::filter($filter)->get());
    }

    public function show(Bus $bus)
    {

        return new BusResource($bus);
    }

    public function store(storeBusRequest $request)
    {
        if (Auth::user()->hasRole('Tour Company Owner')) {
            return new BusResource(Bus::create($request->mappedAttributes()));
        } else {
            return response()->json([
                "You're not authorized to store this resource"
            ]);
        }
    }

    public function update(UpdateBusRequest $request, Bus $bus)
    {
        if (Auth::user()->hasRole("Tour Company Owner")) {
            $bus->update($request->mappedAttributes());
            return new BusResource($bus);
        } else {
            return response()->json([
                "You're not authorized to store this resource"
            ]);
        }
    }

    public function destroy(Bus $bus)
    {
        if (Auth::user()->hasRole("Tour Company Owner")) {
            try {
                $bus->delete();
                return response()->json([
                    "message" => "Bus deleted successfully"
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    "message" => 'Failed to delete the bus. ' . $e->getMessage(),
                ], 500);
            }
        }
        else {
            return response()->json([
                "message" => 'You are not authorized to delete the bus. ',
            ], 500);
        }
    }

    public function getAvailableBuses()
    {
        return BusResource::collection(Bus::where('status', 'available')->get());
    }

    public function getInUseBuses()
    {
        return BusResource::collection(Bus::where('status', 'in use')->get());
    }

}
