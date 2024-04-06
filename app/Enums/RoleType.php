<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class RoleType extends Enum
{
    const ANONYMOUS = 'Anonymous';
    const ADMINISTRATOR = 'Super-Admin';
    const CORPORATE = 'Corporate';
    const MODERATOR = 'Moderator';
    const STANDARD = 'Standard';
}
