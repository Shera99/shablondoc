<?php

namespace App\Helpers;

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
}
