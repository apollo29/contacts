<?php

namespace Contacts\Data;

class Merge
{

    /**
     * @throws \Exception
     */
    public static function merge(array $record, array $exist): array
    {
        $diff = array_diff_assoc($record, $exist);
        $contact = $exist;
        foreach ($diff as $key => $value) {
            $contact[$key] = $value;
            $contact["_diff"][$key] = $exist[$key];
        }
        return $contact;
    }
}