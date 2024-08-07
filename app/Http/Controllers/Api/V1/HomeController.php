<?php

namespace App\Http\Controllers;

use App\Http\filters\HomeFilter;
use App\Http\Requests\Api\V1\storeHomeRequest;
use App\Http\Resources\Api\V1\HomeResource;
use App\Models\Home;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(HomeFilter $filter)
    {
        return HomeResource::collection(Home::filter($filter)->get());
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(storeHomeRequest $request)
    {
        return new HomeResource(Home::create($request->mappedAttributes()));
    }

    /**
     * Display the specified resource.
     */
    public function show(Home $home)
    {
        return new HomeResource($home);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Home $home)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Home $home)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Home $home)
    {
        //
    }
}
