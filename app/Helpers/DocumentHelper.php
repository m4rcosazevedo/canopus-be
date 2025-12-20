<?php

namespace App\Helpers;

class DocumentHelper
{
    public static function isCpfValid(string $cpf): bool
    {
        $cpf = self::onlyNumbers($cpf);

        if (!self::hasLength($cpf, 11)) {
            return false;
        }

        if (self::hasRepeatedDigits($cpf)) {
            return false;
        }

        if (self::isKnownInvalidCpf($cpf)) {
            return false;
        }

        return self::validateCpfDigits($cpf);
    }

    public static function isCnpjValid(string $cnpj): bool
    {
        $cnpj = self::onlyNumbers($cnpj);

        if (!self::hasLength($cnpj, 14)) {
            return false;
        }

        if (self::hasRepeatedDigits($cnpj)) {
            return false;
        }

        return self::validateCnpjDigits($cnpj);
    }

    /* ==========================
       Métodos compartilhados
    ========================== */

    private static function onlyNumbers(string $value): string
    {
        return preg_replace('/\D/', '', $value);
    }

    private static function hasLength(string $value, int $length): bool
    {
        return strlen($value) === $length;
    }

    private static function hasRepeatedDigits(string $value): bool
    {
//        return preg_match('/(\d)\1+/', $value) === 1;
        return preg_match('/(\d)\1{13}/', $value);
    }

    /* ==========================
       CPF
    ========================== */

    private static function isKnownInvalidCpf(string $cpf): bool
    {
        return in_array($cpf, [
            "12345678909",
            "98765432100",
            "01234567890",
            "11122233344",
            "44455566677",
            "12121212121",
            "00000000191",
        ], true);
    }

    private static function validateCpfDigits(string $cpf): bool
    {
        for ($position = 9; $position < 11; $position++) {
            $sum = 0;

            for ($i = 0; $i < $position; $i++) {
                $sum += (int) $cpf[$i] * (($position + 1) - $i);
            }

            $digit = ((10 * $sum) % 11) % 10;

            if ((int) $cpf[$position] !== $digit) {
                return false;
            }
        }

        return true;
    }

    /* ==========================
       CNPJ
    ========================== */

    private static function validateCnpjDigits(string $cnpj): bool
    {
        $weightsFirst = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weightsSecond = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        if (!self::validateCnpjDigit($cnpj, $weightsFirst, 12)) {
            return false;
        }

        if (!self::validateCnpjDigit($cnpj, $weightsSecond, 13)) {
            return false;
        }

        return true;
    }

    private static function validateCnpjDigit(string $cnpj, array $weights, int $length): bool
    {
        $sum = 0;

        for ($i = 0; $i < $length; $i++) {
            $sum += (int) $cnpj[$i] * $weights[$i];
        }

        $remainder = $sum % 11;
        $digit = $remainder < 2 ? 0 : 11 - $remainder;

        return (int) $cnpj[$length] === $digit;
    }
}
