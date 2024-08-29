<?php

namespace App\Services\UIC918\Parser;

use App\Data\UIC918\TicketLayout\Field;
use App\Data\UIC918\TicketLayout\TicketLayout;
use App\Exceptions\UIC918\InvalidDataException;
use App\Utils\ByteReader;

class TicketLayoutParser extends Parser
{
    public function parse(string $rawData): TicketLayout
    {
        $data = new ByteReader($rawData);

        $layout = $data->unpack('A4');

        $ticketLayout = new TicketLayout($layout);

        $numberOfFields = (int) $data->next(4);

        while (! $data->eof() || $numberOfFields > 0) {

            $ticketLayout->addField(Field::from([
                'line' => (int) $data->next(2),
                'column' => (int) $data->next(2),
                'height' => (int) $data->next(2),
                'width' => (int) $data->next(2),
                'formatting' => (int) $data->next(1),
                'length' => $textLength = (int) $data->next(4),
                'text' => $data->unpack('A'.$textLength),
            ]));
            $numberOfFields--;

        }

        if ($numberOfFields !== 0) {
            throw new InvalidDataException('Invalid number of fields in U_TLAY section.');
        }

        return $ticketLayout;
    }
}
