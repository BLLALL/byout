<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OwnerResource;
use App\Models\Chalet;
use App\Models\Owner;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function approveOwner(Owner $owner)
    {

        $owner->update(['status' => 'approved']);
        $user = $owner->user;
        return new OwnerResource($owner);
    }

    public function rejectOwner(Owner $owner)
    {
        $owner->update(['status' => 'rejected']);
        return response()->json(['message' => 'Owner rejected successfully']);
    }

    public function approveChalet(Chalet $chalet)
    {
        $chalet->update(['status' => 'approved']);
        return response()->json(['message' => 'Chalet approved successfully']);
    }


    public function rejectChalet(Chalet $chalet)
    {
        $chalet->update(['status' => 'rejected']);
        return response()->json(['message' => 'Chalet rejected successfully']);
    }

    public function getFinancialReport()
    {

    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }


}
