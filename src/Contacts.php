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
    private array $headers;

    const NEW = "new";
    const UPDATE = "update";
    const DELETE = "delete";

    public function __construct(Repository $repository, array $headers)
    {
        $this->repository = $repository;
        $this->headers = $headers;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function contacts(): array
    {
        return $this->repository->contacts();
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
                $count += count($source->load());
                $data[get_class($source)] = array("name" => $source->name(), "data" => $source->load());
            }
        }
        $this->source_data = $data;
        return $count;
    }

    public function index(): string
    {
        return $this->repository->index();
    }

    // CRUD

    public function upsert_source(Contact $contact, string $source, $index): void
    {
        $this->upsert($contact);
        $repository = $this->sources[$source];
        if ($repository instanceof Source) {
            $repository->archive($index);
        }
    }

    public function upsert(Contact $contact): void
    {
        $this->repository->upsert($contact);
    }

    /**
     * @throws \Exception
     */
    public function update(array $records): void
    {
        echo "<pre>";
        /*
         * create record from $this->headers;
        foreach ($wheres as $key => $where) {
            $record = $records[$key];
            $this->repository->update(Contact::from($record), $where);
        }
        */
        echo "</pre>";
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
    public function merge(Data $record, array $exist): array
    {
        if (isset($exist[0])) {
            throw new \Exception('Existing Record is not an associative array.');
        }

        if (count($exist) == 0) {
            return $record->record();
        } else {
            $contact = $this->repository->to_data($exist);
            return Merge::merge($record, $contact);
        }
    }
}