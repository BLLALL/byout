<?php

namespace App\Services;
class ReserveBusService extends ReserveVehicleService
{

    protected array $seats;

    protected function seatStructure($number): array|string
    {
        return match ($number) {
            24 => [
                'a1', 'a2', 'a3', 'a4',
                'b1', 'b2', 'b3', 'b4',
                'c1', 'c2', 'c3', 'c4',
                'd1', 'd2', 'd3', 'd4',
                'e1', 'e2', 'e3', 'e4',
                'f1', 'f2', 'f3', 'f4',
            ],
            30 => [
                'a1', 'a2', 'a3', 'a4',
                'b1', 'b2', 'b3', 'b4',
                'c1', 'c2', 'c3', 'c4',
                'd1', 'd2', 'd3', 'd4',
                'e1', 'e2', 'e3', 'e4',
                'f1', 'f2', 'f3', 'f4',
                'g1', 'g2', 'g3', 'g4', 'g5',
            ],
            46 => [
                'a1', 'a2', 'a3', 'a4',
                'b1', 'b2', 'b3', 'b4',
                'c1', 'c2', 'c3', 'c4',
                'd1', 'd2', 'd3', 'd4',
                'e1', 'e2', 'e3', 'e4',
                'f1', 'f2', 'f3', 'f4',
                'g1', 'g2', 'g3', 'g4',
                'h1', 'h2',
                'i1', 'i2', 'i3', 'i4',
                'j1', 'j2', 'j3', 'j4',
                'k1', 'k2', 'k3', 'k4',
                'l1', 'l2', 'l3', 'l4',
            ],
            49 => [
                'a1', 'a2', 'a3', 'a4',
                'b1', 'b2', 'b3', 'b4',
                'c1', 'c2', 'c3', 'c4',
                'd1', 'd2', 'd3', 'd4',
                'e1', 'e2', 'e3', 'e4',
                'f1', 'f2', 'f3', 'f4',
                'g1', 'g2', 'g3', 'g4',
                'h1', 'h2', 'h3', 'h4',
                'i1', 'i2', 'i3', 'i4',
                'j1', 'j2', 'j3', 'j4',
                'k1', 'k2', 'k3', 'k4',
                'l1', 'l2',
                'm1', 'm2', 'm3',
            ],
            53 => [
                'a1', 'a2', 'a3', 'a4',
                'b1', 'b2', 'b3', 'b4',
                'c1', 'c2', 'c3', 'c4',
                'd1', 'd2', 'd3', 'd4',
                'e1', 'e2', 'e3', 'e4',
                'f1', 'f2', 'f3', 'f4',
                'g1', 'g2',     
                'h1', 'h2',
                'i1', 'i2', 'i3', 'i4',
                'j1', 'j2', 'j3', 'j4',
                'k1', 'k2', 'k3', 'k4',
                'l1', 'l2', 'l3', 'l4',
                'm1', 'm2', 'm3', 'm4',
                'n1', 'n2', 'n3', 'n4', 'n5',
            ],
            67 => [
                'a1', 'a2', 'a3', 'a4', 'a5',
                'b1', 'b2', 'b3', 'b4', 'b5',
                'c1', 'c2', 'c3', 'c4', 'c5',
                'd1', 'd2', 'd3', 'd4', 'd5',
                'e1', 'e2', 'e3', 'e4', 'e5',
                'f1', 'f2', 'f3', 'f4', 'f5',
                'g1', 'g2', 'g3',
                'h1', 'h2', 'h3',
                'i1', 'i2', 'i3', 'i4', 'i5',
                'j1', 'j2', 'j3', 'j4', 'j5',
                'k1', 'k2', 'k3', 'k4', 'k5',
                'l1', 'l2', 'l3', 'l4', 'l5',
                'm1', 'm2', 'm3', 'm4', 'm5',
                'n1', 'n2', 'n3', 'n4', 'n5', 'n6',
            ],
            default => 'number of seats is incompatible with vehicle type'
        };
    }
}
