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
        $has_tag = stripos($header, "Tag:");
        $has_check = stripos($header, "Check:");
        return $has_tag === 0 || $has_check === 0;
    }
}