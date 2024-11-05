<?php

namespace App\traits;
trait HandlesDiscount {
    public function handlePriceUpdate($entity, &$data)
    {
        if(isset($data['price']))
        {
            \Log::info('Price update detected for entity: ' . $entity->id);
            $newPrice = $data['price'];
            $currentPrice = $entity->price;

            if ($newPrice < $currentPrice) {
                \Log::info('Discount detected for entity: ' . $entity->id);
                $data['discount_price'] = $newPrice;
                $data['price'] = $currentPrice;
            }
            else {

                $data['price'] = $newPrice;
                $data['discount_price'] = null;
            }
        }
    }
}