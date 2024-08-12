<?php

namespace App\Http\Controllers;

use App\Models\Home;
use App\Models\HomeFavourite;
use App\Models\User;
use Illuminate\Http\Request;

class HomeFavouriteController extends Controller
{

    /**
     * @group Home Favourites
     * Get all user's favourite homes
     */
    public function index(User $user)
    {
        return $user->homeFavourite;
    }

    public function store(Home $home)
    {

    }
}
