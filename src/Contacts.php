<?php

namespace Contacts;

use Contacts\Data\Contact;
use Contacts\Data\Data;
use Contacts\Data\Merge;
use Contacts\Repository\Repository;
use Contacts\Source\Source;

class Contacts
{
    private Repository $repository;
    private array $sources = array();
    private array $source_data = array();
    private array $data_types;
    private array $data_required;

    const NEW = "new";
    const UPDATE = "update";
    const DELETE = "delete";
    const DUMP = "dump";

    public function __construct(Repository $repository, array $data_required = array())
    {
        $this->repository = $repository;
        $this->data_types = $repository->data_types();
        $this->data_required = $data_required;
    }

    public function contacts(): array
    {
        return $this->repository->contacts();
    }

    public function headers(): array
    {
        return $this->repository->headers();
    }

    public function data_types(): array
    {
        if (empty($this->data_types)) {
            $this->update_data_types($this->repository->data_types());
        }
        return $this->data_types;
    }

    public function update_data_types(array $data_types): void
    {
        $this->data_types = $data_types;
    }

    public function data_required(): array
    {
        return $this->data_required;
    }

    public function update_data_required(array $data_required): void
    {
        $this->data_required = $data_required;
    }

    public function history(): array
    {
        return $this->repository->history();
    }

    public function has_history(): bool
    {
        return $this->repository->has_history();
    }

    // Sources

    public function add_source(Source ...$sources): void
    {
        foreach ($sources as $source) {
            $this->sources[get_class($source)] = $source;
        }
    }

    public function load_sources(bool $force = false): array
    {
        if ($force) {
            $this->count_sources();
        }
        return $this->source_data;
    }

    public function count_sources(): int
    {
        $count = 0;
        $data = array();
        foreach ($this->sources as $source) {
            if ($source instanceof Source) {
                $load = $source->load();
                $count += count($load);
                $data[get_class($source)] = array("name" => $source->name(), "data" => $load);
            }
        }
        $this->source_data = $data;
        return $count;
    }

    public function sources_headers(): array
    {
        $data = array();
        foreach ($this->sources as $source) {
            if ($source instanceof Source) {
                $data[get_class($source)] = $source->headers();
            }
        }
        return $data;
    }

    public function index(): string
    {
        return $this->repository->index();
    }

    // CRUD

    public function upsert_source(array $contact, string $source, $index): void
    {
        $this->upsert($contact);
        $repository = $this->sources[$source];
        if ($repository instanceof Source) {
            $repository->archive($index);
        }
    }

    public function upsert(array $contact): void
    {
        $this->repository->upsert($contact);
    }

    public function dump(string $records): void
    {
        $this->repository->dump($records);
    }

    public function delete($criterias): void
    {
        if (is_array($criterias)) {
            foreach ($criterias as $criteria) {
                $this->repository->delete_where($criteria);
            }
        } else {
            $this->repository->delete($criterias);
        }
    }

    public function delete_source(string $source, $index): void
    {
        $repository = $this->sources[$source];
        if ($repository instanceof Source) {
            $repository->archive($index);
        }
    }

    public function exists($index): array
    {
        return $this->repository->exists($index);
    }

    // Merge

    /**
     * @throws \Exception
     */
    public function merge(Data $record, array $exist, array $mapping_columns): array
    {
        if (isset($exist[0])) {
            throw new \Exception('Existing Record is not an associative array.');
        }
        $contact = $this->repository->to_record($record->record(), $mapping_columns);
        if (count($exist) == 0) {
            return $contact;
        } else {
            return Merge::merge($contact, $exist);
        }
    }

    public function contact_headers(): array
    {
        return array_keys(get_class_vars(Contact::class));
    }
}