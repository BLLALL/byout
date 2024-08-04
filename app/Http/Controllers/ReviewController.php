<?php

namespace App\Http\Controllers;

use App\Http\Requests\Api\V1\StoreHomeReviewRequest;
use App\Http\Resources\Api\V1\HomeReviewCollection;
use App\Http\Resources\Api\V1\ReviewResource;
use App\Http\Resources\Api\V1\UserReviewCollection;
use App\Models\Home;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Ticket;

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

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        return new ReviewResource(Review::create([
            'rating' => $request->input('data.attributes.rating'),
            'user_id' => $request->user()->id,
            'home_id' => $request->input('data.attributes.home_id'),
        ]));
    }

}
