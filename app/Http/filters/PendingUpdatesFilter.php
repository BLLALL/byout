<?php

namespace App\Http\filters;


class PendingUpdatesFilter extends QueryFilter 
{

    public function updatable_type($value)
    {
        return $this->builder->where('updatable_type', "App\\Models\\" . $value);
    }
}
