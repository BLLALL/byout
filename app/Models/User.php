<?php

namespace App\Models;

use App\Http\filters\QueryFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use \Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];


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

    public function scopeFilter(Builder $builder, QueryFilter $filter)
    {
        return $filter->apply($builder);
    }

    public function owner()
    {
        return $this->hasOne(Owner::class)->chaperone();
    }

    public function ownsCompanyWithOwnerId($ownerId)
    {
        return $this->owner()->where('id', $ownerId)->exists();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function hotelReviews()
    {
        return $this->reviews()->where('reviewable_type', Hotel::class);
    }

    public function chaletReviews()
    {
        return $this->reviews()->where('reviewable_type', Chalet::class);
    }

    public function homeReviews()
    {
        return $this->reviews()->where('reviewable_type', Home::class);
    }


    public function favouriteHome()
    {
        return $this->morphedByMany(Home::class, 'favorable',
            'favourites')->exists();
    }

    public function favouriteHotel()
    {
        return $this->morphedByMany(Hotel::class, 'favorable',
            'favourites');
    }

    public function favouriteChalet()
    {
        return $this->morphedByMany(Chalet::class, 'favorable',
            'favourites');
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class);
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
