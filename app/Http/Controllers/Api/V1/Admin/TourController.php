<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Travel;
use App\Http\Requests\TourRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;

class TourController extends Controller
{
    public function store(Travel $travel, TourRequest $request)
    {
        $tour = $travel->toures()->create($request->validated());
        return new TourResource($tour);
    }
}
