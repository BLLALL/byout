<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\V1\homeOwnerResource;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function homeOwners()
    {
        $homeOwners = (User::role('Home Owner')->with(['owner', 'roles'])->get()->map(function ($user) {
            return $user->owner;
        })->filter());
        return homeOwnerResource::collection($homeOwners);
    }
}
