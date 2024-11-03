<?php

namespace App\Http\filters;

class UserFilter extends QueryFilter {

    public function name($value) {
        return $this->builder->where('name', $value);
    }

    public function status($value)
    {
        return $this->builder->whereRelation('owner', 'status', $value);
    }



}
