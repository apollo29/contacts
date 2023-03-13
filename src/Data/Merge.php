<?php

namespace Contacts\Data;

use Selective\ArrayReader\ArrayReader;

class Merge
{

    /**
     * @throws \Exception
     */
    public static function merge(Data $record, array $exist): array
    {
        $diff = array_diff_assoc($record->record(), $exist);
        $contact = Mapping::to_data_array($exist);

        $reader = new ArrayReader($exist);
        foreach ($diff as $key => $value) {
            if ($reader->exists($key)) {
                $contact[$key] = $value;
                $contact["_diff"][$key] = $reader->find($key);
            }
        }
        return $contact;
    }
}