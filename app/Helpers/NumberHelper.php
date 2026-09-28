<?php

namespace App\Helpers;

class NumberHelper
{
    public static function terbilang(int|float|string $number): string
    {
        $number = (int) $number;

        if ($number === 0) {
            return 'nol';
        }

        if ($number < 0) {
            return 'minus ' . self::terbilang(abs($number));
        }

        $units = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas',
        ];

        if ($number < 12) {
            return $units[$number];
        }

        if ($number < 20) {
            return self::terbilang($number - 10) . ' belas';
        }

        if ($number < 100) {
            return self::terbilang(intdiv($number, 10))
                . ' puluh'
                . (
                    $number % 10 > 0
                    ? ' ' . self::terbilang($number % 10)
                    : ''
                );
        }

        if ($number < 200) {
            return 'seratus'
                . (
                    $number % 100 > 0
                    ? ' ' . self::terbilang($number - 100)
                    : ''
                );
        }

        if ($number < 1000) {
            return self::terbilang(intdiv($number, 100))
                . ' ratus'
                . (
                    $number % 100 > 0
                    ? ' ' . self::terbilang($number % 100)
                    : ''
                );
        }

        if ($number < 2000) {
            return 'seribu'
                . (
                    $number % 1000 > 0
                    ? ' ' . self::terbilang($number - 1000)
                    : ''
                );
        }

        if ($number < 1_000_000) {
            return self::terbilang(intdiv($number, 1000))
                . ' ribu'
                . (
                    $number % 1000 > 0
                    ? ' ' . self::terbilang($number % 1000)
                    : ''
                );
        }

        if ($number < 1_000_000_000) {
            return self::terbilang(intdiv($number, 1_000_000))
                . ' juta'
                . (
                    $number % 1_000_000 > 0
                    ? ' ' . self::terbilang($number % 1_000_000)
                    : ''
                );
        }

        if ($number < 1_000_000_000_000) {
            return self::terbilang(intdiv($number, 1_000_000_000))
                . ' miliar'
                . (
                    $number % 1_000_000_000 > 0
                    ? ' ' . self::terbilang($number % 1_000_000_000)
                    : ''
                );
        }

        if ($number < 1_000_000_000_000_000) {
            return self::terbilang(intdiv($number, 1_000_000_000_000))
                . ' triliun'
                . (
                    $number % 1_000_000_000_000 > 0
                    ? ' ' . self::terbilang($number % 1_000_000_000_000)
                    : ''
                );
        }

        return 'angka terlalu besar';
    }
}
