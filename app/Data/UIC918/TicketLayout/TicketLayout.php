<?php

namespace App\Data\UIC918\TicketLayout;

use App\Data\UIC918\Record;

class TicketLayout extends Record
{
    public int $numberOfFields = 0;

    /** @var array<int, Field> */
    public array $fields;

    public function __construct(
        public string $layout
    ) {
        //
    }

    public function addField(Field $field): void
    {
        $this->fields[] = $field;
        $this->numberOfFields++;
    }
}
