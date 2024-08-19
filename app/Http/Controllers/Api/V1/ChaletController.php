<?php

namespace App\Http\Controllers;

use App\Http\Resources\Api\V1\ChaletResource;
use App\Models\Chalet;
use Illuminate\Http\Request;

class ChaletController extends Controller
{
    public function index()
    {
        return ChaletResource::collection(Chalet::get());
    }

    public function show(Chalet $chalet): ChaletResource
    {
        return new ChaletResource($chalet);
    }

    public function store()
    {

    }
}
