<?php

namespace App\Http\Controllers\Api\References;

use App\Models\CompanyAddress;
use App\Http\Requests\Api\CompanyAddress\{CompanyAddressCreateRequest, CompanyAddressUpdateRequest};
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyAddressController extends \App\Http\Controllers\Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $company = $request->company_id;
        $city = $request->city_id;

        $companies = CompanyAddress::query()
            ->where('company_id', $company)
            ->where('city_id', $city)
            ->where('status', true)
            ->get(['id', 'name'])->toArray();

        $this->setResponse($companies);

        return $this->sendResponse();
    }

    /**
     * @param CompanyAddressCreateRequest $request
     * @param CompanyAddress $companyAddress
     * @return JsonResponse
     */
    public function create(CompanyAddressCreateRequest $request, CompanyAddress $companyAddress): JsonResponse
    {
        $validate_data = $request->validated();

        $companyAddress->name = $validate_data['name'];
        $companyAddress->company_id = $validate_data['company_id'];
        $companyAddress->city_id = $validate_data['city_id'];
        $companyAddress->save();

        $this->setResponse($companyAddress->toArray());

        return $this->sendResponse();
    }

    /**
     * @param CompanyAddressUpdateRequest $request
     * @param CompanyAddress $companyAddress
     * @return JsonResponse
     */
    public function update(CompanyAddressUpdateRequest $request, CompanyAddress $companyAddress): JsonResponse
    {
        $validate_data = $request->validated();

        $companyAddress->name = $validate_data['name'];
        $companyAddress->status = $validate_data['status'];
        $companyAddress->update();

        $this->setResponse($companyAddress->toArray());

        return $this->sendResponse();
    }
}