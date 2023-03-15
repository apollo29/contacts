<?php

namespace Contacts\Helpers;

class Headers
{

    public static function label(string $header): string
    {
        return str_replace(array("Tag:", "Check:"), "", $header);
    }

    public static function is_checkbox(string $header): bool
    {
        $has_tag = Str::starts_with($header, "Tag:");
        $has_check = Str::starts_with($header, "Check:");
        return $has_tag == true || $has_check == true;
    }
}