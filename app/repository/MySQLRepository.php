<?php


use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Database\Query;
use Contacts\Data\Mapping;
use Contacts\Repository\Repository;

class MySQLRepository extends Repository
{
    private Connection $connection;
    const TABLE = "adressdatenbank_test";

    public function __construct($connection)
    {
        // Database settings
        $settings['db'] = [
            'driver' => Mysql::class,
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'quoteIdentifiers' => true,
            'timezone' => null,
            'cacheMetadata' => false,
            'log' => false,
            // PDO options
            'flags' => [
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => true,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_STRINGIFY_FETCHES => true
            ],
        ];

        $settings['db']['host'] = $connection['host'];
        $settings['db']['database'] = $connection['database'];
        $settings['db']['username'] = $connection['user'];
        $settings['db']['password'] = $connection['password'];
        $this->connection = new Connection($settings['db']);
    }

    /**
     * Create a new query.
     *
     * @return Query The query
     */
    private function newQuery(): Query
    {
        return $this->connection->newQuery();
    }

    /**
     * Create a new select query.
     *
     * @return Query The query
     */
    private function newSelect(): Query
    {
        return $this->connection->newQuery()->from(self::TABLE)->select($this->select_columns());
    }

    private function columns(): array
    {
        return [
            'id',
            'name',
            'vorname',
            'strasse',
            'ort',
            'plz',
            'telefon',
            'email',
            'geburtstag',
            'check:infomail_spontan',
            'check:newsletter',
            'tag:freunde',
            'tag:kollegen',
            'tag:nachbarn',
            'tag:blwl',
            'tag:bergsportunternehmen',
            'tag:geschaeftskollegen',
            'tag:dienstleister',
            'tag:basket',
            'tag:mpa',
            'tag:sac_birehubel'
        ];
    }

    private function select_columns(): array
    {
        return array_values($this->mapping_columns());
    }

    public function mapping_columns(): array
    {
        return [
            'name' => 'name',
            'vorname' => 'vorname',
            'strasse' => 'strasse',
            'ort' => 'ort',
            'plz' => 'plz',
            'telefon' => 'telefon',
            'email' => 'email',
            'geburtstag' => 'geburtstag',
            'infomail_spontan' => 'check:infomail_spontan',
            'newsletter' => 'check:newsletter',
            'freunde' => 'tag:freunde',
            'kollegen' => 'tag:kollegen',
            'nachbarn' => 'tag:nachbarn',
            'blwl' => 'tag:blwl',
            'bergsportunternehmen' => 'tag:bergsportunternehmen',
            'geschaeftskollegen' => 'tag:geschaeftskollegen',
            'dienstleister' => 'tag:dienstleister',
            'basket' => 'tag:basket',
            'mpa' => 'tag:mpa',
            'sac_birehubel' => 'tag:sac_birehubel'
        ];
    }

    public function headers(): array
    {
        return $this->select_columns();
    }

    public function contacts(): array
    {
        $query = $this->newSelect();
        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function history(): array
    {
        return array();
    }

    public function has_history(): bool
    {
        return false;
    }

    public function index(): string
    {
        return "email";
    }

    // todo
    public function data_types(): array
    {
        $query = $this->newSelect();

        $query->getTypeMap();

        return array();
    }

    public function upsert(array $contact): void
    {
        $data = $this->to_record($contact);
        $exist = $this->exists($contact[$this->index()]);
        if (!$exist[0]) {
            // insert
            $query = $this->newQuery()->insert($this->columns());
            $query->into(self::TABLE)
                ->values($data)
                ->execute();
        } else {
            // update
            $query = $this->newQuery()->update(self::TABLE);
            $query->set($data)
                ->andWhere([$this->index() => $contact[$this->index()]])
                ->execute();
        }
    }

    public function delete($index): void
    {
        $query = $this->newQuery()->delete(self::TABLE);
        $query->andWhere([$this->index() => $index])
            ->execute();
    }

    public function delete_where(array $where): void
    {
        $where_mapped = Mapping::where_stmt($where, $this->mapping_columns());

        $sql = "DELETE FROM :table WHERE ";
        foreach ($where_mapped as $clause) {
            $key = key($clause);
            $sql .= $key . "=:" . $key . " AND ";
        }
        $sql = substr($sql, 0, -4);

        $query = $this->newQuery()->delete(self::TABLE);
        $query->andWhere($where_mapped)
            ->execute();
    }

    public function exists($index): array
    {
        $query = $this->newSelect()->andWhere([$this->index() => $index]);
        $exist = $query->execute()->fetch('assoc');
        if (!$exist) {
            return array();
        }
        return $exist;
    }

    public function dump(string $records): void
    {
        // TODO: Implement dump() method.
    }
}