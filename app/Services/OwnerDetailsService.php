<?php

namespace App\Services;

use App\Models\Owner;
use App\Models\TourReservation;
use App\Models\Home;
use App\Models\Rental;
use App\Models\Hotel;
use App\Models\Chalet;

class OwnerDetailsService
{
    public function getTourCompanyOwnerDetails(Owner $owner): array
    {
        $tourReservations = TourReservation::whereRelation('tour', function ($query) use ($owner) {
            $query->where('owner_id', $owner->id);
        })
            ->with(['user', 'tour'])
            ->latest()
            ->get();

        return [
            'tour_reservations' => $tourReservations->map(function ($reservation) {
                return [
                    'user_reserving' => $reservation->user,
                    'tour_reserved' => $reservation->tour,
                    'reservation_date' => $reservation->created_at,
                    'payment' => $reservation->tour->price,
                ];
            }),
        ];
    }

    public function getHomeOwnerDetails(Owner $owner): array
    {
        $home = Home::where('owner_id', $owner->id)->firstOrFail();
        $rentals = $this->getRentals($home, 'App\Models\Home');

        return [
            'rented_homes' => $this->mapRentals($rentals),
        ];
    }

    public function getHotelOwnerDetails(Owner $owner): array
    {
        $hotels = Hotel::where('owner_id', $owner->id)->get();
        $rooms = $hotels->flatMap(function ($hotel) {
            return $hotel->hotelRooms;
        });
        
        $rentals = collect();
        if ($rooms->isNotEmpty()) {
            $rentals = Rental::whereIn('rentable_id', $rooms->pluck('id'))
                ->where('rentable_type', 'App\Models\HotelRooms')
                ->with(['user', 'rentable'])
                ->latest()
                ->get();
        }

        $ownerDetails = $owner->toArray();
        $ownerDetails['id'] = $ownerDetails['user_id'];
        unset($ownerDetails['user_id']);

        $ownerDetails['rented_rooms'] = $rentals->map(function ($rental) {
            return [
                'user_renting' => $rental->user,
                'room_rented' => $rental->rentable,
                'rental_period' => [
                    'check_in' => $rental->check_in,
                    'check_out' => $rental->check_out,
                ],
                'payment' => $rental->rentable->price * ($rental->check_in->diffInDays($rental->check_out) + 1),
            ];
        });

        return $ownerDetails;
    }

    public function getChaletOwnerDetails(Owner $owner): array
    {
        $chalet = Chalet::where('owner_id', $owner->id)->firstOrFail();
        $rentals = $this->getRentals($chalet, 'App\Models\Chalet');

        return [
            'rented_chalets' => $this->mapRentals($rentals),
        ];
    }

    private function getRentals($rentable, string $rentableType)
    {
        if ($rentable instanceof Collection) {
            $rentableIds = $rentable->pluck('id')->toArray();
        } else {
            $rentableIds = [$rentable->id];
        }

        return Rental::whereIn('rentable_id', $rentableIds)
            ->where('rentable_type', $rentableType)
            ->with(['user', 'rentable'])
            ->latest()
            ->get();
    }

    private function mapRentals($rentals)
    {
        return $rentals->map(function ($rental) {
            $payment = $rental->rentable->price;
            if ($rental->check_in && $rental->check_out) {
                $payment *= ($rental->check_in->diffInDays($rental->check_out) + 1);
            }

            return [
                'user_renting' => $rental->user,
                'property_rented' => $rental->rentable,
                'rental_period' => [
                    'check_in' => $rental->check_in,
                    'check_out' => $rental->check_out,
                ],
                'payment' => $payment,
            ];
        });
    }
}