<?php

use CSVDB\Helpers\CSVConfig;

require '../vendor/autoload.php';
require_once 'repository/CSVDBRepository.php';

$file = __DIR__ . "/Adressdatenbank.csv";
$config = new CSVConfig(9, "UTF-8", ";", true, true, true);
$csvdb = new CSVDBRepository($file, $config);

echo "<pre>";
var_dump($csvdb->data_types());
echo "</pre>";