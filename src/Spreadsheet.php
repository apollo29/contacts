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
        $headers = $this->headers();
        $columns = [];
        foreach ($headers as $title) {
            $column["title"] = $title;
            $has_tag = stripos(strtolower($title), "tag:");
            $has_check = stripos(strtolower($title), "check:");
            $has_prefix = false;
            if ($has_tag !== false) {
                $has_prefix = $has_tag;
            }
            if ($has_check !== false) {
                $has_prefix = $has_check;
            }
            if ($has_prefix !== false && $has_prefix == 0) {
                $column["title"] = str_replace(array("Tag:", "Check:", "tag:", "check:"), "", $title);
                $column["type"] = "checkbox";
            }
            $columns[] = $column;
        }
        return json_encode($columns);
    }
}