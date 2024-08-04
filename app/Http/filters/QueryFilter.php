<?php

namespace App\Http\filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected $builder;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) {
            if(method_exists($this, $key)) {
                $this->$key($value);
            }
        }
        return $builder;
    }

    //public
    protected function filter($arr) {
        foreach($arr as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    }

    public function sort($value) {
        $sortAttrs = explode(',', $value);

        foreach ($sortAttrs as $sortAttr) {
            $direction = 'asc';

            if($sortAttr[0] === '-') {
                $direction = 'desc';
                $sortAttr = substr($sortAttr, 1);
            }
            return $this->builder->orderBy($sortAttr, $direction);

        }

    }

}
