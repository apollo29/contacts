<?php


use CSVDB\Helpers\CSVConfig;

require '../vendor/autoload.php';

$file = __DIR__ . "\Adressdatenbank.csv";
$config = new CSVConfig(9, "UTF-8", ";", true, true, false);
$repository = new CSVDBRepository($file, $config);

$headers = [
    'id',
    'vorname',
    'name',
    'strasse',
    'plz',
    'ort',
    'land',
    'telefon_geschaeftlich',
    'telefon',
    'mobile',
    'email',
    'email_2',
    'check:infomail_spontan',
    'check:newsletter',
    'tag:familie',
    'tag:freunde',
    'tag:kollegen',
    'tag:nachbarn',
    'tag:wanderleiter',
    'tag:bergsportunternehmen',
    'tag:geschaeftskollegen',
    'tag:dienstleister',
    'tag:linkedin',
    'tag:unternehmen',
    'tag:organisationen'
];

/*
 * CREATE TABLE Persons (
    PersonID int,
    LastName varchar(255),
    FirstName varchar(255),
    Address varchar(255),
    City varchar(255)
);
 */

echo '<textarea style="width: 600px; height: 400px;">';
echo 'CREATE TABLE adressdatenbank (' . "\n";
foreach ($headers as $header) {
    echo '"'.$header . '" varchar(255),' . "\n";
}
echo ');' . "\n";
echo '</textarea>';

$data = $repository->contacts();
$fields="";
foreach ($headers as $header) {
    $fields .= '`' . $header . '`,';
}
$fields = substr($fields, 0,-1);
echo '<textarea style="width: 600px; height: 400px;">';
$i=0;
foreach ($data as $record) {
    //if (!empty($record['E-Mail'])) {
        if ($i == 10) {
            $i = 0;
            echo 'COMMIT;';
        }
        echo 'INSERT INTO adressdatenbank_test (' . "\n";
        echo $fields;
        echo ') VALUES (' . "\n";
        // id
        $values = "NULL,";
        foreach ($record as $value) {
            if (is_numeric($value)) {
                $values .= $value . ',';
            } else {
                $values .= '"' . $value . '",';
            }
        }
        $values = substr($values, 0, -1);
        echo $values;
        echo ');' . "\n";
        $i++;
    //}
}
echo '</textarea>';