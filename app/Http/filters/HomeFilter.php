<?php

namespace App\Http\filters;

class HomeFilter extends QueryFilter
{
    public function price($value)
    {
        $prices = explode(',', $value);

        if(count($prices) > 1) {
            return $this->builder->whereBetween('price', $prices);
        }
        return $this->builder->where('price', $prices);
    }

    public function title($value)
    {

        return $this->builder->where('title', 'LIKE', '%' . $value . '%');
    }

    public function description($value)
    {
        return $this->builder->where('description', 'LIKE', '%' . $value . '%');

    }
}
