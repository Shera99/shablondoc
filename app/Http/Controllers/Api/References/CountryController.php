<?php

namespace App\Http\Controllers\Api\References;

use App\Models\Country;
use Illuminate\Http\JsonResponse;

class CountryController extends \App\Http\Controllers\Controller
{
    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $countries = Country::orderBy('name', 'asc')->all(['id', 'name', 'code'])->toArray();
        $this->setResponse($countries);

        return $this->sendResponse();
    }
}
