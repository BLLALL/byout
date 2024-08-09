<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ApiResponses;

    public function register(RegisterUserRequest $request) {

        Log::info('Raw request data:', $request->all());
        $validatedData = $request->validated();

        $attributes =  $validatedData['data']['attributes'];
            $user = User::create([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => $attributes['password'],
            ]);
            return $this->ok(
                'Registered',
                [
                    'token' => $user->createToken(
                        'API token for ' . $user->email,
                        ['*'],
                        now()->addHours(2))->plainTextToken
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
        $user = Socialite::driver('google')->user();

        dd($user);
    }
    public function login(LoginUserRequest $request)
    {
        $request->validated($request->all());

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->ok(
            'Authenticated',
            [
                'token' => $user->createToken(
                    'API token for ' . $user->email,
                    ['*'],
                    now()->addMonth())->plainTextToken
            ]
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('');
    }
}
