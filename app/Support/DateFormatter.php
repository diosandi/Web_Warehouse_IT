<?php

namespace App\Support;

use Carbon\Carbon;
use DateTimeInterface;
use Throwable;

class DateFormatter
{
    public static function date(mixed $value, string $fallback = '-'): string
    {
        return self::format($value, 'd-m-Y', $fallback);
    }

    public static function datetime(mixed $value, string $fallback = '-'): string
    {
        return self::format($value, 'd-m-Y H:i', $fallback);
    }

    public static function dateRange(?string $from, ?string $to): string
    {
        if ($from && $to) {
            return self::date($from) . ' sampai ' . self::date($to);
        }

        if ($from) {
            return 'Mulai ' . self::date($from);
        }

        if ($to) {
            return 'Sampai ' . self::date($to);
        }

        return 'Semua periode';
    }

    public static function format(mixed $value, string $format, string $fallback = '-'): string
    {
        if ($value === null || $value === '') {
            return $fallback;
        }

        try {
            if ($value instanceof DateTimeInterface) {
                return Carbon::instance($value)->format($format);
            }

            return Carbon::parse((string) $value)->format($format);
        } catch (Throwable) {
            return (string) $value ?: $fallback;
        }
    }
}
