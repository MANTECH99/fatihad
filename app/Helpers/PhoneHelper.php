<?php

namespace App\Helpers;

class PhoneHelper
{
    public static function formatLocal($phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $phone = substr($phone, -9);

        if (strlen($phone) !== 9) {
            return $phone;
        }

        return substr($phone, 0, 2) . ' '
            . substr($phone, 2, 3) . ' '
            . substr($phone, 5, 2) . ' '
            . substr($phone, 7, 2);
    }
}
