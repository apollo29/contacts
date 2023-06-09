<?php

namespace Contacts\Repository;

use Contacts\Data\MappingTrait;

abstract class Repository
{
    use MappingTrait;

    public abstract function headers(): array;

    public abstract function contacts(): array;

    public abstract function history(): array;

    public abstract function has_history(): bool;

    public abstract function index(): string;

    public abstract function data_types(): array;

    // CRUD

    public abstract function upsert(array $contact): void;

    public abstract function delete($index): void;

    public abstract function delete_where(array $where): void;

    public abstract function exists($index): array;

    public abstract function dump(string $records): void;

    // Mapping

    public abstract function mapping_columns(): array;
}