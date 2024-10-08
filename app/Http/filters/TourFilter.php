<?php

namespace App\Http\filters;

use Illuminate\Database\Eloquent\Builder;

class TourFilter extends QueryFilter
{
    public function price($value)
    {
        $prices = explode(',', $value);

        if(count($prices) > 1) {
            return $this->builder->whereBetween('price', $prices);
        }
        return $this->builder->where('price', $prices);
    }

    public function departure_time($value) {
        return $this->builder->where('departure_time', 'LIKE', '%' . $value . '%');
    }

    public function arrival_time($value) {
        return $this->builder->where('departure_time', 'LIKE', '%' . $value . '%');
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

    public function bus_id($value)
    {
        return $this->builder->where('bus_id', $value);
    }
}
