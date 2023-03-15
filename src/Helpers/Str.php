<?php

namespace Contacts\Helpers;

class Str
{
    public static function starts_with(string $haystack, string $needle): bool
    {
        if (!function_exists('str_starts_with')) {
            return (stripos($haystack, $needle)===0);
        } else {
            return str_starts_with($haystack, $needle);
        }
    }
}