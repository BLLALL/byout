<?php

namespace App\Http\filters;

use Illuminate\Database\Eloquent\Builder;

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

    public function owner_id($value)
    {
        return $this->builder->whereHas('owner', (function (Builder $query) use ($value) {
            $query->where('user_id', $value);
        }));
    }
}
