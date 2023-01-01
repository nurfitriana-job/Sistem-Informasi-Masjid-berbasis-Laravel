<?php

namespace App\Helpers;

class Format
{
    public static function removeSeparator($value)
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    public static function formatShortCurrency(float | int $number): string
    {
        if ($number >= 1_000_000_000) {
            return rtrim(rtrim(number_format($number / 1_000_000_000, 2, ',', '.'), '0'), ',') . ' M';
        }

        if ($number >= 1_000_000) {
            return rtrim(rtrim(number_format($number / 1_000_000, 2, ',', '.'), '0'), ',') . ' jt';
        }

        if ($number >= 1_000) {
            return rtrim(rtrim(number_format($number / 1_000, 2, ',', '.'), '0'), ',') . ' rb';
        }

        return 'Rp. ' . number_format($number, 0, ',', '.');
    }
}
