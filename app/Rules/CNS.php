<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CNS implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cns = preg_replace('/\D/', '', (string)$value);
        $firstDigit = (int)$cns[0];

        if (strlen($cns) !== 15 || preg_match("/^{$firstDigit}{15}$/", $cns) > 0) {
            $fail('O campo :attribute não é um CNS válido.');
        }

        if ($firstDigit >= 7) {
            $this->cnsProv($cns, $fail);
            return;
        }

        $this->cns($cns, $fail);
    }

    public function cns(string $cns, Closure $fail): void
    {
        $pis = substr($cns, 0, 11);

        for ($soma = 0, $i = 0, $j = 15; $i <= 10; $i++, $j--) {
            $soma += (int) $pis[$i] * $j;
        }

        $dv = 11 - ($soma % 11);

        if ($dv === 11) {
            $dv = 0;
        }

        if ($dv === 10) {
            $resultado = sprintf("%s001%d", $pis, $dv);
        } else {
            $resultado = sprintf("%s000%d", $pis, $dv);
        }

        if ($cns !== $resultado) {
            $fail('O campo :attribute não é um CNS válido.');
        }
    }

    public function cnsProv(string $cns, Closure $fail): void
    {
        for ($s = 0, $i = 0, $j = 15; $i < 15; $i++, $j--) {
            $s += (int) $cns[$i] * $j;
        }

        if($s % 11 !== 0) {
            $fail('O campo :attribute não é um CNS válido.');
        }
    }
}
