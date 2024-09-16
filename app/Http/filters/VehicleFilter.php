<?php

namespace App\Http\filters;

use Illuminate\Database\Eloquent\Builder;

class VehicleFilter extends QueryFilter
{
    public function type($value)
    {
        return $this->builder->where('type', $value);
    }

    public function model($value)
    {
        return $this->builder->where('model', 'LIKE', "%$value%");
    }

    public function registration_number($value)
    {
        return $this->builder->where('registration_number', $value);
    }

    public function seats_number($value)
    {
        return $this->builder->where('seats_number', $value);
    }

    public function status($value)
    {
        return $this->builder->where('status', $value);
    }

    public function driver_id($value)
    {
        return $this->builder->where('driver_id', $value);
    }

    public function has_wifi($value)
    {
        return $this->builder->where('has_wifi', $value);
    }

    public function has_air_conditioner($value)
    {
        return $this->builder->where('has_air_conditioner', $value);
    }

    public function has_gps($value)
    {
        return $this->builder->where('has_gps', $value);
    }

    public function has_movie_screens($value)
    {
        return $this->builder->where('has_movie_screens', $value);
    }

    public function owner_id($value)
    {
        return $this->builder->whereHas('owner', (function (Builder $query) use ($value) {
            $query->where('user_id', $value);
        }));
    }
}
