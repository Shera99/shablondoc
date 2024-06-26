<?php

namespace App\Http\Services;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create(array $data)
    {
        $user = app(User::class);

        $user->password = Hash::make($data['password']);

        $this->setUserData($user, $data);

        $user->save();

        $role = Role::where('name', $data['role'])->first();
        $user->assignRole($role);

        return $user;
    }

    public function update(User $user, array $data)
    {
        $user->status = $data['status'];
        $this->setUserData($user, $data);
        $user->save();
    }

    private function setUserData(User &$user, array $data)
    {
        $user->login = $data['login'];
        $user->email = $data['email'];

        $optionalFields = ['name', 'last_name', 'phone', 'address'];
        foreach ($optionalFields as $field) {
            if (isset($data[$field])) {
                $user->$field = $data[$field];
            }
        }
    }

    public function employeeCreate(int $user_id, int $company_id): void
    {
        $employee = app(Employee::class);
        $employee->user_id = $user_id;
        $employee->company_id = $company_id;
        $employee->save();
    }
}
