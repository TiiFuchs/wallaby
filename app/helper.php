<?php

function parseFloat(string $number, string $decimal_separator = ',', string $thousands_separator = '.'): float
{
    $number = str_replace([$thousands_separator, $decimal_separator], ['', '.'], $number);

    return floatval($number);
}
