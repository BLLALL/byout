<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ToggleHomeFavouriteRequest;
use App\Models\Chalet;
use App\Models\Home;
use App\Models\Hotel;
use App\Models\User;
use App\traits\apiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use  App\Http\Resources\Api\V1\HomeResource;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{

    use apiResponses;
    /**
     * @group Home Favourites
     * Get all user's favourite homes
     */
    public function index(User $user)
    {
        $favourites = $user->favourites()->with('favorable')->get();

        $groupedFavourites = $favourites->groupBy(function ($favourite) {
            return strtolower(class_basename($favourite->favorable_type));
        })->map(function ($group) {
            return $group->map(function ($favourite) {
                return $favourite->favorable;
            });
        });
        if ($groupedFavourites->isEmpty()) {
            return response()->json([
                'data' => [
                    "homes" => [],
                    "hotels" => [],
                ]
            ]);
        }
        return response()->json([
            'data' => $groupedFavourites
        ]);
    }
    /**
     * Toggle homes.
     * @group Home Favourites
     * if the home isn't in user's favourites will be added, if it's in his favourites; then it will be removed.
     */
    public function toggle(ToggleHomeFavouriteRequest $request)
    {
        try {
            if (Auth::user()->id !== $request->input('data.user_id'))
                return $this->error([
                    "You are not authorized to perform this action"
                ], 403);

            $user = User::findOrFail($request->input('data.user_id'));

            $favorable_type = $request->input('data.favorable_type');
            $favorable_id = $request->input('data.favorable_id');
            $favorable_class = $this->getModelClass($favorable_type);
            $favorable = $favorable_class::findOrFail($favorable_id);

            $favorite = $user->favourites()
                ->where('favorable_type', get_class($favorable))
                ->where('favorable_id', $favorable_id)->first();
            if ($favorite) {
                $favorite->delete();
                $action = 'removed from';
            } else {
                $user->favourites()->create([
                    'favorable_id' => $favorable->id,
                    'favorable_type' => get_class($favorable),
                    'user_id' => $user->id,
                ]);
                $action = 'added to';

            }
            return response()->json([
                'message' => $favorable_type . " {$action} favourites",
                'favourites' => $user->favourites()->with('favorable')->get(),
            ]);
        } catch (ModelNotFoundException $e) {
            return $this->error("Couldn't toggle favourite. Model not found.", 404);
        }
    }


    public function getModelClass($type)
    {
        $models = [
            'Home' => Home::class,
            'Hotel' => Hotel::class,
            'Chalet' => Chalet::class
        ];
        return $models[$type] ?? null;
    }
}
