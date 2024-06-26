<?php

namespace App\Http\Resources;

use App\Contracts\Resource;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserResource implements Resource
{
    public function toArray(Model $model, array $other_data = []): array
    {
        $userData = [
            'id' => $model->id,
            'name' => $model->name,
            'last_name' => $model->last_name,
            'login' => $model->login,
            'email' => $model->email,
            'phone' => $model->phone,
            'address' => $model->address,
            'role' => $model->getRoleName(),
            'register_date' => $model->created_at
        ];

        return array_merge($userData, $other_data);
    }
}
