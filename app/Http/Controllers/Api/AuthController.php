<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\RegisterOwnerRequest;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Http\Resources\Api\V1\OwnerResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Owner;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use App\traits\apiResponses;
use http\Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    use ApiResponses;


    /**
     *
     * User Registration
     *
     * Create an account for the user.
     * @group Authentication
     */
    public function register(RegisterUserRequest $request)
    {

        $validatedData = $request->validated();

        if (!$validatedData) {
            return response()->json('Invalid credentials');
        }
        
        $userData = $request->only([
            "name", "email", "password", "phone_number", "age",
            "marital_status", "current_job"
        ]);

        $user = User::create($userData);

        // $user->notify(new EmailVerificationNotification());
        $user['token'] = $user->createToken(
            'API token for ' . $user->email,
            ['*'],
            now()->addDays(30))->plainTextToken;


        return response()->json(
            [
                'user' => $user
            ]
        );

    }

    /**
     *
     * Owner Registration
     *
     * Create an account for an owner.
     * @group Authentication
     */
    public function registerOwner(RegisterOwnerRequest $request)
    {
        DB::beginTransaction();

        try {
            $userData = $request->only(['name', 'email', 'password', 'phone_number']);

            $user = User::create($userData);

            $ownerData = $request->only(['organization', 'identification_card', 'licensing', 'affiliation_certificate', 'commercial_register']);

            $filePaths = [];
            foreach (['identification_card', 'licensing', 'affiliation_certificate', 'commercial_register'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $filePaths[$fileField] = 'https://travelersres.com/api/' . $request->file($fileField)->store('owner_documents', 'public');
                }
            }

            $ownerData = array_merge($ownerData, $filePaths, ['status'=>'pending']);
            $ownerData['user_id'] = $user->id;
            $owner = Owner::create($ownerData);

            // $user->notify(new EmailVerificationNotification());
            $token = $user->createToken(
                'API token for ' . $user->email,
                ['*'],
                now()->addDays(30))->plainTextToken;

            $role = $request->input('role');
            $user->assignRole($role);
            $owner['role'] = $owner->user->role = $request->input('role');
            $owner['token'] = $token;
            DB::commit();

            return new OwnerResource($owner);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json($e, 500);
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

        $user = User::firstWhere('email', $request->email);

        // Check if the user exists and the password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials', 401);
        }

        $token = $user->createToken(
            'API token for ' . $user->email,
            ['*'],
            now()->addDays(30)
        )->plainTextToken;

        if (!empty($token)) {
            $user['token'] = $token;
        }

        return new UserResource($user);
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

            $user = User::firstOrCreate([
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
     * Logout
     *
     * Logs out the user and deletes the API token.
     *
     * @authenticated
     * @group Authentication
     * @response 200 {
     * "message": "Logged out successfully"
     * }
     */
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        $request->user()->currentAccessToken()->delete();

        return $this->success('Logged out successfully');
    }

    /**
     * Check Token
     *
     * Checks if the user's token is still valid.
     *
     * @authenticated
     * @group Authentication
     * @response 200 {
     * "message": "Authenticated"
     * }
     */
    public function checkToken(Request $request)
    {
        if (!$request->user()) return $this->error('Unauthenticated', 401);
        $token = $request->user()->currentAccessToken();
        if ($token->expires_at->isPast()) {
            return $this->error('Unauthenticated', 401);
        } else {
            return $this->success('Authenticated');
        }
    }

    public function refreshToken(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return $user->createToken(
            'API token for ' . $user->email,
            ['*'],
            now()->addDays(30))->plainTextToken;
    }

}

