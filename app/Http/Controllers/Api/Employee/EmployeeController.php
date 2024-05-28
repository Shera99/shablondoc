<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Http\Services\UserService;
use App\Http\Requests\Api\Employee\{EmployeeCreateRequest,EmployeeUpdateRequest};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EmployeeController extends Controller
{
    protected UserService $userService;

    public function __construct()
    {
        $this->userService = app(UserService::class);
    }

    public function create(EmployeeCreateRequest $request): JsonResponse
    {
        try {
            $validate_data = $request->validated();
            $validate_data['role'] = 'Employee';

            $user = $this->userService->create($validate_data);
            $this->userService->employeeCreate($user->id, $validate_data['company_id']);

            return $this->sendResponse();
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(EmployeeUpdateRequest $request): JsonResponse
    {
        $validate_data = $request->validated();
        //update code

        return $this->sendResponse();
    }

    public function list(int $company): JsonResponse
    {
        $employees = DB::table('employees')->join('users', 'employees.user_id', '=', 'users.id')
            ->where('employees.company_id', $company)
            ->get(['users.id', 'users.name', 'users.last_name', 'users.email',
                'users.phone', 'users.address', 'users.status']);

        if ($employees->isNotEmpty()) $this->setResponse($employees->toArray());

        return $this->sendResponse();
    }
}
