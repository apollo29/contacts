<html lang="de">
<head>
    <title>cntnd_contacts</title>

    <script
            src="https://code.jquery.com/jquery-3.6.3.min.js"
            integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU="
            crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/js-base64@3.7.5/base64.min.js"></script>

    <!-- handsontable -->
    <script type="text/javascript"
            src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/handsontable/dist/languages/de-CH.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.css"/>
    <script src="handsontable.js"></script>

    <script src="https://cdn.jsdelivr.net/gh/cntnd/core_style@1.2.0/dist/core_script.js"></script>
    <script src="app.js"></script>

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <link rel="stylesheet" href="core_style.css">
    <link rel="stylesheet" href="cntnd_contacts.css">
    <?php

    use Contacts\Spreadsheet;
    use CSVDB\Helpers\CSVConfig;

    require '../vendor/autoload.php';
    require_once 'source/ContenidoNewsletter.php';
    require_once 'source/Mailchimp.php';
    require_once 'repository/CSVDBRepository.php';
    require_once 'repository/MySQLRepository.php';

    // todo mappings
    // todo data types, required
    $required = array("Nachname", "Vorname", "E-Mail-Adresse");

    $file = __DIR__ . "/Adressdatenbank.csv";
    $config = new CSVConfig(6, "UTF-8", ";", true, true, true);
    $csvdb = new CSVDBRepository($file, $config);
    $contacts = new Spreadsheet($csvdb, $required);
    ?>
</head>

<body>

<h1>spreadsheet</h1>

<div style="width: 1024px; margin: 0 auto;">
    <script>
        const headers = <?= json_encode($contacts->headers()) ?>;
        const columns_handsontable = <?= json_encode($contacts->columns()) ?>;
        const data_handsontable = <?= $contacts->data() ?>;
    </script>
    <div class="spreadsheet__toolbar">
        <button class="material-symbols-outlined store_csv">save</button>
        <button class="material-symbols-outlined export_csv">download</button>
        <input id="search_field" type="search" placeholder="Suchen"/>
    </div>
    <div id="exampleParent">
        <div id="example"></div>
    </div>
</div>

</body>

</html>