<?php

namespace App\Data\Uic918;

use Carbon\CarbonImmutable;

class MainRecord extends Record
{
    public CarbonImmutable $editionTime;

    public bool $international;

    public bool $editedByAgent;

    public bool $specimen;

    public function __construct(
        public string $companyCode,
        public string $ticketKey,
        string $editionTime,
        int $flags,
        public string $editionLanguage,
        public ?string $secondLanguage,
    ) {
        $this->editionTime = CarbonImmutable::createFromFormat('dmYHi', $editionTime);

        $this->international = (bool) $flags & 0b001;
        $this->editedByAgent = (bool) $flags & 0b010;
        $this->specimen = (bool) $flags & 0b100;
    }
}
