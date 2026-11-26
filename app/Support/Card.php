<?php

namespace App\Support;

class Card
{
    public static function digits(string $number): string
    {
        return preg_replace('/\D+/', '', $number) ?? '';
    }

    public static function valid(string $digits): bool
    {
        $length = strlen($digits);

        if ($length < 12 || $length > 19) {
            return false;
        }

        $sum = 0;
        $alternate = false;

        for ($index = $length - 1; $index >= 0; $index--) {
            $digit = (int) $digits[$index];

            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $alternate = ! $alternate;
        }

        return $sum % 10 === 0;
    }

    public static function brand(string $digits): string
    {
        return match (true) {
            str_starts_with($digits, '34'), str_starts_with($digits, '37') => 'Amex',
            str_starts_with($digits, '4') => 'Visa',
            str_starts_with($digits, '5') => 'Mastercard',
            str_starts_with($digits, '6') => 'RuPay',
            default => 'Card',
        };
    }
}
