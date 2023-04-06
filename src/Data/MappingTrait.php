<?php

namespace Contacts\Data;

use Selective\ArrayReader\ArrayReader;

trait MappingTrait
{
    public static string $default_string = "";
    public static float $default_float = 0;
    public static int $default_int = 0;
    public static bool $default_bool = false;

    public function to_record(array $data, array $mapping_columns = null): array
    {
        $reader = new ArrayReader($data);
        $headers = $this->data_types();
        $mapping = $mapping_columns ?: $this->reverse_mapping_columns();
        $record = array();
        foreach ($headers as $key => $type) {
            if (array_key_exists($key, $mapping)) {
                $record[$key] = $this->find($reader, $mapping[$key], $type, false);
            }
        }
        return $record;
    }

    private function reverse_mapping_columns(array $mapping_columns = null): array
    {
        $columns = $mapping_columns ?: $this->mapping_columns();
        $mapping = array();
        foreach ($columns as $key => $value) {
            $mapping[$value] = $key;
        }
        return $mapping;
    }

    private function find(ArrayReader $reader, string $key, string $type, bool $is_record)
    {
        switch ($type) {
            case "float":
                return $reader->findFloat($key, self::$default_float);
            case "integer":
                return $reader->findInt($key, self::$default_int);
            case "boolean":
                return $this->findBool($reader, $key, $is_record);
            default:
                return $reader->findString($key, self::$default_string);
        }
    }

    private function findBool(ArrayReader $reader, string $key, bool $is_record)
    {

        if ($is_record) {
            return self::x_to_bool($reader->findString($key, self::$default_string));
        }
        return self::bool_to_x($reader->findBool($key, self::$default_bool));
    }

    private static function bool_to_x(bool $val): string
    {
        return ($val) ? "x" : "";
    }

    private static function x_to_bool(string $val): bool
    {
        return $val == "x";
    }
}