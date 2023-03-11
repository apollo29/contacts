<?php

namespace Contacts\Data;

class Mapping
{

    public static string $default_string = "";
    public static bool $default_bool = false;

    public static function bool_to_x(bool $val): string
    {
        return ($val) ? "x" : "";
    }

    public static function x_to_bool(string $val): bool
    {
        return $val == "x";
    }
}