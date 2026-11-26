<?php

namespace App\Support;

class Pricing
{
    public const FREE_SHIPPING_OVER = 999;

    public const SHIPPING_FEE = 49;

    public static function shipping(float $subtotal): float
    {
        if ($subtotal <= 0 || $subtotal >= self::FREE_SHIPPING_OVER) {
            return 0;
        }

        return self::SHIPPING_FEE;
    }
}
