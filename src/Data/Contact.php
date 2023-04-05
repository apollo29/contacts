<?php

namespace Contacts\Data;

use Selective\ArrayReader\ArrayReader;

class Contact implements Data
{
    public string $name;
    public string $vorname;
    public string $strasse;
    public string $plz;
    public string $ort;
    public string $telefon;
    public string $email;
    public string $geburtstag;

    public bool $infomail_spontan;
    public bool $newsletter;

    public bool $freunde;
    public bool $kollegen;
    public bool $nachbarn;
    public bool $blwl;
    public bool $bergsportunternehmen;
    public bool $geschaeftskollegen;
    public bool $dienstleister;
    public bool $basket;
    public bool $mpa;
    public bool $sac_birehubel;

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
        return (array)$this;
    }

    public static function of(array $data, bool $stripslashes = false): Contact
    {
        return new Contact($data, $stripslashes);
    }

    public static function from(array $data, bool $stripslashes = false): Contact
    {
        $mapping = [
            'name' => 'Nachname',
            'vorname' => 'Vorname',
            'strasse' => 'Strasse',
            'plz' => 'PLZ',
            'ort' => 'Ort',
            'telefon' => 'Telefon',
            'email' => 'E-Mail-Adresse',
            'geburtstag' => 'Geburtstag',
            'infomail_spontan' => 'Infomail Spontan',
            'newsletter' => 'Newsletter',
            'freunde' => 'Freunde',
            'kollegen' => 'Kollegen',
            'nachbarn' => 'Nachbarn',
            'blwl' => 'BLWL',
            'bergsportunternehmen' => 'Bergsportunternehmen',
            'geschaeftskollegen' => 'Geschäftskollegen',
            'dienstleister' => 'Dienstleister',
            'basket' => 'Basket',
            'mpa' => 'mpa',
            'sac_birehubel' => 'SAC Birehubel'
        ];
        $array = array();
        foreach ($mapping as $key => $value) {
            $array[$key] = $data[$value];
        }
        return Contact::of($array, $stripslashes);
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