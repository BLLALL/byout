<?php

namespace App\Http\filters;

class UserFilter extends QueryFilter {

    public function name($value) {
        return $this->builder->where('name', $value);
    }

    public function status($value)
    {
        return $this->builder->whereHas('owner', function ($query) use ($value) {
            $query->where('status', $value);
        });
    }



}
