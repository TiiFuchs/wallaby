<?php

namespace App\Data\UIC918\VURecord;

use App\Data\UIC918\Record;

class VURecord extends Record
{
    public int $terminalNr;

    public int $samNr;

    public int $anzahlPersonen;

    /** @var array<int, Efs> */
    public array $efsListe;
}
