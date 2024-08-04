<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class HomeReviewCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */

    protected $home_id;

    public function __construct($home_reviews, $home_id)
    {
        $this->home_id = $home_id;
        parent::__construct($home_reviews);
    }

    public function toArray(Request $request): array
    {
        return [
            'type' => 'home_specific_review',
            'home_id' => $this->home_id,
            'attributes' => $this->collection,
        ];
    }
}
