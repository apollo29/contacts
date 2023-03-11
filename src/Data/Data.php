<?php

namespace Contacts\Data;

interface Data
{
    public static function of(array $data): Data;

    public function record(): array;

    public function identifier(): string;

    public function timestamp(): ?string;

    public function index();
}