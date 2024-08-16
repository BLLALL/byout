<?php

namespace App\Http\filters;

class HotelFilter extends QueryFilter
{


    public function description($value)
    {
        return $this->builder->where('description', 'LIKE', '%' . $value . '%');

    }
}
