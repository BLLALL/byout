<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\filters\QueryFilter;
use App\Http\filters\UserFilter;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use App\traits\apiResponses;
use Illuminate\Support\Facades\Auth;
use function Laravel\Prompts\error;

class UserController extends Controller
{
    use apiResponses;
    public function index(UserFilter $filter){
        $hotelOwnersWithNoHotels = User::whereHas('owner', function($query) {
            $query->whereDoesntHave('hotel');
        })->pluck('id');
       
        $filteredUsers = User::filter($filter)->whereNotIn('id', $hotelOwnersWithNoHotels)->get();
        return UserResource::collection($filteredUsers);
    }
    public function show(User $user) {
        return new UserResource($user->load(['roles', 'roles.permissions']));
    }

    public function update(UpdateUserRequest $request, User $user)
    {

        if (Auth::user()->id !== $user->id) {
            return $this->error([
                "Your are not authorized to update that resource."
            ], 403);
        }
        $userColumns = ['name', 'email', 'password', 'phone_number', 'preferred_currency', 'age', 'marital_status', 'current_job'];

        $userData = [];
        $profile_image = $request->file('profile_image');
        if ($profile_image) {
            $userData['profile_image'] = 'https://travelersres.com/api/' . $profile_image->store('profile_picture', 'public');
        }
        foreach ($userColumns as $userColumn) {
            if ($request->has($userColumn)) {
                $userData[$userColumn] = $request->input($userColumn);
            }
        }   

        $user->update($userData);

        $user['age'] = (int)$user['age'];

        return new UserResource($user->load(['roles', 'roles.permissions']));
    }
}
