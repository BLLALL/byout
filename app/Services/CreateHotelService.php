<?php

    namespace App\Services;

    use App\Models\Hotel;
    use Brick\Money\Money;

    class CreateHotelService extends CreateEntityService
    {
        public function getModel()
        {
            return new Hotel();
        }

        protected function getFillableAttributes()
        {
            return [
                'name',
                'location',
                'wifi',
                'coordinates',
                'hotel_images',
                'owner_id',

            ];
        }

        protected function getImageColumn()
        {
            return 'hotel_images';
        }

        protected function getImagePath()
        {
            return 'hotel_images';
        }


        protected function handleAdditionalData($hotel, $data)
        {
            if (isset($data['rooms']) && is_array($data['rooms'])) {
                $createHotelRoomService = new CreateHotelRoomService();
                foreach ($data['rooms'] as $roomData) {
                    $roomData['hotel_id'] = $hotel->id;
                    $request = new \Illuminate\Http\Request($roomData);
                    $room = $createHotelRoomService->createEntity($request);
                }
            }
        }
        public function createEntity($request)
        {
            $hotel = parent::createEntity($request);
            $this->handleAdditionalData($hotel, $request->validated());
            return $hotel;
        }
    }
