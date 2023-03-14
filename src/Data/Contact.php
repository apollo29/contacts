<?php

namespace Contacts\Data;

use Selective\ArrayReader\ArrayReader;

class Contact implements Data
{
    public string $vorname;
    public string $name;
    public string $strasse;
    public string $plz;
    public string $ort;
    public string $land;
    public string $telefon_geschaeftlich;
    public string $telefon;
    public string $mobile;
    public string $email;
    public string $email_2;

    public bool $infomail_spontan;
    public bool $newsletter;

    public bool $familie;
    public bool $freunde;
    public bool $kollegen;
    public bool $nachbarn;
    public bool $wanderleiter;
    public bool $bergsportunternehmen;
    public bool $geschaeftskollegen;
    public bool $dienstleister;
    public bool $linkedin;
    public bool $unternehmen;
    public bool $organisationen;

    /**
     * The constructor.
     *
     * @param array $data The data
     */
    public function __construct(array $data = [])
    {
        $reader = new ArrayReader($data);

        $this->vorname = $reader->findString('vorname', Mapping::$default_string);
        $this->name = $reader->findString('name', Mapping::$default_string);
        $this->strasse = $reader->findString('strasse', Mapping::$default_string);
        $this->plz = $reader->findString('plz', Mapping::$default_string);
        $this->ort = $reader->findString('ort', Mapping::$default_string);
        $this->land = $reader->findString('land', Mapping::$default_string);
        $this->telefon_geschaeftlich = $reader->findString('telefon_geschaeftlich', Mapping::$default_string);
        $this->telefon = $reader->findString('telefon', Mapping::$default_string);
        $this->mobile = $reader->findString('mobile', Mapping::$default_string);
        $this->email = $reader->findString('email');
        $this->email_2 = $reader->findString('email_2', Mapping::$default_string);

        $this->infomail_spontan = $reader->findBool('infomail_spontan', Mapping::$default_bool);
        $this->newsletter = $reader->findBool('newsletter', Mapping::$default_bool);

        $this->familie = $reader->findBool('familie', Mapping::$default_bool);
        $this->freunde = $reader->findBool('freunde', Mapping::$default_bool);
        $this->kollegen = $reader->findBool('kollegen', Mapping::$default_bool);
        $this->nachbarn = $reader->findBool('nachbarn', Mapping::$default_bool);
        $this->wanderleiter = $reader->findBool('wanderleiter', Mapping::$default_bool);
        $this->bergsportunternehmen = $reader->findBool('bergsportunternehmen', Mapping::$default_bool);
        $this->geschaeftskollegen = $reader->findBool('geschaeftskollegen', Mapping::$default_bool);
        $this->dienstleister = $reader->findBool('dienstleister', Mapping::$default_bool);
        $this->linkedin = $reader->findBool('linkedin', Mapping::$default_bool);
        $this->unternehmen = $reader->findBool('unternehmen', Mapping::$default_bool);
        $this->organisationen = $reader->findBool('organisationen', Mapping::$default_bool);
    }

    public function record(): array
    {
        return (array)$this;
    }

    public static function of(array $data, bool $stripslashes = false): Data
    {
        if ($stripslashes) {
            $data = Mapping::stripslashes($data);
        }
        return new Contact($data);
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