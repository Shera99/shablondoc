<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class OrderStatus extends Enum
{
    const PENDING = 'pending';
    const COMPLETED = 'completed';
    const TRANSLATION = 'translation';
    const DELIVERY = 'delivery';
    const DELIVERED = 'delivered';
    const FAILED = 'failed';
    const MODERATION = 'moderation';
    const TRANSLATE_MODERATION = 'translate_moderation';
}
