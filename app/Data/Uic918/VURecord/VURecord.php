<?php

namespace App\Data\Uic918\VURecord;

use App\Data\Uic918\Record;
use Illuminate\Support\Collection;

class VURecord extends Record
{
    public int $terminalNr;

    public int $samNr;

    public int $anzahlPersonen;

    /** @var Collection<int, Efs> */
    public Collection $efsListe;
}
