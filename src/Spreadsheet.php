<?php

namespace Contacts;

use Contacts\Helpers\Headers;

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
            if (Headers::is_checkbox($title)) {
                $column["title"] = Headers::label($title);
                $column["type"] = "checkbox";
            }
            $columns[] = $column;
        }
        return json_encode($columns);
    }
}