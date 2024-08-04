<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserReviewCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */

    protected $user_id;
    public function __construct($user_reviews, $user_id)
    {
        $this->user_id = $user_id;
        parent::__construct($user_reviews);
    }

    public function toArray(Request $request): array
    {
        return [
            'type' => 'user_reviews',
            'user_id' => $this->user_id,
            'attributes' => $this->collection
        ];
    }
}
