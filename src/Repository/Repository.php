<?php

namespace Contacts\Repository;

use Contacts\Data\Contact;
use Contacts\Data\Data;

interface Repository
{
    public function contacts(): array;

    public function history(): array;

    public function has_history(): bool;

    public function index(): string;

    // CRUD

    public function upsert(Contact $contact): void;

    public function update(Contact $contact, array $where): void;

    public function delete($index): void;

    public function delete_where(array $where): void;

    public function exists($index): array;

    // Mapping

    public function mapping_columns(): array;

    public function to_data(array $record): array;

    public function convert(Data $record): array;
}