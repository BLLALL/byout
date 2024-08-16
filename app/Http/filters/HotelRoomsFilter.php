<?php

namespace App\Http\filters;

use App\Models\Hotel;

class HotelRoomsFilter extends QueryFilter
{
    public function price($value)
    {
        $prices = explode(',', $value);

        if (count($prices) > 1) {
            return $this->builder->whereBetween('price', $prices);
        }
        return $this->builder->where('price', $prices);
    }

    public function location($value)
    {

        return $this->hotel::where('location', $value);;
    }

    public function is_reserved($value)
    {
        return $this->builder->where('is_reserved', $value);
    }

}
