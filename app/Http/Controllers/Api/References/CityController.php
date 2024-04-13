<?php

namespace App\Http\Controllers\Api\References;

use App\Models\City;
use Illuminate\Http\JsonResponse;

class CityController extends \App\Http\Controllers\Controller
{
    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $cities = City::with('country:id,name')->get(['id', 'name'])->toArray();
        $this->setResponse($cities);

        return $this->sendResponse();
    }

    public function showByCountry(int $country_id): JsonResponse
    {
        $cities = City::query()->where('country_id', $country_id)->get(['id', 'name', 'country_id'])->toArray();
        $this->setResponse($cities);

        return $this->sendResponse();
    }
}
