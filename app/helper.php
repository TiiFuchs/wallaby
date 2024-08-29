<?php

namespace App;

function parse_float(string $number, string $decimal_separator = ',', string $thousands_separator = '.'): float
{
    $number = str_replace([$thousands_separator, $decimal_separator], ['', '.'], $number);

    return floatval($number);
}

function asn1_integer(string $value): string
{
    if (ord($value[0]) & 0x80) {
        $value = "\0".$value;
    }

    return "\x02".chr(strlen($value)).$value;
}

function asn1_das_signature(string $r, string $s): string
{
    $encodedR = asn1_integer($r);
    $encodedS = asn1_integer($s);

    return "\x30".chr(strlen($encodedR) + strlen($encodedS)).$encodedR.$encodedS;
}
