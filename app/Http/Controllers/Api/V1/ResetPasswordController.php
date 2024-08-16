<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ForgetPasswordRequest;
use App\Http\Requests\Api\V1\ResetPasswordRequest;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use App\traits\apiResponses;
use Ichtrojan\Otp\Otp;
use Mockery\Exception;
use PharIo\Manifest\NoEmailAddressException;
use Illuminate\Support\Facades\Hash;



class ResetPasswordController extends Controller
{
    use apiResponses;

    protected $otp;

    public function __construct() {
        $this->otp = new Otp;
    }
    public function forgetPassword(ForgetPasswordRequest $request)
    {

        $user = User::where('email', $request->input('email'))->firstOrfail();


        try {
            $user->notify(new ResetPasswordNotification());

            return response()->json([
                'verification code sent to your email'
            ]);
        } catch (Exception $e) {
            return $this->error(["we couldn't send verification code to your email"], 443);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {

        $validationResult = (new Otp)->validate($request->input('email'), $request->input('otp'));


        $user = User::where('email', $request->input('email'))->first();

        if(!$validationResult->status) {
            return $this->error(
                'Your verification code is invalid or expired',
                403
            );
        }

        $user->password = Hash::make($request->password);

        $user->save();

        return $this->ok([
            'password has been successfully reset'
        ]);

    }
}
