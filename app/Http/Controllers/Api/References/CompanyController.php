<?php

namespace App\Http\Controllers\Api\References;

use App\Http\Requests\Api\Company\CompanyCreateRequest;
use App\Http\Requests\Api\Company\CompanyUpdateRequest;
use App\Models\Company;
use App\Models\CompanyType;
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
            ->with('companyType')
            ->where('country_id', $country_id)
            ->whereHas('companyAddress', function ($query) use ($city) {
                $query->where('city_id', $city);
            })
            ->orderBy('name', 'asc')
            ->get(['id', 'name'])->toArray();

        $this->setResponse($companies);

        return $this->sendResponse();
    }

    /**
     * @param CompanyCreateRequest $request
     * @param Company $company
     * @return JsonResponse
     */
    public function create(CompanyCreateRequest $request, Company $company): JsonResponse
    {
        $validate_data = $request->validated();

        $company->name = $validate_data['name'];
        $company->user_id = auth()->user()->getAuthIdentifier();
        $company->country_id = $validate_data['country_id'];
        $company->company_type_id = $validate_data['company_type_id'];

        $company->save();

        $this->setResponse($company->toArray());

        return $this->sendResponse();
    }

    /**
     * @param CompanyUpdateRequest $request
     * @param Company $company
     * @return JsonResponse
     */
    public function update(CompanyUpdateRequest $request, Company $company): JsonResponse
    {
        $company->name = $request->validated()['name'];
        $company->update();

        $this->setResponse($company->toArray());

        return $this->sendResponse();
    }

    public function byUser(Request $request): JsonResponse
    {
        $companies = Company::with('country:id,name', 'companyType')
            ->where('user_id', auth()->user()->getAuthIdentifier())
            ->get();

        if ($companies) $this->setResponse($companies->toArray());

        return $this->sendResponse();
    }

    public function companyType(): JsonResponse
    {
        $company_types = CompanyType::all();

        if ($company_types) $this->setResponse($company_types->toArray());

        return $this->sendResponse();
    }
}
