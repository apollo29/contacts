<?php

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Database\Query;
use Contacts\Source\Source;

require_once 'Infomail.php';

class ContenidoInfomail implements Source
{

    private Connection $connection;
    const TABLE = "infomail";

    /**
     * The constructor.
     *
     */
    public function __construct()
    {

        // Database settings
        $settings['db'] = [
            'driver' => Mysql::class,
            'encoding' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            // Enable identifier quoting
            'quoteIdentifiers' => true,
            // Set to null to use MySQL servers timezone
            'timezone' => null,
            // Disable meta data cache
            'cacheMetadata' => false,
            // Disable query logging
            'log' => false,
            // PDO options
            'flags' => [
                // Turn off persistent connections
                PDO::ATTR_PERSISTENT => false,
                // Enable exceptions
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // Emulate prepared statements
                PDO::ATTR_EMULATE_PREPARES => true,
                // Set default fetch mode to array
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Convert numeric values to strings when fetching.
                // Since PHP 8.1 integers and floats in result sets will be returned using native PHP types.
                // This option restores the previous behavior.
                PDO::ATTR_STRINGIFY_FETCHES => true,
            ],
        ];
        $settings['db']['host'] = "localhost";
        $settings['db']['database'] = "cntnd";
        $settings['db']['username'] = "root";
        $settings['db']['password'] = "";
        $connection = new Connection($settings['db']);

        $this->connection = $connection;
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

    private function fields(): array
    {
        return [
            "id",
            "vorname",
            "name",
            "strasse",
            "plz",
            "ort",
            "email",
            "meldung",
            "pifa_timestamp"
        ];
    }

    public function load(): array
    {
        $query = $this->newQuery()->from(self::TABLE);
        $query->select($this->fields())->orderAsc('pifa_timestamp');
        $data = array();
        foreach ($query->execute()->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $data[] = Newsletter::of($row);
        }
        return $data;
    }

    public function last_load(): ?string
    {
        return null;
    }

    public function name(): string
    {
        return "Contenido: Infomail";
    }

    public function archive($index): void
    {
        $query = $this->newQuery()->delete(self::TABLE);
        $query->delete()
            ->andWhere(['id' => $index])
            ->execute();
    }

    public function headers(): array
    {
        return array_keys(get_class_vars(Infomail::class));
    }
}