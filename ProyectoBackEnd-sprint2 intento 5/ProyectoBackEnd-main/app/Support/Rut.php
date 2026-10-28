<?php

namespace App\Support;

/**
 * Utilidades para RUT chileno (módulo 11).
 */
class Rut
{
    /** Deja solo dígitos y K en mayúscula. */
    public static function clean(?string $rut): string
    {
        return strtoupper(preg_replace('/[^0-9kK]/', '', (string) $rut));
    }

    /** Calcula el dígito verificador de la parte numérica. */
    public static function dv(string $body): string
    {
        $sum = 0;
        $mul = 2;

        for ($i = strlen($body) - 1; $i >= 0; $i--) {
            $sum += (int) $body[$i] * $mul;
            $mul = $mul === 7 ? 2 : $mul + 1;
        }

        $res = 11 - ($sum % 11);

        return match ($res) {
            11 => '0',
            10 => 'K',
            default => (string) $res,
        };
    }

    public static function isValid(?string $rut): bool
    {
        $clean = self::clean($rut);

        if (strlen($clean) < 8 || strlen($clean) > 9) {
            return false;
        }

        $body = substr($clean, 0, -1);
        $dv = substr($clean, -1);

        return ctype_digit($body) && self::dv($body) === $dv;
    }

    /** Formato 12.345.678-5 (si no es válido, devuelve el texto original). */
    public static function format(?string $rut): ?string
    {
        if ($rut === null || trim($rut) === '') {
            return $rut;
        }

        if (! self::isValid($rut)) {
            return $rut;
        }

        $clean = self::clean($rut);

        return number_format((int) substr($clean, 0, -1), 0, ',', '.').'-'.substr($clean, -1);
    }

    /** Genera un RUT válido con formato a partir de un número (útil para seeders). */
    public static function make(int $body): string
    {
        return self::format($body.self::dv((string) $body));
    }
}
