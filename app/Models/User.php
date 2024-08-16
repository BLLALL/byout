<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'traveller_gender',
        'age',
        'marital_status',
        'current_job',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function review()
    {
        return $this->hasMany(Review::class);
    }

    public function home()
    {
        return $this->hasMany(Home::class);
    }

    public function hotel()
    {
        return $this->hasMany(Hotel::class);
    }
    public function favouriteHome()
    {
        return $this->belongsToMany(Home::class, 'home_favourites',
            'user_id', 'home_id');
    }

    /**
     * Get the name of the guard for API authentication.
     *
     * @return string
     */
    public function guardName(): string
    {
        return 'api';
    }
}
