<?php

namespace Contacts\Data;

use Selective\ArrayReader\ArrayReader;

class Test implements Data
{
    private array $record;

    /**
     * The constructor.
     *
     * @param array $data The data
     */
    public function __construct(array $data = [], bool $stripslashes = false)
    {
        if ($stripslashes) {
            $data = Mapping::stripslashes($data);
        }

        $reader = new ArrayReader($data);

        $this->name = $reader->findString('name', Mapping::$default_string);
        $this->vorname = $reader->findString('vorname', Mapping::$default_string);
        $this->strasse = $reader->findString('strasse', Mapping::$default_string);
        $this->plz = $reader->findString('plz', Mapping::$default_string);
        $this->ort = $reader->findString('ort', Mapping::$default_string);
        $this->telefon = $reader->findString('telefon', Mapping::$default_string);
        $this->email = $reader->findString('email', Mapping::$default_string);
        $this->geburtstag = $reader->findString('geburtstag', Mapping::$default_string);

        $this->infomail_spontan = $reader->findBool('infomail_spontan', Mapping::$default_bool);
        $this->newsletter = $reader->findBool('newsletter', Mapping::$default_bool);

        $this->freunde = $reader->findBool('freunde', Mapping::$default_bool);
        $this->kollegen = $reader->findBool('kollegen', Mapping::$default_bool);
        $this->nachbarn = $reader->findBool('nachbarn', Mapping::$default_bool);
        $this->blwl = $reader->findBool('blwl', Mapping::$default_bool);
        $this->bergsportunternehmen = $reader->findBool('bergsportunternehmen', Mapping::$default_bool);
        $this->geschaeftskollegen = $reader->findBool('geschaeftskollegen', Mapping::$default_bool);
        $this->dienstleister = $reader->findBool('dienstleister', Mapping::$default_bool);
        $this->basket = $reader->findBool('basket', Mapping::$default_bool);
        $this->mpa = $reader->findBool('mpa', Mapping::$default_bool);
        $this->sac_birehubel = $reader->findBool('sac_birehubel', Mapping::$default_bool);
    }

    public function record(): array
    {
        return $this->record;
    }

    public static function of(array $data, bool $stripslashes = false): Test
    {
        return new Test($data, $stripslashes);
    }

    public function timestamp(): ?string
    {
        return null;
    }

    public function identifier(): string
    {
        return $this->name . " " . $this->vorname;
    }

    public function index(): string
    {
        return $this->email;
    }
}