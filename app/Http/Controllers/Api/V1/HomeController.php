<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\filters\HomeFilter;
use App\Http\Requests\Api\V1\storeHomeRequest;
use App\Http\Resources\Api\V1\HomeResource;
use App\Models\Home;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Get Homes
     *
     * @group Managing Homes
     * @queryParam sort string Data field to sort by. Separate multiple parameters with commas. Denote descending order with a minus sign.
     * Example: title, -created_at
     * @queryParam price integer Data field to filter homes by their price u can use comma to filter by range. Example: 2000,100000
     * @queryParam title string Data field to search for homes by their title. Example:Lorem
     * @queryParam description string Data field to search for homes by their description. Example:Lorem Ipsum
     *
     */
    public function index(HomeFilter $filter)
    {
        return HomeResource::collection(Home::filter($filter)->get());
    }


    /**
     * Create a Home
     *
     * @group Managing Homes
     *
     *
     */
    public function store(storeHomeRequest $request)
    {
        return new HomeResource(Home::create($request->mappedAttributes()));
    }

    /**
     * show a specific Home
     *
     *Display an individual home
     *
     * @group Managing Homes
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
