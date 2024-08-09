<?php

namespace App\Http\filters;

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


}
