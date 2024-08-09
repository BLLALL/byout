<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreHomeReviewRequest;
use App\Http\Resources\Api\V1\HomeReviewCollection;
use App\Http\Resources\Api\V1\ReviewResource;
use App\Http\Resources\Api\V1\UserReviewCollection;
use App\Models\Home;
use App\Models\Review;
use App\Models\User;

class ReviewController extends Controller
{
    public function UserReviews(User $user)
    {
        $reviews = $user->reviews;
        return new UserReviewCollection($reviews, $user->id);
    }

    public function BookReviews(Home $home)
    {
        $reviews = $home->reviews;
        return new HomeReviewCollection($reviews, $home->id);
    }

    public function store(StoreHomeReviewRequest $request)
    {
        $user = $request->user();

        return new ReviewResource(Review::create([
            'rating' => $request->input('data.attributes.rating'),
            'user_id' => $user->id,
            'home_id' => $request->input('data.attributes.home_id'),
        ]));
    }

}
