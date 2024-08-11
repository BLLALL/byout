<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\EmailVerificationRequest;
use App\Models\User;
use App\traits\apiResponses;
use Ichtrojan\Otp\Otp;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use function Laravel\Prompts\error;

class EmailVerificationController extends Controller
{
    use apiResponses;
    public function email_verification(EmailVerificationRequest $request) {
        $user = User::where('email', $request->input('email'))->firstOrfail();
        if($user->hasVerifiedEmail()) {
            return $this->error(['Email already verified'], 422);
        }

        $otp = new Otp;
        $validationResult = $otp->validate($user->email, $request->input('otp'));

        if(!$validationResult->status) {
            return $this->error(
                'Your verification code is incorrect',
                403
            );
        }
        try {
            $user->markEmailAsVerified();
            $user->update(['email_verified_at' => now()]);
            $user->save();

            return response()->json([
                'Your account has been verified successfully.',
                'user' => $user
            ]);
        } catch (\Exception $e){
            report($e);
            return $this->error("Couldn't Authentcate your account.", 403);
        }

    }
}
