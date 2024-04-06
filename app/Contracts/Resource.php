<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface Resource
{
    public function toArray(Model $model, array $other_data = []): array;
}
