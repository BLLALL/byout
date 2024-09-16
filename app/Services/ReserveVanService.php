<?php

namespace App\Services;
class ReserveVanService extends ReserveVehicleService
{

    protected array $seats;

    protected function seatStructure($number) : array|string{
       return match ($number) {
           7 => [
                'a1',
                'b1', 'b2', 'b3',
               'c1', 'c2', 'c3'
            ],
           14 => [
               'a1',
               'b1', 'b2', 'b3',
               'c1', 'c2', 'c3',
               'd1', 'd2', 'd3', 'd4',
           ],
            default => 'number of seats is incompatible with vehicle type'
        };
    }
}
