<?php

namespace App\Services;
class ReserveBusService extends ReserveVehicleService
{

    protected array $seats;

    protected function seatStructure($number) : array|string{
       return match ($number) {
            3 => [
                'a1',
                'b1', 'b2',
            ],
            5 => [
                'a1',
                'b1', 'b2',
                'c1', 'c2',
            ],
            default => 'number of seats is incompatible with vehicle type'
        };
    }
}
