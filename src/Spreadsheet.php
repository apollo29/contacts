<?php

namespace Contacts;

class Spreadsheet extends Contacts
{
    public function data(): string
    {
        return json_encode($this->contacts());
    }

    public function columns(): array
    {
        $types = $this->data_types();
        $columns = [];
        foreach ($types as $title => $type) {
            unset($column);
            $column["data"] = $title;
            if ($type === "boolean") {
                $column["type"] = "checkbox";
                $column["checkedTemplate"] = "x";
                $column["uncheckedTemplate"] = "";
            }
            $columns[] = $column;
        }
        return $columns;
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

    public function make_input(string $name, string $label, string $type = "text", bool $required = false, string $value = null): string
    {
        $is_required = ($required) ? "required" : "";
        $select = '<div class="form-group w-50">' . "\n";
        $select .= '<label for="' . $name . '">' . $label . '</label>' . "\n";
        $select .= '<input name="' . $name . '" id="' . $name . '" type="' . $type . '" value="' . $value . '" ' . $is_required . ' />' . "\n";
        $select .= '</div>' . "\n";
        return $select;
    }

    public function make_checkbox(string $name, string $label, bool $required = false, bool $checked = false): string
    {
        $is_required = ($required) ? "required" : "";
        $is_checked = ($checked) ? "checked" : "";
        $select = '<div class="form-check form-check-inline">' . "\n";
        $select .= '<input class="form-check-input" name="' . $name . '" id="' . $name . '" type="checkbox" value="x" ' . $is_checked . ' ' . $is_required . '/>' . "\n";
        $select .= '<label for="' . $name . '">' . $label . '</label>' . "\n";
        $select .= '</div>' . "\n";
        return $select;
    }
}