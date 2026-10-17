<?php

namespace Tests\Unit;

use App\Support\PersianNumbers;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PersianNumbersTest extends TestCase
{
    /**
     * @return array<string, array{int, string}>
     */
    public static function cases(): array
    {
        return [
            'zero' => [0, 'صفر'],
            'ones' => [5, 'پنج'],
            'teens' => [12, 'دوازده'],
            'tens' => [20, 'بیست'],
            'tens ones' => [21, 'بیست و یک'],
            'hundreds' => [100, 'یکصد'],
            'full three digits' => [125, 'یکصد و بیست و پنج'],
            'thousands' => [1000, 'یک هزار'],
            'complex' => [125000, 'یکصد و بیست و پنج هزار'],
            'millions' => [2500000, 'دو میلیون و پانصد هزار'],
        ];
    }

    #[DataProvider('cases')]
    public function test_to_words(int $number, string $expected): void
    {
        $this->assertSame($expected, PersianNumbers::toWords($number));
    }

    public function test_toman_in_words(): void
    {
        $this->assertSame(
            'دویست و پنجاه هزار تومان',
            PersianNumbers::tomanInWords(2500000)
        );
    }
}
