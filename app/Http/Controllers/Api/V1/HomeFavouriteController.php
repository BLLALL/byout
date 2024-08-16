<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ToggleHomeFavouriteRequest;
use App\Models\Home;
use App\Models\User;
use App\traits\apiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use  App\Http\Resources\Api\V1\HomeResource;

class HomeFavouriteController extends Controller
{

    use apiResponses;
    /**
     * @group Home Favourites
     * Get all user's favourite homes
     */
    public function index(User $user)
    {
        return HomeResource::collection($user->favouriteHome[0]);
        // return new $user->favouriteHome;
    }

    /**
     * Toggle homes.
     * @group Home Favourites
     * if the home isn't in user's favourites will be added, if it's in his favourites; then it will be removed.
     */
    public function toggle(ToggleHomeFavouriteRequest $request)
    {
        $user_id = $request->input('data.user_id');
        try {
            $user = User::find($user_id);

            $user->favouriteHome()->toggle($request->input('data.home_id'));

            return $user->favouriteHome;
        } catch (ModelNotFoundException $e) {
            return $this->error([
                "Couldn't add home to favourites"
            ], 403);
        }
    }
}
