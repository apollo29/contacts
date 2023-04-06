<?php

namespace Contacts\Data;

class Mapping
{

    public static string $default_string = "";
    public static bool $default_bool = false;

    public static function stripslashes(array $data): array
    {
        foreach ($data as $key => $value) {
            $data[$key] = stripslashes($value);
        }
        return $data;
    }

    public static function where_stmt(array $where, array $mapping_columns): array
    {
        $map = array();
        if (!isset($update[0])) {
            foreach ($where as $stmt) {
                $map[] = self::map_where_stmt($stmt, $mapping_columns);
            }
        } else {
            $map = self::map_where_stmt($where, $mapping_columns);
        }
        return $map;
    }

    private static function map_where_stmt(array $where, array $mapping_columns): array
    {
        $map = array();
        foreach ($where as $key => $value) {
            $map[$mapping_columns[$key]] = $value;
        }
        return $map;
    }

    public static function with(array $data, array $header): array
    {
        if (count($data) != count($header)) {
            throw new \Exception('Data and Header are not of identical size. Given: ' . count($header) . ', expected: ' . count($data));
        }

        $array = array();
        for ($i = 0; $i < count($header); $i++) {
            $array[$header[$i]] = $data[$i];
        }
        return $array;
    }
}