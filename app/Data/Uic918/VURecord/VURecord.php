<?php

namespace App\Data\Uic918\VURecord;

use App\Data\Uic918\Record;

class VURecord extends Record
{
    public int $terminalNr;

    public int $samNr;

    public int $anzahlPersonen;

    /** @var array<int, Efs> */
    public array $efsListe;
}
