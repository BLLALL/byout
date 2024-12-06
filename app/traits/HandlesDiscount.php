<?php

namespace App\traits;
trait HandlesDiscount {
    public function handlePriceUpdate($entity, &$data)
    {
        if(isset($data['price']))
        {
            $newPrice = $data['price'];
            $currentPrice = $entity->price;

            if ($newPrice < $currentPrice) {
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