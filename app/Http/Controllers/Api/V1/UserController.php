<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Models\User;

class UserController extends Controller
{

    public function index() {
        return User::all();
    }
    public function show(User $user) {
        return $user;
    }
}
