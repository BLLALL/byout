<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Owner;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class BaseDriverRequest extends FormRequest
{
    // protected function passedValidation()
    // {
    //     if ($this->isMethod('post')) {
    //         $this->createUserAndDriver();
    //     } elseif ($this->isMethod('patch')) {
    //         $this->updateUserAndDriver();
    //     }
    // }

    public function createUserAndDriver()
    {
        $user = User::create([
            'name' => $this->name,
            'phone_number' => $this->phone_number,
        ]);

        if ($this->hasFile('profile_image')) {
            $imagePath = $this->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = 'https://travelersres.com/' .  $imagePath;   
            $user->save();
        }


        if ($this->hasFile('license')) {
            $licensePath = $this->file('license')->store('licenses', 'public');
        } else {
            return response()->json([
                "Your have to provide driver's license."
            ], 500);
        }

        $ownerId = Owner::where('user_id', Auth::user()->id)->first()->id;

        $driver = Driver::create([
            'license_expiry_date' => $this->license_expiry_date,
            'is_smoker' => $this->is_smoker,
            'user_id' => $user->id,
            'license' => 'https://travelersres.com/' . $licensePath,
            'owner_id' => $ownerId,
        ]);
        $driver->save();
        
        return $driver;
    }

    public function updateUserAndDriver()
    {
        $driver = ($this->route('driver'));
        $user = $driver->user;



        $user->update([
            'name' => $this->name ?? $user->name,
            'email' => $this->email ?? $user->email,
            'phone_number' => $this->phone_number ?? $user->phone_number,
            'password' => $this->password ?? $user->password,
        ]);
        if ($this->hasFile('profile_image')) {
            $imagePath = $this->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = 'https://travelersres.com/' . $imagePath;
        }
        $user->save();

        $driver->update([
            'license_expiry_date' => $this->license_expiry_date ?? $driver->license_expiry_date,
            'is_smoker' => $this->is_smoker ?? $driver->is_smoker,
        ]);

        if ($this->hasFile('license')) {
            $licensePath = $this->file('license')->store('licenses', 'public');
            $driver->license = 'https://travelersres.com/' . $licensePath;
        }
        $driver->save();
        
        return $driver;
    }

}
