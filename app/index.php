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

    use Contacts\Contacts;
    use Contacts\Data\Data;
    use Contacts\Data\Mapping;
    use Contacts\Spreadsheet;
    use CSVDB\Helpers\CSVConfig;

    require '../vendor/autoload.php';
    require_once 'source/ContenidoNewsletter.php';
    require_once 'source/Mailchimp.php';
    require_once 'repository/CSVDBRepository.php';
    require_once 'repository/MySQLRepository.php';

    function unescapeData($string, $decode_specialchars = true)
    {
        $base64 = utf8_encode(base64_decode($string));
        var_dump(urlencode($base64));

        if ($decode_specialchars) {
            $base64 = htmlspecialchars_decode($base64, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
        }
        //$decode = json_decode($base64, true);
        //return $decode;
        return urlencode($base64);
    }

    // todo mappings
    // todo data types, required
    $required = array("Nachname", "Vorname", "E-Mail-Adresse");
    $mappings[ContenidoNewsletter::class] = array("Nachname" => "name", "Vorname" => "vorname", "Strasse" => "strasse", "PLZ" => "plz", "Ort" => "ort", "E-Mail-Adresse" => "email", "Infomail Spontan" => "infomail_spontan", "Newsletter" => "newsletter");
    $mappings[Mailchimp::class] = array("Nachname" => "name", "Vorname" => "vorname", "E-Mail-Adresse" => "email", "Newsletter" => "newsletter");

    $file = __DIR__ . "/Adressdatenbank.csv";
    $config = new CSVConfig(6, "UTF-8", ";", true, true, true);
    $csvdb = new CSVDBRepository($file, $config);
    $contacts = new Spreadsheet($csvdb, $required);
    // todo mappings to class etc.
    // todo data types

    // sources
    $newsletter = new ContenidoNewsletter();
    $mailchimp = new Mailchimp(__DIR__ . "/mailchimp");
    $contacts->add_source($newsletter, $mailchimp);

    echo "<pre>";
    //var_dump($contacts->columns());
    if ($_POST) {
        if (array_key_exists('editor_form_action', $_POST)) {
            // Dashbord & Editor
            if ($_POST['editor_form_action'] == Contacts::NEW || $_POST['editor_form_action'] == Contacts::UPDATE) {
                $data = Mapping::stripslashes($_POST['data']);
                if (!empty($_POST['editor_form_source']) && !empty($_POST['editor_form_index'])) {
                    $contacts->upsert_source($data, $_POST['editor_form_source'], $_POST['editor_form_index']);
                } else {
                    //$contacts->upsert($data);
                }
            } elseif ($_POST['editor_form_action'] == Contacts::DELETE) {
                if (!empty($_POST['editor_form_source']) && !empty($_POST['editor_form_index'])) {
                    $contacts->delete_source($_POST['editor_form_source'], $_POST['editor_form_index']);
                } else if (!empty($_POST['editor_form_delete'])) {
                    $data = json_decode(base64_decode($_POST['editor_form_delete']), true);
                    $contacts->delete($data);
                }
            }
        } elseif (array_key_exists('addresses_form_action', $_POST)) {
            // Addresses
            if ($_POST['addresses_form_action'] == Contacts::DUMP) {
                $records = base64_decode($_POST['addresses_form_data']);
                $contacts->dump($records);
            }
        } elseif (array_key_exists('mapping_form_action', $_POST)) {
            // Mappings
            // todo store and update
            var_dump($_POST['data']);
        }
    }
    echo "</pre>";

    // load sources
    $count_sources = $contacts->count_sources();
    // mappings
    $mapping_count = array_filter($contacts->data_types(), function ($value) {
        return !is_string($value);
    });
    ?>
</head>

<body>

<h1>spreadsheet</h1>

<pre></pre>

<div style="width: 1024px; margin: 0 auto;">
    <ul class="tabs" id="contacts">
        <li class="tabs__tab <?= ($count_sources > 0) ? "active" : "" ?>" data-toggle="tabs"
            data-target="contacts__content--dashboard">
            <span class="tabs__tab--link">Dashboard <?= ($count_sources > 0) ? "(" . $count_sources . ")" : "" ?></span>
        </li>
        <li class="tabs__tab <?= ($count_sources == 0) ? "active" : "" ?>" data-toggle="tabs"
            data-target="contacts__content--contacts">
            <span class="tabs__tab--link">Adressen</span>
        </li>
        <li class="tabs__tab" data-toggle="tabs"
            data-target="contacts__content--mappings">
            <span class="tabs__tab--link">Mappings <?= (count($mapping_count) > 0) ? "(" . count($mapping_count) . ")" : "" ?></span>
        </li>
        <li class="tabs__tab <?= ($contacts->has_history()) ? "" : "disabled" ?>" data-toggle="tabs"
            data-target="contacts__content--history">
            <span class="tabs__tab--link">History</span>
        </li>
    </ul>

    <div class="tabs__content" id="contacts__content">
        <div class="tabs__content--pane fade <?= ($count_sources > 0) ? "active" : "" ?>"
             id="contacts__content--dashboard">
            <h2>Dashboard <a href="#editor" class="header__action">neuer Eintrag</a></h2>

            <h3>Neue Einträge: <?= $count_sources ?></h3>
            <?php
            $sources = $contacts->load_sources();
            $has_entries = false;
            foreach ($sources as $source => $records) {
                foreach ($records["data"] as $record) {
                    $has_entries = true;
                    if ($record instanceof Data) {
                        $uuid = rand();
                        $exist = $contacts->exists($record->email);
                        $base64 = base64_encode(json_encode($contacts->merge($record, $exist, $mappings[$source])));
                        ?>
                        <div class="card">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <strong class="w-25"><?= $record->identifier() ?></strong>
                                <span class="w-25"><?= $record->email ?></span>
                                <span class="w-auto">Quelle <?= $records["name"] ?></span>
                                <span class="w-auto"><?= date('d.m.Y H:i:s', strtotime($record->timestamp())) ?></span>
                                <span class="w-auto">
                                <!-- data -->
                                <input type="hidden" id="contact_<?= $uuid ?>" name="contact_<?= $uuid ?>"
                                       value="<?= $base64 ?>"/>
                                <span class="material-symbols-outlined add_contact"
                                      data-contact="contact_<?= $uuid ?>"
                                      data-action="<?= (count($exist) > 0) ? "update" : "new" ?>"
                                      data-source="<?= $source ?>"
                                      data-index="<?= $record->index() ?>">archive</span>
                                <span class="material-symbols-outlined remove_contact"
                                      data-source="<?= $source ?>"
                                      data-index="<?= $record->index() ?>">delete</span>
                            </span>
                            </div>
                        </div>
                        <?php
                    }
                }
            }

            if (!$has_entries) {
                echo '<div class="cntnd_alert cntnd_alert-primary">Keine neuen Einträge vorhanden.</div>';
            }
            ?>
            <h3>Mappings</h3>
            <?php
            if (count($mapping_count) > 0) {
                echo '<div class="cntnd_alert cntnd_alert-danger">Für folgende Felder gibt es offene Mappings:<ul>';
                foreach ($mapping_count as $mapping => $type) {
                    echo '<li>' . $mapping . '</li>';
                }
                echo '</ul></div>';
                // todo sources
            } else {
                echo '<div class="cntnd_alert cntnd_alert-primary">Keine offenen Mappings vorhanden.</div>';
            }
            ?>
        </div>

        <div class="tabs__content--pane fade <?= ($count_sources == 0) ? "active" : "" ?>"
             id="contacts__content--contacts">
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

        <div class="tabs__content--pane fade" id="contacts__content--mappings">
            <h2>Mappings</h2>
            <form name="mapping_form" id="mapping_form" method="post">
                <div class="card card--list">
                    <?php
                    foreach ($contacts->data_types() as $header => $type) {
                        ?>
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <strong class="w-25"><?= $header ?></strong>
                            <span class="w-auto">
                            <?= $contacts->make_select_assoc(
                                ["string" => "Text", "integer" => "Zahl", "date" => "Datum", "boolean" => "Checkbox", "float" => "Gleitkommazahl"],
                                "data[type][$header]",
                                "Typ",
                                $type) ?>
                            <?= $contacts->make_checkbox("data[required][$header]", "Muss-Feld", false, in_array($header, $required)) ?>
                        </span>
                            <?php
                            foreach ($contacts->sources_headers() as $key => $sources) {
                                $value = "";
                                if (array_key_exists($header, $mappings[$key])) {
                                    $value = $mappings[$key][$header];
                                }
                                echo '<span class="w-auto">' . $contacts->make_select($sources, "data[mappings][$key][$header]", $key, $value) . '</span>';
                            }
                            ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="action">
                    <input type="hidden" name="mapping_form_action" value="update"/>
                    <button class="btn btn-primary" type="submit">Speichern</button>
                    <button class="btn btn-light" type="reset">Zurücksetzen</button>
                </div>
            </form>
        </div>

        <div class="tabs__content--pane fade" id="contacts__content--history">
            <h2>History</h2>
            <table class="table">
                <thead>
                <tr>
                    <th>Datei</th>
                    <th>Datum</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $files = $contacts->history();
                foreach ($files as $filename => $file) {
                    if (is_file($file)) {
                        $download_file = str_replace(__DIR__, "", $file);
                        echo "<tr>";
                        echo '<td><a href="' . $download_file . '" target="_blank">' . $filename . '</a></td>';
                        echo '<td>' . date("d.m.Y H:i:s", filectime($file)) . '</td>';
                        echo "</tr>\n";
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div id="editor" class="overlay">
    <div class="popup">
        <h2>Editor</h2>
        <a class="close" href="#">&times;</a>
        <form name="editor_form" id="editor_form" method="post">
            <div class="content">
                <div class="d-flex">
                    <?php
                    foreach ($contacts->data_types() as $header => $type) {
                        if ($type === "boolean") {
                            echo $contacts->make_checkbox("data[" . $header . "]", $header, in_array($header, $required));
                        } else {
                            $input_type = ($type === "integer" || $type === "float") ? "number" : "text";
                            echo $contacts->make_input("data[" . $header . "]", $header, $input_type, in_array($header, $required));
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="action">
                <input type="hidden" name="editor_form_action" value="new"/>
                <input type="hidden" name="editor_form_source"/>
                <input type="hidden" name="editor_form_index"/>
                <button class="btn btn-primary" type="submit">Speichern</button>
                <button class="btn btn-light" type="reset">Zurücksetzen</button>
                <button class="btn btn-dark right editor_form_remove" type="button">Löschen</button>
            </div>
        </form>
    </div>
</div>
<div id="delete" style="visibility: hidden;">
    <form name="delete_form" id="delete_form" method="post">
        <input type="hidden" name="editor_form_action" value="delete"/>
        <input type="hidden" name="editor_form_source"/>
        <input type="hidden" name="editor_form_index"/>
        <input type="hidden" name="editor_form_delete"/>
    </form>
</div>
<div id="update" style="visibility: hidden;">
    <form name="addresses_form" id="addresses_form" method="post">
        <input type="hidden" name="addresses_form_action" value="dump"/>
        <input type="hidden" name="addresses_form_data"/>
    </form>
</div>

</body>

</html>