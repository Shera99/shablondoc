<?php

namespace App\Http\Controllers\Api\References;

use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends \App\Http\Controllers\Controller
{
    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $cities = City::with('country:id,name')->get(['id', 'name', 'country_id'])->toArray();
        $this->setResponse($cities);

        return $this->sendResponse();
    }

    public function showByCountry(Request $request): JsonResponse
    {
        $cities = City::query()->where('country_id', $request->country_id)->get(['id', 'name', 'country_id'])->toArray();
        $this->setResponse($cities);

        return $this->sendResponse();
    }
}
