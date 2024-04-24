<?php

namespace App\Http\Controllers\Api\References;

use App\Http\Requests\Api\Company\{CompanyAddressCreateRequest, CompanyAddressUpdateRequest};
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends \App\Http\Controllers\Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $country_id = $request->country_id;
        $city = $request->city_id;

        $companies = Company::query()
            ->where('country_id', $country_id)
            ->whereHas('companyAddress', function ($query) use ($city) {
                $query->where('city_id', $city);
            })
            ->get(['id', 'name'])->toArray();

        $this->setResponse($companies);

        return $this->sendResponse();
    }

    /**
     * @param CompanyAddressCreateRequest $request
     * @param Company $company
     * @return JsonResponse
     */
    public function create(CompanyAddressCreateRequest $request, Company $company): JsonResponse
    {
        $validate_data = $request->validated();

        $company->name = $validate_data['name'];
        $company->user_id = auth()->user()->getAuthIdentifier();
        $company->country_id = $validate_data['country_id'];
        $company->save();

        $this->setResponse($company->toArray());

        return $this->sendResponse();
    }

    /**
     * @param CompanyAddressUpdateRequest $request
     * @param Company $company
     * @return JsonResponse
     */
    public function update(CompanyAddressUpdateRequest $request, Company $company): JsonResponse
    {
        $company->name = $request->validated()['name'];
        $company->update();

        $this->setResponse($company->toArray());

        return $this->sendResponse();
    }
}
