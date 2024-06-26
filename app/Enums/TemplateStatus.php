<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TemplateStatus extends Enum
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const MODERATION = 'moderation';
}
