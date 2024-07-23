<?php

namespace App\Helpers;

use App\Models\Currency;

class ApiHelper
{
    public static function generateEmailVerificationCode(int $length): string
    {
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $d = rand(1, 30) % 2;
            $code .= $d ? chr(rand(65, 90)) : chr(rand(48, 57));
        }

        return $code;
    }

    public static function getConvertedAmount(string $currency, int $amount)
    {
        if ($currency != 'USD') {
            $amount = $amount / Currency::where('code', $currency)->value('convert');
        }
        return number_format($amount, 2, '.');
    }
}
