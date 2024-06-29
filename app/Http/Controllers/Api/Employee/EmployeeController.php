<?php

namespace App\Http\Controllers\Api\Employee;

use App\Http\Controllers\Controller;
use App\Http\Services\UserService;
use App\Models\Employee;
use App\Models\User;
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

    public function update(User $user, EmployeeUpdateRequest $request): JsonResponse
    {
        try {
            $validate_data = $request->validated();

            if (User::where('email', $validate_data['email'])->where('id', '<>', $user->id)->exists())
                return $this->sendErrorResponse('A user with this email already exists', Response::HTTP_BAD_REQUEST);

            $this->userService->update($user, $validate_data);

            return $this->sendResponse();
        } catch (\Exception $e) {
            return $this->sendErrorResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function list(int $company): JsonResponse
    {
        $employees = Employee::with(['user'])
            ->where('company_id', $company)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'user' => $employee->user,
                    'order_count' => $employee->order_count,
                ];
            });

        if ($employees->isNotEmpty()) $this->setResponse($employees->toArray());

        return $this->sendResponse();
    }
}
