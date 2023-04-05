<?php

namespace Contacts;

class Spreadsheet extends Contacts
{
    public function data(): string
    {
        return json_encode($this->contacts());
    }

    public function columns(): string
    {
        $types = $this->data_types();
        $columns = [];
        foreach ($types as $title => $type) {
            $column["title"] = $title;
            if ($type == "boolean") {
                $column["type"] = "checkbox";
            }
            $columns[] = $column;
        }
        return json_encode($columns);
    }

    public function make_select_assoc(array $data, string $name, string $label, string $value = null): string
    {
        $select = '<div class="form-group">' . "\n";
        $select .= '<label for="' . $name . '">' . $label . '</label>' . "\n";
        $select .= '<select name="' . $name . '" id="' . $name . '" size="1">' . "\n";
        $select .= '<option value=""> - </option>' . "\n";
        foreach ($data as $key => $record) {
            $selected = (!empty($value) && $key == $value) ? "selected" : "";
            $select .= '<option ' . $selected . ' value="' . $key . '">' . $record . '</option>' . "\n";
        }
        $select .= '</select>' . "\n";
        $select .= '</div>' . "\n";
        return $select;
    }

    public function make_select(array $data, string $name, string $label, string $value = null): string
    {
        $select = '<div class="form-group">' . "\n";
        $select .= '<label for="' . $name . '">' . $label . '</label>' . "\n";
        $select .= '<select name="' . $name . '" id="' . $name . '" size="1">' . "\n";
        $select .= '<option value=""> - </option>' . "\n";
        foreach ($data as $record) {
            $selected = (!empty($value) && $record == $value) ? "selected" : "";
            $select .= '<option ' . $selected . ' value="' . $record . '">' . $record . '</option>' . "\n";
        }
        $select .= '</select>' . "\n";
        $select .= '</div>' . "\n";
        return $select;
    }

    public function make_input(string $name, string $label, string $type = "text", string $required = "", string $value = null): string
    {
        $select = '<div class="form-group w-50">' . "\n";
        $select .= '<label for="' . $name . '">' . $label . '</label>' . "\n";
        $select .= '<input name="' . $name . '" id="' . $name . '" type="' . $type . '" value="' . $value . '" ' . $required . ' />' . "\n";
        $select .= '</div>' . "\n";
        return $select;
    }

    public function make_checkbox(string $name, string $label, string $required = "", bool $checked = false): string
    {
        $select = '<div class="form-check form-check-inline">' . "\n";
        $select .= '<input class="form-check-input" name="' . $name . '" id="' . $name . '" type="checkbox" value="x" ' . $required . '/>' . "\n";
        $select .= '<label for="' . $name . '">' . $label . '</label>' . "\n";
        $select .= '</div>' . "\n";
        return $select;
    }
}