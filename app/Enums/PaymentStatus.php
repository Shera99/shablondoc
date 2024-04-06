<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PaymentStatus extends Enum
{
    const PENDING = 'pending';
    const COMPLETED = 'completed';
    const FAILED = 'failed';
    const REFUNDED = 'refunded';
}
