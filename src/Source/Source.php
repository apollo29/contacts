<?php

namespace Contacts\Source;

interface Source
{
    public function load(): array;

    public function last_load(): ?string;

    public function name(): string;

    public function archive($index, $key = null): void;

    public function headers(): array;
}