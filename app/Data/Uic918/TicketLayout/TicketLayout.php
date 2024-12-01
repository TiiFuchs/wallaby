<?php

namespace App\Data\Uic918\TicketLayout;

use App\Data\Uic918\Record;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class TicketLayout extends Record
{
    public int $numberOfFields = 0;

    /** @var Collection<int, Field> */
    public Collection $fields;

    public function __construct(
        public string $layout
    ) {
        $this->fields = new Collection;
    }

    public function addField(Field $field): void
    {
        $this->fields->add($field);
        $this->numberOfFields++;
    }

    protected function getAreaText(int $fromRow, int $toRow, int $fromColumn, int $toColumn): string
    {
        $lines = [];

        foreach ($this->fields as $field) {
            if ($field->line >= $fromRow && $field->line <= $toRow
                && $field->column >= $fromColumn && $field->column <= $toColumn) {

                if (! isset($lines[$field->line])) {
                    $lines[$field->line] = str_pad('', 72, ' ');
                }

                $lines[$field->line] = substr($lines[$field->line], 0, $field->column).$field->text.substr($lines[$field->line], $field->column + strlen($field->text));

            }
        }

        return collect($lines)
            ->sortKeys()
            ->map(fn ($text) => trim(preg_replace('/(\s)+/', '$1', $text)))
            ->implode("\n");
    }

    public function name(): string
    {
        return $this->getAreaText(0, 0, 52, 70);
    }

    public function tariffDesc(): string
    {
        return $this->getAreaText(1, 1, 1, 50);
    }

    public function class(): int
    {
        return (int) $this->getAreaText(6, 6, 66, 70);
    }

    public function validFrom(): CarbonImmutable
    {
        $dateText = $this->getAreaText(6, 6, 1, 62);
        preg_match_all('/\d{2}\.\d{2} \d{2}:\d{2}/', $dateText, $matches);

        return CarbonImmutable::createFromFormat('d.m H:i', $matches[0][0]);
    }

    public function validUntil(): CarbonImmutable
    {
        $dateText = $this->getAreaText(6, 6, 1, 62);
        preg_match_all('/\d{2}\.\d{2} \d{2}:\d{2}/', $dateText, $matches);

        $validFrom = CarbonImmutable::createFromFormat('d.m H:i', $matches[0][0]);
        $validUntil = CarbonImmutable::createFromFormat('d.m H:i', $matches[0][1]);

        if ($validUntil->isBefore($validFrom)) {
            $validUntil = $validUntil->addYear();
        }

        return $validUntil;
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
