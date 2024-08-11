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
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function UserReviews(User $user)
    {
        $reviews = $user->reviews;
        return new UserReviewCollection($reviews, $user->id);
    }

    public function HomeReviews(Home $home)
    {
        $reviews = $home->reviews;
        return new HomeReviewCollection($reviews, $home->id);
    }

    public function store(StoreHomeReviewRequest $request)
    {
        $user = Auth::user();

        return new ReviewResource(Review::updateOrCreate(
            ['user_id' => $request->input('data.user_id')],
            [
                'rating' => $request->input('data.rating'),
                'home_id' => $request->input('data.home_id'),
            ]));
    }

}
