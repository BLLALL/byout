<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreHomeReviewRequest;
use App\Http\Requests\Api\V1\StoreReviewRequest;
use App\Http\Resources\Api\V1\HomeReviewCollection;
use App\Http\Resources\Api\V1\ReviewResource;
use App\Http\Resources\Api\V1\UserReviewCollection;
use App\Models\Chalet;
use App\Models\Home;
use App\Models\Hotel;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use function Pest\Laravel\json;

class ReviewController extends Controller
{
    public function UserReviews(User $user)
    {

        if ($user->id !== Auth::user()->id)
            return response()->json([
                "Your're not authorized to access other users' reviews"
            ]);

        $homeReviews = $user->homeReviews()->with('reviewable')->get();
        $hotelReviews = $user->hotelReviews()->with('reviewable')->get();
        $chaletReviews = $user->chaletReviews()->with('reviewable')->get();


        // Merge reviews together
        $reviews = $homeReviews->merge($hotelReviews)->merge($chaletReviews);

        // Group by the type of the reviewable model (e.g., home, hotel, chalet)
        $groupedReviews = $reviews->groupBy(function ($review) {
            return strtolower(class_basename($review->reviewable_type));
        })->map(function ($group) {
            return $group->map(function ($review) {
                return $review->reviewable;  // This gives you the actual Home, Hotel, Chalet models
            });
        });

        if ($groupedReviews->isEmpty()) {
            return response()->json([
                'data' => [
                    "homes" => [],
                    "hotels" => [],
                ]
            ]);
        }
        return response()->json([
            'data' => $groupedReviews
        ]);
    }

    public function HomeReviews(Home $home)
    {
        return $home->reviews;
        $reviews = $home->reviews()->with('user')->get();
        if ($reviews->isEmpty()) {
            return response()->json([
                'message' => 'No reviews found for this resource.',
                'data' => []
            ], 200);
        }
        return ReviewResource::collection($reviews);
    }

    public function HotelReviews(Hotel $hotel)
    {
        $reviews = $hotel->reviews()->with('user')->get();
        if ($reviews->isEmpty()) {
            return response()->json([
                'message' => 'No reviews found for this resource.',
                'data' => []
            ], 200);
        }

        return ReviewResource::collection($reviews);
    }

    public function ChaletReviews(Chalet $chalet)
    {
        $reviews = $chalet->reviews()->with('user')->get();
        if ($reviews->isEmpty()) {
            return response()->json([
                'message' => 'No reviews found for this resource.',
                'data' => []
            ], 200);
        }

        return ReviewResource::collection($reviews);
    }


    public function store(StoreReviewRequest $request)
    {
        $user = Auth::user();
        if ($user->id !== $request->input('data.user_id')) {
            return response()->json([
                "You're not authorized to use other user's ids"
            ]);
        }
        $reviewableType = 'App\Models\\' . ucfirst($request->input('data.type'));
        $reviewableId = $request->input('data.reviewable_id');

        $review = Review::updateOrCreate(
            [
                'user_id' => $request->input('data.user_id'),
                'reviewable_type' => $reviewableType,
                'reviewable_id' => $reviewableId,
            ],
            [
                'rating' => $request->input('data.rating'),
            ]
        );

        return new ReviewResource($review);
    }

    public function getModelClass($type)
    {
        $models = [
            'Home' => Home::class,
            'Hotel' => Hotel::class,
            'Chalet' => Chalet::class
        ];
        return $models[$type] ?? null;
    }
}
