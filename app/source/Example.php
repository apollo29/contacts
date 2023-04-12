<?php

use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Database\Query;
use Contacts\Source\Source;
use CSVDB\CSVDB;
use CSVDB\Helpers\CSVConfig;

class Example implements Source
{

    private Connection $connection;
    private CSVDB $csvdb;

    const TABLE = "infomail";

    /**
     * The constructor.
     *
     */
    public function __construct(string $dir = __DIR__)
    {
        // setup
        $this->setup_source($dir);

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

    private function setup_source(string $dir): void
    {
        $file = $dir . "/sources.csv";
        if (!file_exists($file)) {
            $fp = fopen($file, 'w');
            fputcsv($fp, ["source", "last_load"]);
            fclose($fp);
        }

        try {
            $config = new CSVConfig(CSVConfig::INDEX, CSVConfig::ENCODING, CSVConfig::DELIMITER, CSVConfig::HEADERS, false);
            $this->csvdb = new CSVDB($file, $config);
        } catch (Exception $exception) {
            var_dump($exception);
        }
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
        $query->select($this->fields());
        $last_load = $this->last_load();
        if (!empty($last_load)) {
            $query->andWhere(['pifa_timestamp >' => $last_load]);
        }
        $query->orderAsc('pifa_timestamp');
        $data = array();
        foreach ($query->execute()->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $data[] = Newsletter::of($row);
        }
        $this->update_last_load();
        return $data;
    }

    private function update_last_load(): void
    {
        try {
            $this->csvdb->upsert(["source" => get_class($this), "last_load" => time()], ["source" => get_class($this)]);
        } catch (Exception $exception) {
            var_dump($exception);
        }
    }

    public function last_load(): ?string
    {
        try {
            $last_load = $this->csvdb->select(["last_load"])->where(["source" => get_class($this)])->get();
            if (count($last_load) > 0) {
                return date('Y-m-d H:i:s', $last_load[0]["last_load"]);
            }
        } catch (Exception $exception) {
            var_dump($exception);
            return null;
        }
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
}