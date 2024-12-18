<?php

namespace App\Http\filters;

use Illuminate\Database\Eloquent\Builder;

class TourFilter extends QueryFilter
{
   
    public function departure_time($value) {
        return $this->builder->where('departure_time', '>=', $value);
    }

    public function arrival_time($value) {
        return $this->builder->where('arrival_time', '<=', $value);
    }

    public function inBetween($value) {
        $times = explode(',', $value);
        return $this->builder->whereBetween('departure_time', $times);
    }

    public function source($value) {
        return $this->builder->where('source', $value);
    }

    public function destination($value) {
        return $this->builder->where('destination', $value);
    }

    public function owner_id($value)
    {
        return $this->builder->whereHas('owner', (function (Builder $query) use ($value) {
            $query->where('user_id', $value);
        }));
    }

    public function driver_id($value)
    {
        return $this->builder->where('driver_id', $value);
    }

    public function vehicle_id($value)
    {
        return $this->builder->where('vehicle_id', $value);
    }

    public function vehicle_type($value)
    {
        return $this->builder->whereHas('vehicle', (function (Builder $query) use ($value) {
            $query->where('type', $value);
        }));
    }
}
