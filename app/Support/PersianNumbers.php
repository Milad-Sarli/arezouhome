<?php

namespace App\Support;

/**
 * Persian number to words. Used for invoice total in words.
 */
class PersianNumbers
{
    private const ONES = ['', 'یک', 'دو', 'سه', 'چهار', 'پنج', 'شش', 'هفت', 'هشت', 'نه'];

    private const TEENS = ['ده', 'یازده', 'دوازده', 'سیزده', 'چهارده', 'پانزده', 'شانزده', 'هفده', 'هجده', 'نوزده'];

    private const TENS = ['', '', 'بیست', 'سی', 'چهل', 'پنجاه', 'شصت', 'هفتاد', 'هشتاد', 'نود'];

    private const HUNDREDS = ['', 'یکصد', 'دویست', 'سیصد', 'چهارصد', 'پانصد', 'ششصد', 'هفتصد', 'هشتصد', 'نهصد'];

    private const SCALES = ['', 'هزار', 'میلیون', 'میلیارد', 'هزار میلیارد', 'بیلیون'];

    public static function toWords(int $number): string
    {
        if ($number === 0) {
            return 'صفر';
        }

        $parts = [];
        $scale = 0;

        while ($number > 0) {
            $chunk = $number % 1000;

            if ($chunk > 0) {
                $words = self::threeDigits($chunk);

                if (self::SCALES[$scale] !== '') {
                    $words .= ' '.self::SCALES[$scale];
                }

                array_unshift($parts, $words);
            }

            $number = intdiv($number, 1000);
            $scale++;
        }

        return implode(' و ', $parts);
    }

    public static function tomanInWords(int $rial): string
    {
        return self::toWords((int) ($rial / 10)).' تومان';
    }

    public static function fa(int|string $number): string
    {
        return str_replace(
            ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ','],
            ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '٬'],
            (string) $number
        );
    }

    private static function threeDigits(int $number): string
    {
        $parts = [];

        $hundreds = intdiv($number, 100);
        $rest = $number % 100;

        if ($hundreds > 0) {
            $parts[] = self::HUNDREDS[$hundreds];
        }

        if ($rest >= 10 && $rest < 20) {
            $parts[] = self::TEENS[$rest - 10];
        } elseif ($rest > 0) {
            $tens = intdiv($rest, 10);
            $ones = $rest % 10;

            if ($tens > 0) {
                $parts[] = self::TENS[$tens];
            }

            if ($ones > 0) {
                $parts[] = self::ONES[$ones];
            }
        }

        return implode(' و ', $parts);
    }
}
