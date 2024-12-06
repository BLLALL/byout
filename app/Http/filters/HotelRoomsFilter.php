<?php

namespace App\Http\filters;

use App\Models\Hotel;

class HotelRoomsFilter extends QueryFilter
{

    public function date_range($value)
    {
        $dates = explode(',', $value);
        $fromDate = $dates[0];
        $toDate = $dates[1];
        if ($fromDate && $toDate) {
            return $this->builder->availableBetween($fromDate, $toDate);
        }
        
    }
}
