<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\filters\DriverFilter;
use App\Http\Requests\Api\V1\StoreDriverRequest;
use App\Http\Requests\Api\V1\UpdateDriverRequest;
use App\Http\Resources\Api\V1\DriverResource;
use App\Models\Driver;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    public function index(DriverFilter $filter)
    {
        return DriverResource::collection(Driver::filter($filter)->get());
    }

    public function show(Driver $driver)
    {
        return new DriverResource($driver);
    }

    public function store(StoreDriverRequest $request)
    {
        if (Auth::user()->hasRole('Tour Company Owner')) {
            $driver = $request->createUserAndDriver();
            return new DriverResource($driver);
        } else {
            return response()->json([
                "You're not authorized to store that driver."
            ]);
        }
    }

    public function update(UpdateDriverRequest $request, Driver $driver)
    {
        if (!Auth::user()->hasRole('Tour Company Owner')) {
            return response()->json([
                "You're not authorized to store that driver."
            ]);
        }


        $request->updateUserAndDriver();
        return new DriverResource($driver);
    }

    public function destroy(Driver $driver)
    {
        if (Auth::user()->hasRole("Tour Company Owner") && $driver->owner->user_id == Auth::user()->id) {
            try {
                $driver->delete();
                return response()->json([
                    "message" => "Driver deleted successfully"
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    "message" => 'Failed to delete the driver. ' . $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                "message" => 'You are not authorized to delete the driver. ',
            ], 500);
        }
    }
}
