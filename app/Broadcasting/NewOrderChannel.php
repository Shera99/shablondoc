<?php

namespace App\Broadcasting;

use App\Enums\RoleType;
use App\Models\User;

class NewOrderChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user): array|bool
    {
        return true;//$user->hasRole([RoleType::CORPORATE, RoleType::STANDARD, RoleType::EMPLOYEE]);
    }
}
