<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use App\traits\apiResponses;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ApiResponses;


    public function register(RegisterUserRequest $request)
    {

        $validatedData = $request->validated();

        if (!$validatedData) {
            return response()->json('Invalid credentials');
        }

        $user = User::create($request->all());

        $user->notify(new EmailVerificationNotification());
        $user['token'] = $user->createToken(
            'API token for ' . $user->email,
            ['*'],
            now()->addHours(2))->plainTextToken;

        return response()->json(
            [

                'user' => $user
            ]

        );

    }

    public function google()
    {
        //send the user's request to google
        return Socialite::driver('google')->redirect();
    }

    public function googleRedirect()
    {
        // get oauth request back from google to authenticate user
        try {

            $user = Socialite::driver('google')->user();

            $user = User::Create([
                'email' => $user->email
            ], [
                    'name' => $user->name,
                    'password' => Hash::make(Str::random(24))
                ]
            );


            Auth::login($user, true);

            return response()->json([
                'Authenticated.'
            ]);
        } catch (AuthenticationException $e) {
            return response()->json([
                'Failed Authentication'
            ]);
        }
    }

    /**
     * Login
     *
     * Authenticates the user and returns the user's API token.
     *
     * @unauthenticated
     * @group Authentication
     * @response 200 {
     * "data": {
     * "token": "{YOUR_AUTH_KEY}"
     * },
     * "message": "Authenticated",
     * "status": 200
     * }
     */
    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        $user['token'] = $user->createToken(
            'API token for ' . $user->email,
            ['*'],
            now()->addMonth())->plainTextToken;


        $roleNames = DB::table('roles')
            ->join('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', 'App\Models\User')
            ->pluck('roles.name');


        $user['role'] = $roleNames[0];

        return response()->json(
            [
                'user' => $user
            ]

        );

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('');
    }
}