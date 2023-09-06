<?php

namespace Geekk\PayselectionPaymentsPhp\Paylink;

class Formatter
{

    public static function floatToString(float $value): string
    {
        return number_format($value, 2, '.', '');
    }
}
