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

    public function render(): string
    {
        $lines = [];

        foreach ($this->fields as $field) {
            $row = $field->line;
            $column = $field->column;
            $length = strlen($field->text);

            // Make sure we have text
            $lines[$row] = $lines[$row] ?? str_pad('', 72);

            $lines[$row] = substr($lines[$row], 0, $column).$field->text.substr($lines[$row], $column + $length);
        }

        $text = '';
        $max = max(array_keys($lines));
        for ($i = 0; $i <= $max; $i++) {
            $text .= ($lines[$i] ?? '')."\n";
        }

        return $text;
    }
}
